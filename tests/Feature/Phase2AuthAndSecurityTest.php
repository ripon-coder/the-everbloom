<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Admin;
use App\Models\AdminActivityLog;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class Phase2AuthAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_updates_last_login_and_logout_invalidates_session(): void
    {
        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin.test@everbloom.test',
            'password' => Hash::make('StrongPass123'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        // Remember checkbox is unchecked by default
        $this->get('/admin/login')
            ->assertOk()
            ->assertDontSee('name="remember" value="1" checked', false);

        $beforeSessionId = session()->getId();

        $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'StrongPass123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin, 'admin');
        $this->assertNotNull($admin->fresh()->last_login_at);
        $this->assertNotSame($beforeSessionId, session()->getId());

        $this->post('/admin/logout')
            ->assertRedirect(route('admin.login'));

        $this->assertGuest('admin');
    }

    public function test_inactive_admin_cannot_login_and_deactivated_admin_session_is_ejected(): void
    {
        $admin = Admin::create([
            'name' => 'Inactive Admin',
            'email' => 'inactive.admin@everbloom.test',
            'password' => Hash::make('StrongPass123'),
            'role' => 'super_admin',
            'is_active' => false,
        ]);

        $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'StrongPass123',
        ])->assertSessionHasErrors('email');
        $this->assertGuest('admin');

        // Active admin gets deactivated mid-session
        $admin->update(['is_active' => true]);
        $this->actingAs($admin, 'admin')->get('/admin')->assertOk();

        $admin->update(['is_active' => false]);
        $this->actingAs($admin, 'admin')->get('/admin')
            ->assertRedirect(route('admin.login'));
        $this->assertGuest('admin');
    }

    public function test_inactive_customer_session_is_ejected_per_request(): void
    {
        $user = User::factory()->create([
            'status' => 'Active',
            'password' => Hash::make('StrongPass123'),
        ]);

        $this->actingAs($user, 'web')->get('/account')->assertOk();

        $user->update(['status' => 'Inactive']);
        $this->actingAs($user, 'web')->get('/account')
            ->assertRedirect(route('login'));
        $this->assertGuest('web');
    }

    public function test_customer_registration_enforces_password_policy_and_regenerates_session(): void
    {
        // Reject password without numbers
        $this->post('/account/register', [
            'register_type' => 'email',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'weak@example.com',
            'password' => 'onlyletters',
            'password_confirmation' => 'onlyletters',
            'terms' => '1',
        ])->assertSessionHasErrors('password');

        $beforeSessionId = session()->getId();

        $this->post('/account/register', [
            'register_type' => 'email',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'strong@example.com',
            'password' => 'StrongPass123',
            'password_confirmation' => 'StrongPass123',
            'terms' => '1',
        ])->assertRedirect(route('account'));

        $this->assertAuthenticated('web');
        $this->assertNotSame($beforeSessionId, session()->getId());
    }

    public function test_customer_password_reset_flow_works_end_to_end(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'reset.user@example.com',
            'status' => 'Active',
            'password' => Hash::make('OldPass123'),
        ]);

        $this->post('/account/forgot-password', [
            'email' => $user->email,
        ])->assertSessionHas('status');

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use (&$token) {
            $token = $notification->token;
            return true;
        });

        $this->assertNotNull($token);

        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->assertOk()
            ->assertSee('Reset Password');

        $this->post('/account/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewResetPass123',
            'password_confirmation' => 'NewResetPass123',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('NewResetPass123', $user->fresh()->password));
        $this->assertFalse(Password::broker('users')->tokenExists($user, $token));
    }

    public function test_login_rate_limiting_blocks_after_five_attempts(): void
    {
        Admin::create([
            'name' => 'Rate Admin',
            'email' => 'rate.admin@everbloom.test',
            'password' => Hash::make('StrongPass123'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', [
                'email' => 'rate.admin@everbloom.test',
                'password' => 'WrongPass123',
            ])->assertStatus(302);
        }

        $this->post('/admin/login', [
            'email' => 'rate.admin@everbloom.test',
            'password' => 'WrongPass123',
        ])->assertStatus(429);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/account/login', [
                'login_type' => 'email',
                'email' => 'rate.customer@example.com',
                'password' => 'WrongPass123',
            ])->assertStatus(302);
        }

        $this->post('/account/login', [
            'login_type' => 'email',
            'email' => 'rate.customer@example.com',
            'password' => 'WrongPass123',
        ])->assertStatus(429);
    }

    public function test_customer_cannot_access_other_customers_orders_or_addresses(): void
    {
        $owner = User::factory()->create(['status' => 'Active']);
        $other = User::factory()->create(['status' => 'Active']);

        $order = Order::create([
            'order_number' => 'EVB-IDOR-1001',
            'user_id' => $owner->id,
            'customer_email' => $owner->email,
            'subtotal' => 100,
            'total' => 100,
            'delivery_method' => 'standard',
            'payment_method' => 'cod',
            'shipping_address' => ['address' => 'Dhaka'],
            'placed_at' => now(),
        ]);

        $address = Address::create([
            'user_id' => $owner->id,
            'first_name' => 'Owner',
            'last_name' => 'User',
            'country' => 'Bangladesh',
            'address_line1' => 'House 1',
            'phone' => '+8801711000001',
            'is_default' => true,
        ]);

        $this->actingAs($other, 'web')
            ->get("/account/orders/{$order->order_number}")
            ->assertNotFound();

        $this->actingAs($other, 'web')
            ->deleteJson("/account/addresses/{$address->id}")
            ->assertForbidden();
    }

    public function test_account_views_use_post_logout_form_and_admin_writes_are_logged(): void
    {
        $this->seed();

        $user = User::where('email', 'eleanor.vance@example.com')->firstOrFail();
        $this->actingAs($user, 'web')
            ->get('/account')
            ->assertOk()
            ->assertSee('action="' . route('logout') . '"', false);

        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();
        $this->actingAs($admin, 'admin')
            ->post('/admin/coupons', [
                'code' => 'LOGTEST10',
                'type' => 'percentage',
                'value' => 10,
                'label' => 'Log Test 10%',
            ])
            ->assertRedirect(route('admin.coupons.index'));

        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $admin->id,
            'method' => 'POST',
            'route_name' => 'admin.coupons.store',
        ]);
        $this->assertGreaterThan(0, AdminActivityLog::count());
    }
}
