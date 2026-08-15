<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;

class SiteSettingController extends Controller
{
    public function index()
    {
        $setting = SiteSetting::first();
        return view('admin.site-settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'site_favicon' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,ico,webp|max:1024',
            'site_email' => 'nullable|email|max:255',
            'site_phone' => 'nullable|string|max:20',
            'site_address' => 'nullable|string',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'footer_text' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $setting = SiteSetting::firstOrCreate(['id' => 1]);
        $data = $request->except(['site_logo', 'site_favicon']);

        if ($request->hasFile('site_logo')) {
            $setting->uploadImage($request->file('site_logo'), 'site', 'site_logo', 'backblaze', false);
            $data['site_logo'] = $setting->site_logo;
        }

        if ($request->hasFile('site_favicon')) {
            $file = $request->file('site_favicon');
            // If it's standard raster/vector image, process via uploadImage, otherwise store directly if ico
            if (in_array(strtolower($file->getClientOriginalExtension()), ['ico', 'svg'])) {
                if ($setting->site_favicon) {
                    $setting->deleteImage($setting->site_favicon, 'backblaze');
                }
                $faviconPath = $file->store('site', 'backblaze');
                $data['site_favicon'] = $faviconPath;
            } else {
                $setting->uploadImage($file, 'site', 'site_favicon', 'backblaze', false);
                $data['site_favicon'] = $setting->site_favicon;
            }
        }

        $setting->update($data);

        return redirect()->back()->with('success', 'Site settings updated successfully!');
    }
}
