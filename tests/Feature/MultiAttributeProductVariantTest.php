<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Area;
use App\Models\Attribute;
use App\Models\District;
use App\Models\Order;
use App\Models\Product;
use App\Models\Thana;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MultiAttributeProductVariantTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_attributes_and_create_multi_attribute_variants_with_optional_image(): void
    {
        $this->seed();
        Storage::fake('s3');
        config(['filesystems.cloud' => 's3']);

        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();
        $user = User::where('email', 'eleanor.vance@example.com')->firstOrFail();

        // 1. Admin creates an Attribute + Value in /admin/attributes
        $this->actingAs($admin, 'admin')->get('/admin/attributes')->assertOk();
        $this->actingAs($admin, 'admin')->post('/admin/attributes', [
            'name' => 'Edition',
            'display_type' => 'button',
        ])->assertRedirect('/admin/attributes');

        $edition = Attribute::where('name', 'Edition')->firstOrFail();
        $this->actingAs($admin, 'admin')->post("/admin/attributes/{$edition->id}/values", [
            'value' => 'Limited Reserve',
        ])->assertRedirect('/admin/attributes');

        // 2. Admin creates a product with 2 multi-attribute variants:
        //    Variant 0 has an optional variant image (converted to .webp on S3), Variant 1 has no image.
        $mainImage = UploadedFile::fake()->image('main.jpg', 400, 500);
        $variantImage = UploadedFile::fake()->image('amber-50ml.png', 400, 500);

        $this->actingAs($admin, 'admin')->post('/admin/products', [
            'name' => 'Everbloom Reserve Elixir',
            'category_slug' => 'serums',
            'price' => 88.00,
            'short_description' => 'Multi-attribute reserve botanical elixir.',
            'images' => [$mainImage],
            'variants' => [
                [
                    'attributes' => ['Brand' => 'The Everbloom', 'Size' => '50ml / 1.7 fl oz', 'Color / Vessel' => 'Amber Glass'],
                    'sku' => 'RES-50-AMB',
                    'price' => 88.00,
                    'stock_count' => 15,
                    'stock_status' => 'in_stock',
                ],
                [
                    'attributes' => ['Brand' => 'Apothecary Reserve', 'Size' => '30ml / 1.0 fl oz', 'Color / Vessel' => 'Frosted Ivory'],
                    'sku' => 'RES-30-FRS',
                    'price' => 58.00,
                    'stock_count' => 10,
                    'stock_status' => 'in_stock',
                ],
            ],
            'variant_images' => [
                0 => $variantImage,
            ],
        ])->assertRedirect('/admin/products');

        $product = Product::with('productVariants')->where('name', 'Everbloom Reserve Elixir')->firstOrFail();
        $this->assertCount(2, $product->productVariants);

        $v1 = $product->productVariants[0];
        $v2 = $product->productVariants[1];

        $this->assertSame('The Everbloom / 50ml / 1.7 fl oz / Amber Glass', $v1->title);
        $this->assertSame('s3', $v1->image_source);
        $this->assertStringEndsWith('.webp', $v1->image);
        Storage::disk('s3')->assertExists($v1->image);

        $this->assertSame('Apothecary Reserve / 30ml / 1.0 fl oz / Frosted Ivory', $v2->title);
        $this->assertNull($v2->image);

        // 3. Storefront product page renders both variants
        $this->get("/shop/product/{$product->slug}")
            ->assertOk()
            ->assertSee($v1->title)
            ->assertSee($v2->title);

        // 4. Customer orders Variant 1 (with custom variant image)
        $dhaka = District::where('slug', 'dhaka')->firstOrFail();
        $gulshan = Thana::where('district_id', $dhaka->id)->where('name', 'Gulshan')->firstOrFail();
        $gulshan2 = Area::where('thana_id', $gulshan->id)->where('name', 'Gulshan 2')->firstOrFail();

        $checkoutRes = $this->actingAs($user, 'web')->postJson('/checkout', [
            'email' => $user->email,
            'phone' => '+8801711000001',
            'firstName' => 'Eleanor',
            'lastName' => 'Vance',
            'district_id' => $dhaka->id,
            'thana_id' => $gulshan->id,
            'area_id' => $gulshan2->id,
            'address' => 'House 42, Road 71',
            'shippingMethod' => 'standard',
            'paymentMethod' => 'card_demo',
            'items' => [
                ['productId' => $product->id, 'variantId' => $v1->title, 'qty' => 1],
            ],
        ])->assertOk();

        $order = Order::with('items')->where('order_number', $checkoutRes->json('order_number'))->firstOrFail();
        $orderItem = $order->items->first();
        $this->assertSame($v1->title, $orderItem->variant);
        $this->assertSame($v1->image, $orderItem->image);
        $this->assertSame('s3', $orderItem->image_source);
    }
}

