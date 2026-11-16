<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class AdminSettingController extends Controller
{
    public function index()
    {
        $site = Setting::where('key', 'site')->first()?->value ?? config('demo.site', []);

        return view('admin.settings.index', compact('site'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'currency_code' => ['required', 'string', 'max:10'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'announcement_enabled' => ['nullable', 'boolean'],
            'announcement_message' => ['nullable', 'string', 'max:255'],
            'announcement_link' => ['nullable', 'string', 'max:255'],
            'announcement_link_text' => ['nullable', 'string', 'max:100'],
            'free_shipping_threshold' => ['required', 'numeric', 'min:0'],
            'standard_rate' => ['required', 'numeric', 'min:0'],
            'express_rate' => ['required', 'numeric', 'min:0'],
            'returns_window_days' => ['required', 'integer', 'min:0'],
            'contact_email' => ['required', 'email', 'max:191'],
            'contact_phone' => ['nullable', 'string', 'max:60'],
            'contact_hours' => ['nullable', 'string', 'max:120'],
            'contact_address' => ['nullable', 'string', 'max:500'],
        ]);

        $current = Setting::where('key', 'site')->first()?->value ?? config('demo.site', []);

        $updated = array_replace_recursive($current, [
            'name' => $validated['name'],
            'tagline' => $validated['tagline'] ?? ($current['tagline'] ?? ''),
            'currency' => [
                'code' => strtoupper($validated['currency_code']),
                'symbol' => $validated['currency_symbol'],
            ],
            'announcement' => [
                'enabled' => $request->boolean('announcement_enabled'),
                'message' => $validated['announcement_message'] ?? '',
                'link' => $validated['announcement_link'] ?? '/shop',
                'link_text' => $validated['announcement_link_text'] ?? 'Shop Now',
            ],
            'shipping' => [
                'free_shipping_threshold' => (float) $validated['free_shipping_threshold'],
                'standard_rate' => (float) $validated['standard_rate'],
                'express_rate' => (float) $validated['express_rate'],
            ],
            'shipping_rates' => [
                'standard' => [
                    'cost' => (float) $validated['standard_rate'],
                    'free_threshold' => (float) $validated['free_shipping_threshold'],
                ],
                'express' => [
                    'cost' => (float) $validated['express_rate'],
                ],
            ],
            'returns' => [
                'window_days' => (int) $validated['returns_window_days'],
            ],
            'contact' => [
                'email' => $validated['contact_email'],
                'phone' => $validated['contact_phone'] ?? '',
                'hours' => $validated['contact_hours'] ?? '',
                'address' => $validated['contact_address'] ?? '',
            ],
        ]);

        Setting::updateOrCreate(
            ['key' => 'site'],
            ['group' => 'storefront', 'value' => $updated]
        );

        Cache::forget('site_settings');
        config(['demo.site' => $updated]);
        View::share('currencySymbol', $updated['currency']['symbol']);
        View::share('currencyCode', $updated['currency']['code']);

        return redirect()->back()->with('success', 'Store settings updated successfully.');
    }
}
