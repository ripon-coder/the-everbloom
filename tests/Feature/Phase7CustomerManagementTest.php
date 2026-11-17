<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase7CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_filter_paginate_and_export_customers(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();

        $this->actingAs($admin, 'admin')
            ->get('/admin/customers?search=eleanor')
            ->assertOk()
            ->assertSee('eleanor.vance@example.com')
            ->assertDontSee('emma.chen@example.com');

        $this->actingAs($admin, 'admin')
            ->get('/admin/customers?status=Inactive')
            ->assertOk()
            ->assertSee('lucas.wright@example.com')
            ->assertDontSee('eleanor.vance@example.com');

        $export = $this->actingAs($admin, 'admin')
            ->get('/admin/customers/export?search=eleanor');

        $export->assertOk();
        $export->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Eleanor Vance', $export->streamedContent());
    }

    public function test_admin_can_toggle_customer_status_and_fabricated_labels_are_removed(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();
        $user = User::where('email', 'eleanor.vance@example.com')->firstOrFail();

        $this->actingAs($admin, 'admin')
            ->get("/admin/customers/{$user->id}")
            ->assertOk()
            ->assertDontSee('VIP Botanical Member')
            ->assertDontSee('Evening Restorative Hydration')
            ->assertDontSee('580 pts')
            ->assertSee('Average Order Value')
            ->assertSee('Gulshan, Dhaka');

        // Toggle Active -> Inactive
        $this->actingAs($admin, 'admin')
            ->post("/admin/customers/{$user->id}/toggle-status")
            ->assertRedirect();

        $this->assertSame('Inactive', $user->fresh()->status);

        // Live session for deactivated customer is ejected
        $this->actingAs($user->fresh(), 'web')
            ->get('/account')
            ->assertRedirect(route('login'));
    }
}
