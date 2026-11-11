<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function index()
    {
        $site = config('demo.site') ?? [];
        return view('admin.settings.index', compact('site'));
    }

    public function update(Request $request)
    {
        // BACKEND: Validate and save settings to database/cache
        return redirect()->back()->with('success', 'Store settings updated successfully.');
    }
}
