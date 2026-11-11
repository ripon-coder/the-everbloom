<?php

namespace Tests\Feature;

use Tests\TestCase;

class AccountAreaTest extends TestCase
{
    public function test_account_overview_page_loads_with_required_elements(): void
    {
        $response = $this->get('/account');

        $response->assertStatus(200);
        $response->assertSee('MY ACCOUNT');
        $response->assertSee('Welcome, Eleanor');
        $response->assertSee('eleanor.vance@example.com');
        $response->assertSee('Demo account. Sample data.');
        $response->assertDontSee('Client since');
        $response->assertDontSee('Join The Everbloom Archive'); // No newsletter
        $response->assertSee('Recent Orders');
        $response->assertSee('Default Address');
        $response->assertSee('Wishlist');
        $response->assertSee('aria-current="page"', false);
        $response->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_orders_list_page_and_empty_state(): void
    {
        $response = $this->get('/account/orders');

        $response->assertStatus(200);
        $response->assertSee('Orders');
        $response->assertSee('EVB-9204');
        $response->assertSee('EVB-8921');
        $response->assertSee('View details');

        // Test empty state
        $emptyResponse = $this->get('/account/orders?empty=1');
        $emptyResponse->assertStatus(200);
        $emptyResponse->assertSee('No Orders Yet');
        $emptyResponse->assertSee('Start shopping');
    }

    public function test_order_details_page(): void
    {
        $response = $this->get('/account/orders/EVB-8921');

        $response->assertStatus(200);
        $response->assertSee('Order #EVB-8921');
        $response->assertSee('Demo order');
        $response->assertSee('Botanical Radiance Serum');
        $response->assertSee('Camellia Restorative Moisturizer');
        $response->assertSee('Order Summary');
        $response->assertSee('Buy again');
        $response->assertSee('Back to orders');
        $response->assertSee('Processing');
        $response->assertSee('Shipped');
        $response->assertSee('Delivered');
    }

    public function test_addresses_page(): void
    {
        $response = $this->get('/account/addresses');

        $response->assertStatus(200);
        $response->assertSee('Addresses');
        $response->assertSee('Default');
        $response->assertSee('740 Botanical Way');
        $response->assertSee('Add new address');
        $response->assertSee('Demo only. Nothing was saved.');
    }

    public function test_profile_page(): void
    {
        $response = $this->get('/account/profile');

        $response->assertStatus(200);
        $response->assertSee('Profile');
        $response->assertSee('Personal Information');
        $response->assertSee('Security & Password', false);
        $response->assertSee('Current Password');
        $response->assertSee('New Password');
        $response->assertSee('Confirm New Password');
    }

    public function test_login_and_register_pages(): void
    {
        $login = $this->get('/account/login');
        $login->assertStatus(200);
        $login->assertSee('Sign In');
        $login->assertSee('input-field');
        $login->assertSee('Continue with Google');
        $login->assertSee('Continue with Apple');
        $login->assertSee('Or continue with email');
        $login->assertSee('Forgot password?');

        $register = $this->get('/account/register');
        $register->assertStatus(200);
        $register->assertSee('Create Account');
        $register->assertSee('Sign up with Google');
        $register->assertSee('Sign up with Apple');
        $register->assertSee('Or register with email');
        $register->assertSee('Demo only. No account was created.');
    }
}
