<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Area;
use App\Models\District;
use App\Models\Order;
use App\Models\Product;
use App\Models\Thana;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WholesalePurchaseAndOrderProfitTest extends TestCase
{
    use RefreshDatabase;

    public function test_weighted_average_buy_price_uses_available_stock_only_and_order_tracks_profit(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();
        $user = User::where('email', 'eleanor.vance@example.com')->firstOrFail();

        $product = Product::findOrFail(1);
        $product->update([
            'stock_count' => 20,
            'avg_buy_price' => 25.00,
            'price' => 64.00,
        ]);
        $product->productVariants()->update([
            'avg_buy_price' => 25.00,
            'price' => 64.00,
        ]);

        // Record new wholesale batch of 30 units at $35.00 from Pacific Botanicals
        // Expected weighted avg = (20*25 + 30*35) / 50 = 1550 / 50 = 31.00
        $this->actingAs($admin, 'admin')->post('/admin/purchases', [
            'vendor_name' => 'Pacific Botanicals',
            'reference_no' => 'PO-9001',
            'purchased_at' => '2026-11-16',
            'items' => [
                [
                    'product_id' => $product->id,
                    'batch_lot' => 'LOT-PB-01',
                    'qty' => 30,
                    'unit_buy_price' => 35.00,
                ],
            ],
        ])->assertRedirect('/admin/purchases');

        $product->refresh();
        $this->assertSame(50, $product->stock_count);
        $this->assertEquals(31.00, $product->avg_buy_price);
        $this->assertEquals(35.00, $product->last_buy_price);

        // Place an order for 2 units at $64.00
        $dhaka = District::where('slug', 'dhaka')->firstOrFail();
        $gulshan = Thana::where('district_id', $dhaka->id)->where('name', 'Gulshan')->firstOrFail();
        $gulshan2 = Area::where('thana_id', $gulshan->id)->where('name', 'Gulshan 2')->firstOrFail();

        $checkoutRes = $this->actingAs($user, 'web')->postJson('/checkout', [
            'email' => $user->email,
            'firstName' => 'Eleanor',
            'lastName' => 'Vance',
            'district_id' => $dhaka->id,
            'thana_id' => $gulshan->id,
            'area_id' => $gulshan2->id,
            'address' => 'House 42, Road 71',
            'shippingMethod' => 'standard',
            'paymentMethod' => 'card_demo',
            'items' => [
                [
                    'productId' => $product->id,
                    'variantId' => $product->productVariants->first()->size,
                    'qty' => 2,
                ],
            ],
        ])->assertOk();

        $order = Order::with('items')->where('order_number', $checkoutRes->json('order_number'))->firstOrFail();
        $item = $order->items->first();

        $this->assertEquals(31.00, $item->unit_buy_price);
        $this->assertEquals(33.00, $item->unit_profit);
        $this->assertEquals(66.00, $item->line_profit);
        $this->assertEquals(62.00, $order->total_cost);
        $this->assertEquals(66.00, $order->total_profit);

        // Admin views render vendor, batch code, weighted avg, and order profit
        $this->actingAs($admin, 'admin')->get('/admin/purchases')
            ->assertOk()
            ->assertSee('Pacific Botanicals')
            ->assertSee('31.00');

        $this->actingAs($admin, 'admin')->get('/admin/orders')
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('+$66.00');

        $this->actingAs($admin, 'admin')->get("/admin/orders/{$order->order_number}")
            ->assertOk()
            ->assertSee('31.00')
            ->assertSee('+$66.00');
    }
}

