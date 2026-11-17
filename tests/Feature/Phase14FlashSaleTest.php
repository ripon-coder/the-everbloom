<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Area;
use App\Models\Coupon;
use App\Models\District;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\Thana;
use App\Models\User;
use App\Services\Orders\OrderStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class Phase14FlashSaleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_crud_flash_sales_and_enforce_validation_and_overlap_d08(): void
    {
        $admin = Admin::firstOrFail();
        $product = Product::where('is_active', true)->firstOrFail();
        $product->update(['price' => 100.00, 'stock_count' => 15]);

        $starts = now()->subHour()->format('Y-m-d\TH:i');
        $ends = now()->addHours(5)->format('Y-m-d\TH:i');

        $this->actingAs($admin, 'admin')
            ->post(route('admin.flash-sales.store'), [
                'title' => 'Evening Glow Drop',
                'starts_at' => $starts,
                'ends_at' => $ends,
                'status' => 'scheduled',
                'show_on_homepage' => '1',
            ])
            ->assertRedirect();

        $sale = FlashSale::where('title', 'Evening Glow Drop')->firstOrFail();
        $this->assertEquals(FlashSale::STATUS_ACTIVE, $sale->status);

        // Reject sale_price >= regular price
        $this->actingAs($admin, 'admin')
            ->post(route('admin.flash-sales.products.sync', $sale), [
                'items' => [
                    [
                        'product_id' => $product->id,
                        'sale_price' => 120.00,
                        'sale_stock' => 5,
                    ],
                ],
            ])
            ->assertSessionHasErrors('items.0.sale_price');

        // Reject sale_stock > available stock
        $this->actingAs($admin, 'admin')
            ->post(route('admin.flash-sales.products.sync', $sale), [
                'items' => [
                    [
                        'product_id' => $product->id,
                        'sale_price' => 70.00,
                        'sale_stock' => 50,
                    ],
                ],
            ])
            ->assertSessionHasErrors('items.0.sale_stock');

        // Valid assignment
        $this->actingAs($admin, 'admin')
            ->post(route('admin.flash-sales.products.sync', $sale), [
                'items' => [
                    [
                        'product_id' => $product->id,
                        'sale_price' => 70.00,
                        'sale_stock' => 5,
                        'max_qty_per_customer' => 2,
                        'is_active' => '1',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.flash-sales.products', $sale));

        // D-08: Overlapping sale on same product rejected
        $overlapSale = FlashSale::create([
            'title' => 'Overlapping Drop',
            'slug' => 'overlapping-drop',
            'starts_at' => now(),
            'ends_at' => now()->addHours(2),
            'status' => FlashSale::STATUS_ACTIVE,
            'show_on_homepage' => false,
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.flash-sales.products.sync', $overlapSale), [
                'items' => [
                    [
                        'product_id' => $product->id,
                        'sale_price' => 65.00,
                        'sale_stock' => 3,
                    ],
                ],
            ])
            ->assertSessionHasErrors('items.0.product_id');

        $this->actingAs($admin, 'admin')
            ->get(route('admin.flash-sales.show', $sale))
            ->assertOk()
            ->assertSee('Evening Glow Drop');
    }

    public function test_price_resolver_respects_timestamps_even_with_stale_scheduled_status_and_excludes_coupons_d03(): void
    {
        $product = Product::where('is_active', true)->firstOrFail();
        $product->update(['price' => 100.00, 'stock_count' => 20]);
        $product->productVariants()->delete();

        $sale = FlashSale::create([
            'title' => 'Stale Status Live Sale',
            'slug' => 'stale-status-live-sale',
            'starts_at' => now()->subMinutes(10),
            'ends_at' => now()->addHour(),
            'status' => FlashSale::STATUS_SCHEDULED, // intentionally stale
            'show_on_homepage' => true,
        ]);

        FlashSaleProduct::create([
            'flash_sale_id' => $sale->id,
            'product_id' => $product->id,
            'sale_price' => 60.00,
            'sale_stock' => 5,
            'sold_qty' => 0,
            'max_qty_per_customer' => 2,
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'NOSTACK20',
            'type' => 'percentage',
            'value' => 20,
            'label' => '20% Off',
            'min_order_amount' => 0,
            'is_active' => true,
        ]);

        // Quote should apply flash price 60.00 and reject coupon on flash-only cart (D-03)
        $res = $this->postJson(route('cart.quote'), [
            'items' => [['productId' => $product->id, 'qty' => 2]],
            'discountCode' => 'NOSTACK20',
            'shippingMethod' => 'standard',
        ])->assertOk();

        $res->assertJsonPath('lines.0.unit_price', 60);
        $res->assertJsonPath('lines.0.original_price', 100);
        $res->assertJsonPath('lines.0.is_flash_sale', true);
        $res->assertJsonPath('discount_amount', 0);
        $res->assertJsonPath('coupon_error', 'Promotional codes cannot be combined with Flash Sale items.');

        // After ends_at passes, quote reverts to regular price immediately even before scheduler runs
        Carbon::setTestNow(now()->addHours(2));
        $after = $this->postJson(route('cart.quote'), [
            'items' => [['productId' => $product->id, 'qty' => 1]],
            'shippingMethod' => 'standard',
        ])->assertOk();

        $after->assertJsonPath('lines.0.unit_price', 100);
        $after->assertJsonPath('lines.0.is_flash_sale', false);
        Carbon::setTestNow();
    }

    public function test_checkout_enforces_flash_limit_tracks_order_items_and_cancel_restores_d04(): void
    {
        $user = User::factory()->create(['status' => 'Active']);
        $district = District::where('is_active', true)->firstOrFail();
        $thana = Thana::where('district_id', $district->id)->where('is_active', true)->firstOrFail();
        $area = Area::where('thana_id', $thana->id)->where('is_active', true)->firstOrFail();

        $product = Product::where('is_active', true)->firstOrFail();
        $product->update(['price' => 100.00, 'stock_count' => 20]);
        $product->productVariants()->delete();

        $sale = FlashSale::create([
            'title' => 'Checkout Flash',
            'slug' => 'checkout-flash',
            'starts_at' => now()->subMinutes(5),
            'ends_at' => now()->addHours(3),
            'status' => FlashSale::STATUS_ACTIVE,
            'show_on_homepage' => true,
        ]);

        $fsp = FlashSaleProduct::create([
            'flash_sale_id' => $sale->id,
            'product_id' => $product->id,
            'sale_price' => 55.00,
            'sale_stock' => 4,
            'sold_qty' => 0,
            'max_qty_per_customer' => 2,
            'is_active' => true,
        ]);

        $payload = [
            'firstName' => 'Nadia',
            'lastName' => 'Karim',
            'email' => $user->email,
            'phone' => '01711000001',
            'district_id' => $district->id,
            'thana_id' => $thana->id,
            'area_id' => $area->id,
            'address' => 'Road 12, Banani',
            'shippingMethod' => 'standard',
            'paymentMethod' => 'cod_demo',
            'items' => [['productId' => $product->id, 'qty' => 2]],
        ];

        $this->actingAs($user, 'web')
            ->postJson(route('checkout.store'), $payload)
            ->assertOk()
            ->assertJsonPath('success', true);

        $fsp->refresh();
        $this->assertEquals(2, $fsp->sold_qty);

        $order = Order::latest('id')->firstOrFail();
        $orderItem = $order->items()->firstOrFail();
        $this->assertEquals(55.00, $orderItem->price);
        $this->assertEquals(100.00, $orderItem->original_price);
        $this->assertEquals($sale->id, $orderItem->flash_sale_id);
        $this->assertEquals($fsp->id, $orderItem->flash_sale_product_id);

        // Regular product price remains untouched
        $this->assertEquals(100.00, $product->fresh()->price);

        // Second purchase by same customer exceeds max_qty_per_customer=2 -> 422
        $payload['items'] = [['productId' => $product->id, 'qty' => 1]];
        $this->actingAs($user, 'web')
            ->postJson(route('checkout.store'), $payload)
            ->assertStatus(422);

        // Cancel order while sale is active -> sold_qty restored (D-04)
        app(OrderStatusService::class)->transition($order, 'Cancelled');
        $this->assertEquals(0, $fsp->fresh()->sold_qty);

        // Storefront /flash-sales and homepage return 200
        $this->get(route('flash-sales.index'))->assertOk()->assertSee('Checkout Flash');
        $this->get(route('home'))->assertOk()->assertSee($product->name);
    }

    public function test_flashsales_sync_command_activates_and_expires_campaigns(): void
    {
        $toActivate = FlashSale::create([
            'title' => 'Ready To Go',
            'slug' => 'ready-to-go',
            'starts_at' => now()->subMinute(),
            'ends_at' => now()->addHour(),
            'status' => FlashSale::STATUS_SCHEDULED,
        ]);

        $toExpire = FlashSale::create([
            'title' => 'Already Ended',
            'slug' => 'already-ended',
            'starts_at' => now()->subHours(3),
            'ends_at' => now()->subMinute(),
            'status' => FlashSale::STATUS_ACTIVE,
        ]);

        $this->artisan('flashsales:sync')->assertExitCode(0);

        $this->assertEquals(FlashSale::STATUS_ACTIVE, $toActivate->fresh()->status);
        $this->assertEquals(FlashSale::STATUS_EXPIRED, $toExpire->fresh()->status);
    }
}
