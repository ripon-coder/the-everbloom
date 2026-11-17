<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase3ProductAndCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_edit_filter_and_bulk_update_products_with_full_fields(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();
        $category = Category::firstOrFail();

        Storage::fake('s3');
        config(['filesystems.default' => 's3']);

        $response = $this->actingAs($admin, 'admin')->post('/admin/products', [
            'name' => 'Rosehip Barrier Elixir',
            'brand' => 'Terra Apothecary',
            'subtitle' => 'Cold-Pressed Botanical Oil',
            'category_slug' => $category->slug,
            'sku' => 'EB-P3-1001',
            'price' => 1250,
            'compare_at_price' => 1500,
            'stock_count' => 30,
            'min_threshold' => 8,
            'badge' => 'Limited Batch',
            'is_featured' => '1',
            'best_seller' => '1',
            'is_active' => '1',
            'skin_types' => 'Dry, Sensitive',
            'benefits' => 'Barrier Repair, Glow',
            'key_ingredients' => 'Rosehip, Squalane',
            'meta_title' => 'Rosehip Barrier Elixir | Everbloom SEO',
            'meta_description' => 'Cold-pressed rosehip barrier elixir for dry skin.',
            'images' => [
                UploadedFile::fake()->image('elixir-1.jpg', 600, 600),
            ],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', 'EB-P3-1001')->firstOrFail();
        $this->assertSame('Terra Apothecary', $product->brand);
        $this->assertSame('Cold-Pressed Botanical Oil', $product->subtitle);
        $this->assertSame(8, $product->min_threshold);
        $this->assertTrue($product->best_seller);
        $this->assertTrue($product->is_active);
        $this->assertSame(['Dry', 'Sensitive'], $product->skin_types);
        $this->assertSame(['Barrier Repair', 'Glow'], $product->benefits);
        $this->assertSame(['Rosehip', 'Squalane'], $product->key_ingredients);
        $this->assertCount(1, $product->images);

        // Storefront shows active product + SEO meta
        $this->get("/shop/product/{$product->slug}")
            ->assertOk()
            ->assertSee('Rosehip Barrier Elixir | Everbloom SEO')
            ->assertSee('Cold-pressed rosehip barrier elixir for dry skin.');

        // Update with another image preserves existing image and appends new one
        $this->actingAs($admin, 'admin')->put("/admin/products/{$product->id}", [
            'name' => 'Rosehip Barrier Elixir Updated',
            'category_slug' => $category->slug,
            'price' => 1250,
            'compare_at_price' => 1500,
            'is_active' => '1',
            'images' => [
                UploadedFile::fake()->image('elixir-2.jpg', 600, 600),
            ],
        ])->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertCount(2, $product->images);

        // Remove only the first image
        $firstImage = $product->images[0];
        $this->actingAs($admin, 'admin')->put("/admin/products/{$product->id}", [
            'name' => 'Rosehip Barrier Elixir Updated',
            'category_slug' => $category->slug,
            'price' => 1250,
            'is_active' => '0',
            'remove_images' => [$firstImage],
        ])->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertCount(1, $product->images);
        $this->assertFalse($product->is_active);

        // Inactive product returns 404 on storefront
        $this->get("/shop/product/{$product->slug}")->assertNotFound();

        // Admin index status filter
        $this->actingAs($admin, 'admin')
            ->get('/admin/products?status=inactive')
            ->assertOk()
            ->assertSee('Rosehip Barrier Elixir Updated');

        // Bulk activate restores active state
        $this->actingAs($admin, 'admin')
            ->post('/admin/products/bulk', [
                'action' => 'activate',
                'product_ids' => [$product->id],
            ])
            ->assertRedirect(route('admin.products.index'));

        $this->assertTrue($product->fresh()->is_active);
    }

    public function test_product_validation_enforces_compare_at_price_and_unique_skus(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();
        $category = Category::firstOrFail();
        $existing = Product::firstOrFail();

        // compare_at_price <= price rejected
        $this->actingAs($admin, 'admin')->post('/admin/products', [
            'name' => 'Invalid Price Product',
            'category_slug' => $category->slug,
            'price' => 500,
            'compare_at_price' => 450,
        ])->assertSessionHasErrors('compare_at_price');

        // Duplicate product SKU rejected
        $this->actingAs($admin, 'admin')->post('/admin/products', [
            'name' => 'Duplicate SKU Product',
            'category_slug' => $category->slug,
            'sku' => $existing->sku,
            'price' => 500,
        ])->assertSessionHasErrors('sku');

        // Duplicate variant SKU in submission rejected
        $this->actingAs($admin, 'admin')->post('/admin/products', [
            'name' => 'Duplicate Variant SKU Product',
            'category_slug' => $category->slug,
            'price' => 500,
            'variants' => [
                ['title' => '30ml', 'sku' => 'DUP-VAR-1', 'price' => 500, 'stock_count' => 5],
                ['title' => '50ml', 'sku' => 'DUP-VAR-1', 'price' => 700, 'stock_count' => 5],
            ],
        ])->assertSessionHasErrors('variants.1.sku');
    }

    public function test_product_referenced_by_orders_cannot_be_deleted_and_unreferenced_can(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();
        $category = Category::firstOrFail();

        $orderedProduct = Product::create([
            'category_id' => $category->id,
            'slug' => 'ordered-product-test',
            'sku' => 'EB-ORD-99',
            'name' => 'Ordered Product',
            'price' => 500,
            'stock_count' => 10,
            'stock_status' => 'in_stock',
            'is_active' => true,
        ]);

        $order = Order::create([
            'order_number' => 'EVB-P3-9001',
            'customer_email' => 'buyer@example.com',
            'subtotal' => 500,
            'total' => 500,
            'delivery_method' => 'standard',
            'payment_method' => 'cod',
            'shipping_address' => ['address' => 'Dhaka'],
            'placed_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $orderedProduct->id,
            'name' => $orderedProduct->name,
            'slug' => $orderedProduct->slug,
            'qty' => 1,
            'price' => 500,
            'original_price' => 500,
            'line_total' => 500,
        ]);

        $this->actingAs($admin, 'admin')
            ->from('/admin/products')
            ->delete("/admin/products/{$orderedProduct->id}")
            ->assertRedirect('/admin/products')
            ->assertSessionHasErrors('product');

        $this->assertDatabaseHas('products', ['id' => $orderedProduct->id]);

        $freeProduct = Product::create([
            'category_id' => $category->id,
            'slug' => 'free-product-test',
            'sku' => 'EB-FREE-99',
            'name' => 'Free Product',
            'price' => 500,
            'stock_count' => 10,
            'stock_status' => 'in_stock',
            'is_active' => true,
        ]);

        $this->actingAs($admin, 'admin')
            ->delete("/admin/products/{$freeProduct->id}")
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseMissing('products', ['id' => $freeProduct->id]);
    }

    public function test_category_crud_seo_and_child_deletion_protection(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();

        $this->actingAs($admin, 'admin')->post('/admin/categories', [
            'name' => 'Parent Botanical',
            'slug' => 'parent-botanical',
            'sort_order' => 10,
            'is_active' => '1',
            'meta_title' => 'Parent Botanical SEO',
            'meta_description' => 'Parent category SEO description.',
        ])->assertRedirect(route('admin.categories.index'));

        $parent = Category::where('slug', 'parent-botanical')->firstOrFail();
        $this->assertSame('Parent Botanical SEO', $parent->meta_title);

        $this->actingAs($admin, 'admin')->post('/admin/categories', [
            'parent_id' => $parent->id,
            'name' => 'Child Botanical',
            'slug' => 'child-botanical',
        ])->assertRedirect(route('admin.categories.index'));

        // Parent with child cannot be deleted
        $this->actingAs($admin, 'admin')
            ->from('/admin/categories')
            ->delete("/admin/categories/{$parent->slug}")
            ->assertRedirect('/admin/categories')
            ->assertSessionHasErrors('category');

        $this->assertDatabaseHas('categories', ['id' => $parent->id]);
    }

    public function test_unauthorized_users_cannot_access_admin_product_routes(): void
    {
        $user = User::factory()->create(['status' => 'Active']);

        $this->post('/admin/products', ['name' => 'Hack'])->assertRedirect('/admin/login');
        $this->actingAs($user, 'web')->post('/admin/products', ['name' => 'Hack'])->assertRedirect('/admin/login');
    }
}

