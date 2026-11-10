<?php

namespace Tests\Feature;

use Tests\TestCase;

class StorefrontRoutesTest extends TestCase
{
    public function test_all_storefront_routes_return_ok(): void
    {
        $routes = [
            '/',
            '/styleguide',
            '/shop',
            '/shop/category/serums',
            '/shop/product/botanical-radiance-serum',
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
            '/account',
            '/account/orders',
            '/account/orders/EVB-8921',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }
    }
}
