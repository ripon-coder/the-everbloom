<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase15ReviewsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_review_defaults_to_pending_checks_verified_buyer_and_blocks_duplicates(): void
    {
        $product = Product::where('is_active', true)->firstOrFail();
        $buyer = User::factory()->create(['status' => 'Active']);
        $nonBuyer = User::factory()->create(['status' => 'Active']);

        // Create a real non-cancelled order for $buyer containing $product
        $order = Order::create([
            'order_number' => 'EVB-REV-1001',
            'user_id' => $buyer->id,
            'customer_email' => $buyer->email,
            'customer_phone' => '01711223344',
            'status' => 'Delivered',
            'current_step' => 'Delivered',
            'subtotal' => 100,
            'total' => 100,
            'items_count' => 1,
            'delivery_method' => 'Standard Delivery',
            'payment_method' => 'Cash on Delivery (Demo)',
            'payment_status' => 'Paid',
            'shipping_address' => ['first_name' => 'Test'],
            'placed_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'qty' => 1,
            'price' => 100,
            'line_total' => 100,
        ]);

        // Non-buyer submits review -> Pending & verified = false
        $this->actingAs($nonBuyer, 'web')
            ->postJson(route('shop.product.reviews.store', $product->slug), [
                'author' => 'Non Buyer',
                'rating' => 4,
                'headline' => 'Nice texture',
                'body' => 'Very gentle cleanser, absorbs nicely into skin.',
            ])
            ->assertCreated();

        $nonBuyerReview = Review::where('user_id', $nonBuyer->id)->firstOrFail();
        $this->assertEquals(Review::STATUS_PENDING, $nonBuyerReview->status);
        $this->assertFalse($nonBuyerReview->verified);

        // Duplicate submission by same user -> 422
        $this->actingAs($nonBuyer, 'web')
            ->postJson(route('shop.product.reviews.store', $product->slug), [
                'author' => 'Non Buyer',
                'rating' => 5,
                'body' => 'Trying to submit a second review for the same product.',
            ])
            ->assertStatus(422);

        // Buyer submits review -> Pending & verified = true
        $this->actingAs($buyer, 'web')
            ->postJson(route('shop.product.reviews.store', $product->slug), [
                'author' => 'Verified Buyer',
                'rating' => 5,
                'headline' => 'Love it',
                'body' => 'Bought this last week and my skin feels much brighter.',
            ])
            ->assertCreated();

        $buyerReview = Review::where('user_id', $buyer->id)->firstOrFail();
        $this->assertEquals(Review::STATUS_PENDING, $buyerReview->status);
        $this->assertTrue($buyerReview->verified);
    }

    public function test_admin_can_filter_approve_reject_feature_reply_bulk_and_delete_reviews(): void
    {
        $admin = Admin::firstOrFail();
        $product = Product::where('is_active', true)->firstOrFail();
        Review::where('product_id', $product->id)->delete();

        $r1 = Review::create([
            'product_id' => $product->id,
            'author' => 'Alice',
            'rating' => 5,
            'headline' => 'Amazing serum',
            'body' => 'Super hydrating and gentle on sensitive skin.',
            'verified' => true,
        ]);

        // DB default must be Pending
        $this->assertEquals(Review::STATUS_PENDING, $r1->fresh()->status);

        $r2 = Review::create([
            'product_id' => $product->id,
            'author' => 'Bob',
            'rating' => 3,
            'headline' => 'Okay',
            'body' => 'Decent moisturizer but a bit light for winter.',
            'verified' => false,
        ]);

        // Approve r1
        $this->actingAs($admin, 'admin')
            ->post(route('admin.reviews.approve', $r1->id))
            ->assertRedirect();

        $this->assertEquals(Review::STATUS_APPROVED, $r1->fresh()->status);
        $this->assertEquals(1, $product->fresh()->review_count);
        $this->assertEquals(5.0, (float) $product->fresh()->rating);

        // Toggle featured & add reply
        $this->actingAs($admin, 'admin')
            ->post(route('admin.reviews.featured', $r1->id))
            ->assertRedirect();
        $this->assertTrue($r1->fresh()->is_featured_testimonial);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.reviews.reply', $r1->id), ['admin_reply' => 'Thank you Alice!'])
            ->assertRedirect();
        $this->assertEquals('Thank you Alice!', $r1->fresh()->admin_reply);

        // Reject r2
        $this->actingAs($admin, 'admin')
            ->post(route('admin.reviews.reject', $r2->id))
            ->assertRedirect();
        $this->assertEquals(Review::STATUS_REJECTED, $r2->fresh()->status);

        // Filter & search
        $this->actingAs($admin, 'admin')
            ->get(route('admin.reviews.index', ['status' => 'Approved', 'search' => 'Alice']))
            ->assertOk()
            ->assertSee('Alice')
            ->assertDontSee('Bob');

        // Storefront product page shows approved r1 and real breakdown, not rejected r2
        $this->get(route('shop.product', $product->slug))
            ->assertOk()
            ->assertSee('Amazing serum')
            ->assertDontSee('Decent moisturizer');

        // Bulk reject r1 -> product count back to 0
        $this->actingAs($admin, 'admin')
            ->post(route('admin.reviews.bulk'), [
                'action' => 'reject',
                'ids' => [$r1->id],
            ])
            ->assertRedirect();
        $this->assertEquals(0, $product->fresh()->review_count);

        // Delete r2
        $this->actingAs($admin, 'admin')
            ->delete(route('admin.reviews.destroy', $r2->id))
            ->assertRedirect();
        $this->assertDatabaseMissing('reviews', ['id' => $r2->id]);
    }
}

