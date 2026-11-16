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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminConsoleEndToEndTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pages_return_ok(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();

        $routes = [
            '/admin',
            '/admin/products',
            '/admin/products/create',
            '/admin/categories',
            '/admin/categories/create',
            '/admin/orders',
            '/admin/orders/EVB-9204',
            '/admin/inventory',
            '/admin/invoices',
            '/admin/invoices/EVB-9204',
            '/admin/customers',
            '/admin/customers/1',
            '/admin/coupons',
            '/admin/coupons/create',
            '/admin/reviews',
            '/admin/settings',
        ];

        foreach ($routes as $route) {
            $this->actingAs($admin, 'admin')->get($route)->assertOk();
        }
    }

    public function test_full_admin_to_storefront_end_to_end_flow(): void
    {
        $this->seed();
        Storage::fake('s3');
        config(['filesystems.cloud' => 's3']);

        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();
        $user = User::where('email', 'eleanor.vance@example.com')->firstOrFail();

        // 1. Admin updates announcement bar in settings
        $this->actingAs($admin, 'admin')->post('/admin/settings', [
            'name' => 'The Everbloom',
            'tagline' => 'Naturally Beautiful. Always Blooming.',
            'currency_code' => 'USD',
            'currency_symbol' => '$',
            'announcement_enabled' => '1',
            'announcement_message' => 'Harvest Sale: Free Express Shipping!',
            'announcement_link' => '/shop',
            'announcement_link_text' => 'Explore',
            'free_shipping_threshold' => 75,
            'standard_rate' => 6,
            'express_rate' => 15,
            'returns_window_days' => 30,
            'contact_email' => 'concierge@the-everbloom.com',
        ])->assertRedirect();

        // 2. Admin creates a new product with image converted to WebP on S3
        $imageFile = UploadedFile::fake()->image('elixir.jpg', 400, 500);
        $this->actingAs($admin, 'admin')->post('/admin/products', [
            'name' => 'Wildflower Moonlight Elixir',
            'category_slug' => 'serums',
            'price' => 72.00,
            'short_description' => 'Concentrated wildflower botanical night oil.',
            'sizes' => ['30ml / 1.0 fl oz'],
            'stock_count' => 20,
            'images' => [$imageFile],
        ])->assertRedirect('/admin/products');

        $newProduct = Product::where('name', 'Wildflower Moonlight Elixir')->firstOrFail();
        $this->assertSame('s3', $newProduct->image_source);
        $this->assertStringEndsWith('.webp', $newProduct->images[0]);
        Storage::disk('s3')->assertExists($newProduct->images[0]);

        // 3. Storefront search & shop show the new product
        $this->get('/shop')->assertOk()->assertSee('Wildflower Moonlight Elixir');
        $this->getJson('/search?q=Wildflower')->assertOk()->assertJsonFragment([
            'name' => 'Wildflower Moonlight Elixir',
        ]);

        // 4. Admin creates a coupon and customer places an order with it
        $this->actingAs($admin, 'admin')->post('/admin/coupons', [
            'code' => 'HARVEST15',
            'type' => 'percentage',
            'value' => 15,
            'label' => '15% Harvest Promo',
            'is_active' => '1',
        ])->assertRedirect('/admin/coupons');

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
            'zip' => '1212',
            'shippingMethod' => 'standard',
            'paymentMethod' => 'card_demo',
            'discountCode' => 'HARVEST15',
            'items' => [
                ['productId' => $newProduct->id, 'variantId' => '30ml / 1.0 fl oz', 'qty' => 1],
            ],
        ])->assertOk();

        $orderNumber = $checkoutRes->json('order_number');
        $this->assertTrue(Order::where('order_number', $orderNumber)->exists());

        // 5. Order appears in both Customer account and Admin orders/invoices
        $this->actingAs($user, 'web')->get('/account/orders')->assertOk()->assertSee($orderNumber);
        $this->actingAs($admin, 'admin')->get('/admin/orders')->assertOk()->assertSee($orderNumber);
        $this->actingAs($admin, 'admin')->get('/admin/invoices')->assertOk()->assertSee($orderNumber);
    }
}
