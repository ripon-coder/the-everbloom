<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_storefront_routes_return_ok(): void
    {
        $this->seed();

        $publicRoutes = [
            '/',
            '/styleguide',
            '/shop',
            '/shop?sort=price-asc',
            '/shop?search=bakuchiol',
            '/shop/category/serums',
            '/shop/product/botanical-radiance-serum',
            '/search?q=rosehip',
            '/cart',
            '/checkout',
            '/about',
            '/contact',
            '/faq',
            '/wishlist',
            '/privacy-policy',
            '/terms-and-conditions',
            '/shipping-and-returns',
            '/account/login',
            '/account/register',
        ];

        foreach ($publicRoutes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }

        $user = User::where('email', 'eleanor.vance@example.com')->firstOrFail();

        $accountRoutes = [
            '/account',
            '/account/orders',
            '/account/orders/EVB-8921',
            '/account/addresses',
            '/account/profile',
        ];

        foreach ($accountRoutes as $route) {
            $response = $this->actingAs($user, 'web')->get($route);
            $response->assertStatus(200);
        }
    }
}
