<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmationMail;
use App\Models\Area;
use App\Models\District;
use App\Models\Order;
use App\Models\Product;
use App\Models\Thana;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class Phase10CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_rejects_inactive_product_and_invalid_variant_and_queues_confirmation(): void
    {
        Mail::fake();
        $this->seed();

        $user = User::where('email', 'eleanor.vance@example.com')->firstOrFail();
        $product = Product::with('productVariants')->where('is_active', true)->firstOrFail();
        $variant = $product->productVariants->firstOrFail();

        $dhaka = District::where('slug', 'dhaka')->firstOrFail();
        $gulshan = Thana::where('district_id', $dhaka->id)->where('name', 'Gulshan')->firstOrFail();
        $gulshan2 = Area::where('thana_id', $gulshan->id)->where('name', 'Gulshan 2')->firstOrFail();

        // Invalid variantId rejected (no silent fallback)
        $this->actingAs($user, 'web')->postJson('/checkout', [
            'email' => $user->email,
            'phone' => '+8801711000001',
            'firstName' => 'Eleanor',
            'lastName' => 'Vance',
            'district_id' => $dhaka->id,
            'thana_id' => $gulshan->id,
            'area_id' => $gulshan2->id,
            'address' => 'House 42',
            'shippingMethod' => 'standard',
            'paymentMethod' => 'cod_demo',
            'items' => [
                ['productId' => $product->id, 'variantId' => 'NON-EXISTENT-SIZE', 'qty' => 1],
            ],
        ])->assertStatus(422)->assertJsonValidationErrors('items');

        // Inactive product rejected
        $product->update(['is_active' => false]);
        $this->actingAs($user, 'web')->postJson('/checkout', [
            'email' => $user->email,
            'phone' => '+8801711000001',
            'firstName' => 'Eleanor',
            'lastName' => 'Vance',
            'district_id' => $dhaka->id,
            'thana_id' => $gulshan->id,
            'area_id' => $gulshan2->id,
            'address' => 'House 42',
            'shippingMethod' => 'standard',
            'paymentMethod' => 'cod_demo',
            'items' => [
                ['productId' => $product->id, 'variantId' => $variant->size, 'qty' => 1],
            ],
        ])->assertStatus(422)->assertJsonValidationErrors('items');

        // Re-activate product and place valid order with saveAddress=true and coupon
        $product->update(['is_active' => true]);
        $initialAddresses = $user->addresses()->count();

        $res = $this->actingAs($user, 'web')->postJson('/checkout', [
            'email' => $user->email,
            'phone' => '+8801711999888',
            'firstName' => 'Eleanor',
            'lastName' => 'Vance',
            'district_id' => $dhaka->id,
            'thana_id' => $gulshan->id,
            'area_id' => $gulshan2->id,
            'address' => 'House 77, Road 12',
            'saveAddress' => true,
            'shippingMethod' => 'standard',
            'paymentMethod' => 'cod_demo',
            'discountCode' => 'WELCOME10',
            'items' => [
                ['productId' => $product->id, 'variantId' => $variant->size, 'qty' => 1],
            ],
        ])->assertOk();

        $orderNumber = $res->json('order_number');
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        $this->assertSame('Pending', $order->payment_status);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'to_status' => 'Placed',
        ]);
        $this->assertDatabaseHas('coupon_redemptions', [
            'order_id' => $order->id,
            'user_id' => $user->id,
        ]);
        $this->assertSame($initialAddresses + 1, $user->addresses()->count());

        Mail::assertQueued(OrderConfirmationMail::class, fn ($mail) => $mail->order->id === $order->id);

        // Confirmation page renders for owner and blocks other users
        $this->actingAs($user, 'web')
            ->get(route('checkout.confirmation', $orderNumber))
            ->assertOk()
            ->assertSee($orderNumber);

        $other = User::where('email', 'sophia.laurent@example.com')->firstOrFail();
        $this->actingAs($other, 'web')
            ->get(route('checkout.confirmation', $orderNumber))
            ->assertForbidden();
    }

    public function test_checkout_page_is_wired_to_post_checkout_endpoint(): void
    {
        $this->seed();

        $this->get('/checkout')
            ->assertOk()
            ->assertSee('validate-coupon', false)
            ->assertSee('saveAddress: Boolean(this.form.saveAddress)', false);
    }
}
