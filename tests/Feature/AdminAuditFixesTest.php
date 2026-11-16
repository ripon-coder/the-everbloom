<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminAuditFixesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_topbar_has_post_logout_and_categories_form_has_multipart(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();

        $this->actingAs($admin, 'admin')->get('/admin')
            ->assertOk()
            ->assertSee('action="' . route('admin.logout') . '"', false)
            ->assertSee($admin->name);

        $this->actingAs($admin, 'admin')->get('/admin/categories/create')
            ->assertOk()
            ->assertSee('enctype="multipart/form-data"', false);
    }

    public function test_order_cancellation_restores_stock_and_shows_status_inputs(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();

        $order = Order::with('items')->where('order_number', 'EVB-9204')->firstOrFail();
        $firstItem = $order->items->first();
        $product = Product::findOrFail($firstItem->product_id);
        $stockBefore = $product->stock_count;

        $this->actingAs($admin, 'admin')->get('/admin/orders/EVB-9204')
            ->assertOk()
            ->assertSee('name="status"', false)
            ->assertSee('name="tracking_number"', false);

        $this->actingAs($admin, 'admin')->post('/admin/orders/EVB-9204/status', [
            'status' => 'Cancelled',
            'tracking_number' => 'CANCELLED',
        ])->assertRedirect();

        $this->assertSame('Cancelled', $order->fresh()->status);
        $this->assertSame($stockBefore + $firstItem->qty, $product->fresh()->stock_count);
    }

    public function test_coupon_delete_and_zero_limit_do_not_error(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();

        $coupon = Coupon::create([
            'code' => 'UNLIMITED5',
            'type' => 'percentage',
            'value' => 5,
            'label' => '5% Unlimited',
            'usage_limit' => null,
            'is_active' => true,
        ]);

        $this->actingAs($admin, 'admin')->get('/admin/coupons')
            ->assertOk()
            ->assertSee('UNLIMITED5');

        $this->actingAs($admin, 'admin')->delete("/admin/coupons/{$coupon->id}")
            ->assertRedirect('/admin/coupons');

        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }

    public function test_product_pagination_inventory_adjust_and_variant_stock_sync(): void
    {
        $this->seed();
        Storage::fake('s3');
        config(['filesystems.cloud' => 's3']);

        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();

        // Pagination page 2 shows remaining seeded products (16 total, 12 per page)
        $this->actingAs($admin, 'admin')->get('/admin/products?page=2')
            ->assertOk()
            ->assertSee('Showing 13 to 16 of 16 formulations');

        // Inventory adjustment works
        $product = Product::findOrFail(1);
        $this->actingAs($admin, 'admin')->post("/admin/inventory/{$product->id}/adjust", [
            'stock_count' => 99,
            'min_threshold' => 12,
        ])->assertRedirect();
        $this->assertSame(99, $product->fresh()->stock_count);

        // Multi-variant product sums stock_count and deletes variant cloud image on destroy
        $vImg = UploadedFile::fake()->image('v1.png', 300, 300);
        $this->actingAs($admin, 'admin')->post('/admin/products', [
            'name' => 'Audit Variant Sum Product',
            'category_slug' => 'serums',
            'price' => 50.00,
            'variants' => [
                ['attributes' => ['Size' => '30ml / 1.0 fl oz'], 'price' => 50.00, 'stock_count' => 7],
                ['attributes' => ['Size' => '50ml / 1.7 fl oz'], 'price' => 80.00, 'stock_count' => 8],
            ],
            'variant_images' => [0 => $vImg],
        ])->assertRedirect('/admin/products');

        $created = Product::with('productVariants')->where('name', 'Audit Variant Sum Product')->firstOrFail();
        $this->assertSame(15, $created->stock_count);
        $v1Path = $created->productVariants[0]->image;
        Storage::disk('s3')->assertExists($v1Path);

        // Cloud URL is not prefixed with /images/ in admin product list
        $this->actingAs($admin, 'admin')->get('/admin/products')
            ->assertOk()
            ->assertDontSee('/images/https://');

        $this->actingAs($admin, 'admin')->delete("/admin/products/{$created->id}")
            ->assertRedirect('/admin/products');
        Storage::disk('s3')->assertMissing($v1Path);
    }
}

