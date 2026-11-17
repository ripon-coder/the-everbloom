<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase20DashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_dashboard_renders_real_aggregates_flash_sale_analytics_and_notifications(): void
    {
        $admin = Admin::firstOrFail();
        $user = User::firstOrFail();
        $product = Product::where('is_active', true)->firstOrFail();
        $product->update(['stock_count' => 2, 'min_threshold' => 5]);

        $sale = FlashSale::create([
            'title' => 'Dash Flash',
            'slug' => 'dash-flash',
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHour(),
            'status' => FlashSale::STATUS_ACTIVE,
        ]);

        $fsp = FlashSaleProduct::create([
            'flash_sale_id' => $sale->id,
            'product_id' => $product->id,
            'sale_price' => 50,
            'sale_stock' => 10,
            'sold_qty' => 2,
            'is_active' => true,
        ]);

        $order = Order::create([
            'order_number' => 'EVB-DASH-1',
            'user_id' => $user->id,
            'customer_email' => $user->email,
            'customer_phone' => '01711223344',
            'status' => 'Placed',
            'current_step' => 'Placed',
            'subtotal' => 100,
            'total' => 100,
            'items_count' => 2,
            'delivery_method' => 'Standard Delivery',
            'payment_method' => 'Cash on Delivery (Demo)',
            'payment_status' => 'Paid',
            'shipping_address' => ['first_name' => 'A', 'last_name' => 'B', 'city' => 'Banani', 'state' => 'Dhaka', 'district_name' => 'Dhaka'],
            'placed_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'flash_sale_id' => $sale->id,
            'flash_sale_product_id' => $fsp->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'qty' => 2,
            'price' => 50,
            'line_total' => 100,
        ]);

        $res = $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard', ['period' => 'today']))
            ->assertOk()
            ->assertSee('Paid Revenue')
            ->assertSee('Active Flash Sales')
            ->assertSee('Low Stock Watchlist')
            ->assertSee('Pending Order #EVB-DASH-1')
            ->assertDontSee('Michael B. reviewed');

        $this->actingAs($admin, 'admin')
            ->getJson(route('admin.dashboard.chart', ['period' => '1m']))
            ->assertOk()
            ->assertJsonStructure(['labels', 'revenue', 'orders']);
    }
}

