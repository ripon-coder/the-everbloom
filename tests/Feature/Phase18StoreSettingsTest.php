<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase18StoreSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_update_branding_currency_decimals_timezone_tax_and_social_links(): void
    {
        Storage::fake('s3');
        config(['filesystems.default' => 's3']);

        $admin = Admin::firstOrFail();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('Asia/Dhaka')
            ->assertSee('Decimals');

        $this->actingAs($admin, 'admin')
            ->post(route('admin.settings.update'), [
                'name' => 'The Everbloom BD',
                'tagline' => 'Botanical Skincare',
                'logo' => UploadedFile::fake()->image('logo.png', 200, 80),
                'favicon' => UploadedFile::fake()->image('favicon.png', 32, 32),
                'currency_code' => 'BDT',
                'currency_symbol' => '৳',
                'currency_decimals' => 2,
                'currency_position' => 'after',
                'timezone' => 'Asia/Dhaka',
                'tax_enabled' => '1',
                'tax_rate' => 5,
                'tax_inclusive' => '1',
                'free_shipping_threshold' => 2000,
                'standard_rate' => 60,
                'express_rate' => 120,
                'returns_window_days' => 14,
                'contact_email' => 'care@the-everbloom.com',
                'contact_phone' => '+8801711000000',
                'social_instagram' => 'https://instagram.com/everbloombd',
                'social_facebook' => 'https://facebook.com/everbloombd',
            ])
            ->assertRedirect();

        $site = Setting::where('key', 'site')->firstOrFail()->value;

        $this->assertEquals('Asia/Dhaka', $site['timezone']);
        $this->assertEquals('Asia/Dhaka', config('app.timezone'));
        $this->assertEquals(2, $site['currency']['decimals']);
        $this->assertEquals('after', $site['currency']['position']);
        $this->assertTrue($site['tax']['enabled']);
        $this->assertEquals(5.0, (float) $site['tax']['rate']);
        $this->assertEquals('https://instagram.com/everbloombd', $site['social']['instagram']);
        $this->assertNotEmpty($site['logo']);
        $this->assertNotEmpty($site['favicon']);
    }
}

