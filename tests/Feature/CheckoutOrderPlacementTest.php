<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Coupon;
use App\Models\District;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Thana;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutOrderPlacementTest extends TestCase
{
    use RefreshDatabase;

    public function test_coupon_validation_endpoint_works(): void
    {
        $this->seed();

        $this->postJson('/checkout/validate-coupon', [
            'code' => 'WELCOME10',
            'subtotal' => 100,
        ])
            ->assertOk()
            ->assertJson([
                'valid' => true,
                'code' => 'WELCOME10',
                'type' => 'percentage',
                'value' => 10,
            ]);

        $this->postJson('/checkout/validate-coupon', [
            'code' => 'INVALIDCODE',
            'subtotal' => 100,
        ])->assertStatus(422);
    }

    public function test_checkout_creates_order_items_invoice_and_updates_stock_and_coupon(): void
    {
        $this->seed();

        $user = User::where('email', 'eleanor.vance@example.com')->firstOrFail();
        $p1 = Product::with('productVariants')->findOrFail(1);
        $p2 = Product::with('productVariants')->findOrFail(2);

        $p1InitialStock = $p1->stock_count;
        $p1VariantInitialStock = $p1->productVariants->first()->stock_count;
        $p2InitialStock = $p2->stock_count;
        $couponInitialUsed = Coupon::where('code', 'WELCOME10')->value('used_count');

        $dhaka = District::where('slug', 'dhaka')->firstOrFail();
        $gulshan = Thana::where('district_id', $dhaka->id)->where('name', 'Gulshan')->firstOrFail();
        $gulshan2 = Area::where('thana_id', $gulshan->id)->where('name', 'Gulshan 2')->firstOrFail();

        $response = $this->actingAs($user, 'web')->postJson('/checkout', [
            'email' => $user->email,
            'phone' => '+880 1711-000192',
            'firstName' => 'Eleanor',
            'lastName' => 'Vance',
            'district_id' => $dhaka->id,
            'thana_id' => $gulshan->id,
            'area_id' => $gulshan2->id,
            'address' => 'House 42, Road 71',
            'apartment' => 'Suite 4B',
            'zip' => '1212',
            'shippingMethod' => 'standard',
            'paymentMethod' => 'card_demo',
            'discountCode' => 'WELCOME10',
            'items' => [
                ['productId' => $p1->id, 'variantId' => $p1->productVariants->first()->size, 'qty' => 2],
                ['productId' => $p2->id, 'variantId' => $p2->productVariants->first()->size, 'qty' => 1],
            ],
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $orderNumber = $response->json('order_number');
        $this->assertNotEmpty($orderNumber);

        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $this->assertSame($user->id, $order->user_id);
        $this->assertSame('WELCOME10', $order->discount_code);
        $this->assertSame(2, OrderItem::where('order_id', $order->id)->count());
        $this->assertTrue(Invoice::where('order_id', $order->id)->exists());

        $p1->refresh();
        $p2->refresh();
        $this->assertSame($p1InitialStock - 2, $p1->stock_count);
        $this->assertSame($p1VariantInitialStock - 2, $p1->productVariants->first()->stock_count);
        $this->assertSame($p2InitialStock - 1, $p2->stock_count);
        $this->assertSame($couponInitialUsed + 1, Coupon::where('code', 'WELCOME10')->value('used_count'));
    }
}
