<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DualAuthSessionIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_requests_redirect_to_respective_login_pages(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/account')->assertRedirect('/account/login');
    }

    public function test_admin_can_sign_in_using_admins_table_credentials(): void
    {
        $admin = Admin::create([
            'name' => 'Clara Vance',
            'email' => 'admin@the-everbloom.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@the-everbloom.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin, 'admin');
        $this->assertGuest('web');
    }

    public function test_customer_can_sign_in_using_users_table_credentials(): void
    {
        $user = User::create([
            'first_name' => 'Eleanor',
            'last_name' => 'Vance',
            'email' => 'eleanor.vance@example.com',
            'password' => Hash::make('password'),
            'status' => 'Active',
        ]);

        $response = $this->post('/account/login', [
            'email' => 'eleanor.vance@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/account');
        $this->assertAuthenticatedAs($user, 'web');
        $this->assertGuest('admin');
    }

    public function test_customer_logged_into_web_guard_cannot_access_admin(): void
    {
        $user = User::create([
            'first_name' => 'Eleanor',
            'last_name' => 'Vance',
            'email' => 'eleanor.vance@example.com',
            'password' => Hash::make('password'),
            'status' => 'Active',
        ]);

        $this->actingAs($user, 'web')
            ->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function test_isolate_admin_session_middleware_uses_distinct_cookies_and_paths(): void
    {
        $adminResponse = $this->get('/admin/login');
        $adminResponse->assertOk();
        $this->assertSame('everbloom_admin_session', config('session.cookie'));
        $this->assertSame('/admin', config('session.path'));

        $userResponse = $this->get('/account/login');
        $userResponse->assertOk();
        $this->assertSame('everbloom_user_session', config('session.cookie'));
        $this->assertSame('/', config('session.path'));
    }
}
