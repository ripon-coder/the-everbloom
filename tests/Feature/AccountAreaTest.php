<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountAreaTest extends TestCase
{
    use RefreshDatabase;

    private function demoUser(): User
    {
        $this->seed();

        return User::where('email', 'eleanor.vance@example.com')->firstOrFail();
    }

    public function test_account_overview_page_loads_with_required_elements(): void
    {
        $user = $this->demoUser();
        $response = $this->actingAs($user, 'web')->get('/account');

        $response->assertStatus(200);
        $response->assertSee('MY ACCOUNT');
        $response->assertSee('Welcome, Eleanor');
        $response->assertSee('eleanor.vance@example.com');
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
        $user = $this->demoUser();
        $response = $this->actingAs($user, 'web')->get('/account/orders');

        $response->assertStatus(200);
        $response->assertSee('Orders');
        $response->assertSee('EVB-9204');
        $response->assertSee('EVB-8921');
        $response->assertSee('View details');

        // Test empty state
        $emptyResponse = $this->actingAs($user, 'web')->get('/account/orders?empty=1');
        $emptyResponse->assertStatus(200);
        $emptyResponse->assertSee('No orders yet');
        $emptyResponse->assertSee('Start shopping');
    }

    public function test_order_details_page(): void
    {
        $user = $this->demoUser();
        $response = $this->actingAs($user, 'web')->get('/account/orders/EVB-8921');

        $response->assertStatus(200);
        $response->assertSee('Order #EVB-8921');
        $response->assertSee('Botanical Radiance Serum');
        $response->assertSee('Camellia Restorative Moisturizer');
        $response->assertSee('Order Summary');
        $response->assertSee('Back to orders');
        $response->assertSee('Processing');
        $response->assertSee('Shipped');
        $response->assertSee('Delivered');
    }

    public function test_addresses_page(): void
    {
        $user = $this->demoUser();
        $response = $this->actingAs($user, 'web')->get('/account/addresses');

        $response->assertStatus(200);
        $response->assertSee('Addresses');
        $response->assertSee('Default');
        $response->assertSee('House 42, Road 71, Gulshan 2');
        $response->assertSee('Add new address');
    }

    public function test_profile_page(): void
    {
        $user = $this->demoUser();
        $response = $this->actingAs($user, 'web')->get('/account/profile');

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
    }
}
