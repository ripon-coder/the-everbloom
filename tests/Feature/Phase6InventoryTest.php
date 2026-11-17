<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Area;
use App\Models\District;
use App\Models\Order;
use App\Models\OrderItemBatch;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\PurchaseBatchItem;
use App\Models\StockMovement;
use App\Models\Thana;
use App\Models\User;
use App\Services\PurchaseBatchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase6InventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_rejects_insufficient_stock_without_clamping_or_creating_order(): void
    {
        $this->seed();
        $user = User::where('email', 'eleanor.vance@example.com')->firstOrFail();
        $product = Product::with('productVariants')->firstOrFail();
        $variant = $product->productVariants->firstOrFail();

        $variant->update(['stock_count' => 2, 'stock_status' => 'low_stock']);
        $product->update(['stock_count' => 2, 'stock_status' => 'low_stock']);

        $dhaka = District::where('slug', 'dhaka')->firstOrFail();
        $gulshan = Thana::where('district_id', $dhaka->id)->where('name', 'Gulshan')->firstOrFail();
        $gulshan2 = Area::where('thana_id', $gulshan->id)->where('name', 'Gulshan 2')->firstOrFail();

        $beforeOrderCount = Order::count();

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
                ['productId' => $product->id, 'variantId' => $variant->size, 'qty' => 5],
            ],
        ])->assertStatus(422)->assertJsonValidationErrors('items');

        $this->assertSame($beforeOrderCount, Order::count());
        $this->assertSame(2, $variant->fresh()->stock_count);
    }

    public function test_fifo_batch_consumption_and_exact_cancel_restore_and_stock_movements(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();
        $user = User::where('email', 'eleanor.vance@example.com')->firstOrFail();
        $product = Product::with('productVariants')->firstOrFail();
        $variant = $product->productVariants->firstOrFail();

        // Reset batches for clean FIFO test across 2 batches
        PurchaseBatchItem::where('product_id', $product->id)->delete();
        $product->update(['stock_count' => 0]);
        $variant->update(['stock_count' => 0]);

        $batchService = app(PurchaseBatchService::class);
        $b1 = $batchService->createBatch([
            'vendor_name' => 'Vendor One',
            'items' => [['product_id' => $product->id, 'product_variant_id' => $variant->id, 'qty' => 3, 'unit_buy_price' => 20]],
        ]);
        $b2 = $batchService->createBatch([
            'vendor_name' => 'Vendor Two',
            'items' => [['product_id' => $product->id, 'product_variant_id' => $variant->id, 'qty' => 5, 'unit_buy_price' => 30]],
        ]);

        $batchItem1 = $b1->items->first();
        $batchItem2 = $b2->items->first();

        $dhaka = District::where('slug', 'dhaka')->firstOrFail();
        $gulshan = Thana::where('district_id', $dhaka->id)->where('name', 'Gulshan')->firstOrFail();
        $gulshan2 = Area::where('thana_id', $gulshan->id)->where('name', 'Gulshan 2')->firstOrFail();

        // Order 4 units: takes 3 from batchItem1 and 1 from batchItem2
        $res = $this->actingAs($user, 'web')->postJson('/checkout', [
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
                ['productId' => $product->id, 'variantId' => $variant->size, 'qty' => 4],
            ],
        ])->assertOk();

        $this->assertSame(0, $batchItem1->fresh()->remaining_qty);
        $this->assertSame(4, $batchItem2->fresh()->remaining_qty);

        $order = Order::with('items')->where('order_number', $res->json('order_number'))->firstOrFail();
        $this->assertSame(2, OrderItemBatch::where('order_item_id', $order->items->first()->id)->count());
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'delta' => -4,
            'reason' => 'order_placed',
        ]);

        // Cancel order restores exact FIFO batch rows
        $this->actingAs($admin, 'admin')
            ->post("/admin/orders/{$order->order_number}/status", ['status' => 'Cancelled'])
            ->assertRedirect();

        $this->assertSame(3, $batchItem1->fresh()->remaining_qty);
        $this->assertSame(5, $batchItem2->fresh()->remaining_qty);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'delta' => 4,
            'reason' => 'order_cancelled',
        ]);
    }

    public function test_variant_level_adjust_syncs_product_stock_and_logs_movement_and_alerts(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();
        $product = Product::with('productVariants')->firstOrFail();
        $variant = $product->productVariants->firstOrFail();

        $otherVariantsSum = (int) $product->productVariants()->where('id', '!=', $variant->id)->sum('stock_count');

        $this->actingAs($admin, 'admin')
            ->post("/admin/inventory/variants/{$variant->id}/adjust", [
                'stock_count' => 3,
                'note' => 'Cycle count correction',
            ])
            ->assertRedirect();

        $this->assertSame(3, $variant->fresh()->stock_count);
        $this->assertSame($otherVariantsSum + 3, $product->fresh()->stock_count);
        $this->assertTrue(StockMovement::where('product_variant_id', $variant->id)->where('reason', 'manual_adjustment')->exists());

        // Inventory page shows Low-Stock Alerts and Recent Stock Movements
        $product->update(['stock_count' => 2, 'min_threshold' => 10, 'stock_status' => 'low_stock']);
        $this->actingAs($admin, 'admin')
            ->get('/admin/inventory')
            ->assertOk()
            ->assertSee('Low-Stock &amp; Out-of-Stock Alerts', false)
            ->assertSee('Recent Stock Movements')
            ->assertSee('Cycle count correction');
    }
}

