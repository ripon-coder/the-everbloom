<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Area;
use App\Models\Coupon;
use App\Models\District;
use App\Models\Product;
use App\Models\Thana;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase13CouponsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_edit_toggle_and_view_coupon_history_with_validation(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();

        // Percentage > 100 rejected
        $this->actingAs($admin, 'admin')->post('/admin/coupons', [
            'code' => 'OVER150',
            'type' => 'percentage',
            'value' => 150,
            'label' => 'Invalid',
        ])->assertSessionHasErrors('value');

        // Duplicate code (case-insensitive) rejected
        $this->actingAs($admin, 'admin')->post('/admin/coupons', [
            'code' => 'welcome10',
            'type' => 'percentage',
            'value' => 10,
            'label' => 'Duplicate',
        ])->assertSessionHasErrors('code');

        // expires_at before starts_at rejected
        $this->actingAs($admin, 'admin')->post('/admin/coupons', [
            'code' => 'DATES10',
            'type' => 'percentage',
            'value' => 10,
            'label' => 'Dates',
            'starts_at' => '2026-12-10',
            'expires_at' => '2026-12-01',
        ])->assertSessionHasErrors('expires_at');

        // Create valid coupon with max_discount & per_customer_limit
        $this->actingAs($admin, 'admin')->post('/admin/coupons', [
            'code' => 'summer25',
            'type' => 'percentage',
            'value' => 25,
            'max_discount' => 200,
            'per_customer_limit' => 1,
            'label' => '25% Off',
            'is_active' => '1',
        ])->assertRedirect(route('admin.coupons.index'));

        $coupon = Coupon::where('code', 'SUMMER25')->firstOrFail();
        $this->assertSame(200.0, $coupon->max_discount);
        $this->assertSame(1, $coupon->per_customer_limit);

        // Edit & update
        $this->actingAs($admin, 'admin')->get("/admin/coupons/{$coupon->id}/edit")->assertOk();
        $this->actingAs($admin, 'admin')->put("/admin/coupons/{$coupon->id}", [
            'code' => 'SUMMER25',
            'type' => 'percentage',
            'value' => 20,
            'max_discount' => 150,
            'per_customer_limit' => 1,
            'label' => '20% Off Updated',
            'is_active' => '1',
        ])->assertRedirect(route('admin.coupons.index'));
        $this->assertSame(20.0, $coupon->fresh()->value);

        // Toggle
        $this->actingAs($admin, 'admin')->post("/admin/coupons/{$coupon->id}/toggle")->assertRedirect();
        $this->assertFalse($coupon->fresh()->is_active);

        // History page
        $this->actingAs($admin, 'admin')->get("/admin/coupons/{$coupon->id}/history")->assertOk();
    }

    public function test_coupon_product_scope_and_per_customer_limit_enforced_in_quote_and_checkout(): void
    {
        $this->seed();
        $user = User::where('email', 'eleanor.vance@example.com')->firstOrFail();
        $products = Product::with('productVariants')->where('is_active', true)->take(2)->get();
        $p1 = $products[0];
        $p2 = $products[1];
        $v1 = $p1->productVariants->firstOrFail();
        $v2 = $p2->productVariants->firstOrFail();

        $v1->update(['price' => 200, 'stock_count' => 10]);
        $v2->update(['price' => 300, 'stock_count' => 10]);

        $coupon = Coupon::create([
            'code' => 'ONLYP1',
            'type' => 'percentage',
            'value' => 50,
            'per_customer_limit' => 1,
            'label' => '50% off P1 only',
            'is_active' => true,
        ]);
        $coupon->products()->sync([$p1->id]);

        // Quote with both P1 (200) and P2 (300): discount applies ONLY to P1 -> 100.00
        $quote = $this->actingAs($user, 'web')->postJson('/cart/quote', [
            'items' => [
                ['productId' => $p1->id, 'variantId' => $v1->size, 'qty' => 1],
                ['productId' => $p2->id, 'variantId' => $v2->size, 'qty' => 1],
            ],
            'discountCode' => 'ONLYP1',
        ])->assertOk();

        $quote->assertJson([
            'subtotal' => 500.0,
            'discount_amount' => 100.0,
        ]);

        // First checkout succeeds and applies discount
        $dhaka = District::where('slug', 'dhaka')->firstOrFail();
        $gulshan = Thana::where('district_id', $dhaka->id)->where('name', 'Gulshan')->firstOrFail();
        $gulshan2 = Area::where('thana_id', $gulshan->id)->where('name', 'Gulshan 2')->firstOrFail();

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
            'discountCode' => 'ONLYP1',
            'items' => [
                ['productId' => $p1->id, 'variantId' => $v1->size, 'qty' => 1],
            ],
        ])->assertOk();

        // Second quote for same customer is rejected by per_customer_limit = 1
        $secondQuote = $this->actingAs($user, 'web')->postJson('/cart/quote', [
            'items' => [
                ['productId' => $p1->id, 'variantId' => $v1->size, 'qty' => 1],
            ],
            'discountCode' => 'ONLYP1',
        ])->assertOk();

        $secondQuote->assertJson([
            'discount_amount' => 0.0,
        ]);
        $this->assertNotNull($secondQuote->json('coupon_error'));
    }
}

