<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ProductionSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class Phase1SchemaAndIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_unblocked_phase_1_tables_and_columns_exist_and_blocked_tables_are_deferred(): void
    {
        foreach ([
            'brands',
            'order_status_histories',
            'stock_movements',
            'order_item_batches',
            'coupon_redemptions',
            'coupon_product',
            'coupon_category',
            'wishlists',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Expected table [{$table}] to exist.");
        }

        // Still-deferred tables & columns per OPEN_DECISIONS.md
        $this->assertFalse(Schema::hasTable('flash_sales'));
        $this->assertFalse(Schema::hasTable('flash_sale_products'));
        $this->assertFalse(Schema::hasTable('payment_transactions'));
        $this->assertFalse(Schema::hasColumn('users', 'phone_normalized'));

        // Approved new columns
        $this->assertTrue(Schema::hasColumns('coupons', ['max_discount', 'per_customer_limit', 'starts_at']));
        $this->assertTrue(Schema::hasColumns('orders', ['payment_status', 'placed_via']));
        $this->assertTrue(Schema::hasColumns('order_items', ['original_price']));
    }

    public function test_orders_payment_status_defaults_to_pending_and_original_price_is_fillable(): void
    {
        $orderId = DB::table('orders')->insertGetId([
            'order_number' => 'EVB-TEST-1001',
            'customer_email' => 'guest@example.com',
            'subtotal' => 100.00,
            'total' => 100.00,
            'delivery_method' => 'standard',
            'payment_method' => 'cod',
            'shipping_address' => json_encode(['address' => 'Dhaka']),
            'placed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $order = Order::findOrFail($orderId);
        $this->assertSame('Pending', $order->payment_status);
        $this->assertSame('web', $order->placed_via);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'name' => 'Botanical Serum',
            'slug' => 'botanical-serum',
            'qty' => 1,
            'price' => 45.00,
            'original_price' => 55.00,
            'line_total' => 45.00,
        ]);

        $this->assertSame(55.00, $item->original_price);
    }

    public function test_category_with_products_cannot_be_deleted_at_db_or_controller_level(): void
    {
        $this->seed();

        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();
        $category = Category::whereHas('products')->firstOrFail();

        // Controller blocks deletion with validation error
        $response = $this->actingAs($admin, 'admin')
            ->from('/admin/categories')
            ->delete("/admin/categories/{$category->slug}");

        $response->assertRedirect('/admin/categories');
        $response->assertSessionHasErrors('category');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);

        // DB foreign key restrictOnDelete also blocks raw deletion
        $this->expectException(QueryException::class);
        $category->delete();
    }

    public function test_production_seeder_only_seeds_env_admin_and_locations_without_demo_data(): void
    {
        putenv('ADMIN_SEED_EMAIL=owner@everbloom-prod.test');
        putenv('ADMIN_SEED_PASSWORD=StrongProdPass!987');
        putenv('ADMIN_SEED_NAME=Prod Owner');

        try {
            $this->seed(ProductionSeeder::class);

            $this->assertDatabaseHas('admins', [
                'email' => 'owner@everbloom-prod.test',
                'role' => 'super_admin',
                'is_active' => 1,
            ]);
            $this->assertSame(1, Admin::count());
            $this->assertSame(0, User::count());
            $this->assertSame(0, Product::count());
            $this->assertSame(0, Order::count());
            $this->assertSame(0, Coupon::count());
            $this->assertGreaterThan(0, DB::table('districts')->count());
        } finally {
            putenv('ADMIN_SEED_EMAIL');
            putenv('ADMIN_SEED_PASSWORD');
            putenv('ADMIN_SEED_NAME');
        }
    }

    public function test_production_seeder_rejects_default_password(): void
    {
        putenv('ADMIN_SEED_EMAIL=owner@everbloom-prod.test');
        putenv('ADMIN_SEED_PASSWORD=password');

        try {
            $this->expectException(RuntimeException::class);
            $this->seed(ProductionSeeder::class);
        } finally {
            putenv('ADMIN_SEED_EMAIL');
            putenv('ADMIN_SEED_PASSWORD');
        }
    }

    public function test_database_seeder_aborts_in_production_environment(): void
    {
        $originalEnv = $this->app['env'];
        $this->app['env'] = 'production';

        try {
            $this->expectException(RuntimeException::class);
            (new DatabaseSeeder())->run();
        } finally {
            $this->app['env'] = $originalEnv;
        }
    }
}

