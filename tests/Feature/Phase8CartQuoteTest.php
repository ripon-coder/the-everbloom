<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase8CartQuoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_quote_computes_server_authoritative_totals_coupons_and_shipping(): void
    {
        $this->seed();

        $product = Product::with('productVariants')->where('is_active', true)->firstOrFail();
        $variant = $product->productVariants->firstOrFail();
        $variant->update(['price' => 200.00, 'stock_count' => 10, 'stock_status' => 'in_stock']);

        Coupon::create([
            'code' => 'QUOTE10',
            'type' => 'percentage',
            'value' => 10,
            'max_discount' => 30,
            'label' => '10% Max 30',
            'min_order_amount' => 100,
            'is_active' => true,
        ]);

        // 2 * 200 = 400 subtotal; 10% = 40 capped at 30; shipping standard = 60 (< 500 threshold) -> total = 430
        $res = $this->postJson('/cart/quote', [
            'items' => [
                ['productId' => $product->id, 'variantId' => $variant->size, 'qty' => 2],
            ],
            'discountCode' => 'quote10',
            'shippingMethod' => 'standard',
        ])->assertOk();

        $res->assertJson([
            'has_stock_errors' => false,
            'subtotal' => 400.0,
            'discount_code' => 'QUOTE10',
            'discount_amount' => 30.0,
            'shipping_cost' => 60.0,
            'total' => 430.0,
        ]);
    }

    public function test_cart_quote_reports_stock_errors_and_inactive_products(): void
    {
        $this->seed();

        $product = Product::with('productVariants')->where('is_active', true)->firstOrFail();
        $variant = $product->productVariants->firstOrFail();
        $variant->update(['stock_count' => 2, 'stock_status' => 'low_stock']);

        $res = $this->postJson('/cart/quote', [
            'items' => [
                ['productId' => $product->id, 'variantId' => $variant->size, 'qty' => 5],
            ],
        ])->assertOk();

        $res->assertJson([
            'has_stock_errors' => true,
        ]);
        $this->assertSame(2, $res->json('lines.0.max_qty'));
        $this->assertNotEmpty($res->json('errors'));

        // Inactive product reported as unavailable
        $product->update(['is_active' => false]);
        $inactiveRes = $this->postJson('/cart/quote', [
            'items' => [
                ['productId' => $product->id, 'variantId' => $variant->size, 'qty' => 1],
            ],
        ])->assertOk();

        $inactiveRes->assertJson([
            'has_stock_errors' => true,
            'subtotal' => 0.0,
            'total' => 0.0,
        ]);
    }
}

