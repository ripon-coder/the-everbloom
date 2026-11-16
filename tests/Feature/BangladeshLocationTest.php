<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Area;
use App\Models\District;
use App\Models\Thana;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BangladeshLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_64_districts_are_seeded_and_cascading_api_works(): void
    {
        $this->seed();

        $this->assertSame(64, District::count());

        $dhaka = District::where('slug', 'dhaka')->firstOrFail();
        $gulshan = Thana::where('district_id', $dhaka->id)->where('name', 'Gulshan')->firstOrFail();

        $this->getJson('/locations/districts')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Dhaka']);

        $this->getJson("/locations/districts/{$dhaka->id}/thanas")
            ->assertOk()
            ->assertJsonFragment(['name' => 'Gulshan']);

        $this->getJson("/locations/thanas/{$gulshan->id}/areas")
            ->assertOk()
            ->assertJsonFragment(['name' => 'Gulshan 2', 'postal_code' => '1212']);
    }

    public function test_admin_can_rename_and_disable_locations_affecting_storefront_api(): void
    {
        $this->seed();
        $admin = Admin::where('email', 'admin@the-everbloom.com')->firstOrFail();
        $dhaka = District::where('slug', 'dhaka')->firstOrFail();
        $gulshan = Thana::where('district_id', $dhaka->id)->where('name', 'Gulshan')->firstOrFail();
        $gulshan2 = Area::where('thana_id', $gulshan->id)->where('name', 'Gulshan 2')->firstOrFail();

        // Admin views locations page
        $this->actingAs($admin, 'admin')->get('/admin/locations')->assertOk();

        // Admin renames an area
        $this->actingAs($admin, 'admin')->put("/admin/locations/areas/{$gulshan2->id}", [
            'name' => 'Gulshan 2 Circle',
            'postal_code' => '1212',
        ])->assertRedirect();

        $this->getJson("/locations/thanas/{$gulshan->id}/areas")
            ->assertOk()
            ->assertJsonFragment(['name' => 'Gulshan 2 Circle']);

        // Admin disables the thana
        $this->actingAs($admin, 'admin')->post("/admin/locations/thanas/{$gulshan->id}/toggle")
            ->assertRedirect();

        $this->getJson("/locations/districts/{$dhaka->id}/thanas")
            ->assertOk()
            ->assertJsonMissing(['name' => 'Gulshan']);
    }
}

