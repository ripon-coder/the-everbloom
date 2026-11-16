<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicCurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_updating_currency_to_bdt_taka_updates_storefront_and_admin(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();
        $user = User::where('email', 'eleanor.vance@example.com')->firstOrFail();

        $this->actingAs($admin, 'admin')->post('/admin/settings', [
            'name' => 'The Everbloom',
            'tagline' => 'Naturally Beautiful.',
            'currency_code' => 'BDT',
            'currency_symbol' => '৳',
            'announcement_enabled' => '1',
            'announcement_message' => 'Free shipping in Dhaka',
            'announcement_link' => '/shop',
            'announcement_link_text' => 'Shop Now',
            'free_shipping_threshold' => 2000,
            'standard_rate' => 60,
            'express_rate' => 120,
            'returns_window_days' => 30,
            'contact_email' => 'concierge@the-everbloom.com',
        ])->assertRedirect();

        // Storefront views reflect ৳ and BDT
        $this->get('/shop')->assertOk()->assertSee('৳');
        $this->get('/shop/product/botanical-radiance-serum')->assertOk()->assertSee('৳')->assertSee('BDT');
        $this->get('/cart')->assertOk()->assertSee('৳');
        $this->get('/checkout')->assertOk()->assertSee('৳');
        $this->actingAs($user, 'web')->get('/account/orders')->assertOk()->assertSee('৳');

        // Admin views reflect ৳ and BDT
        $this->actingAs($admin, 'admin')->get('/admin')->assertOk()->assertSee('৳');
        $this->actingAs($admin, 'admin')->get('/admin/products')->assertOk()->assertSee('৳');
        $this->actingAs($admin, 'admin')->get('/admin/orders')->assertOk()->assertSee('৳');
        $this->actingAs($admin, 'admin')->get('/admin/invoices/EVB-9204')->assertOk()->assertSee('৳')->assertSee('BDT');
        $this->actingAs($admin, 'admin')->get('/admin/purchases')->assertOk()->assertSee('৳');
    }
}

