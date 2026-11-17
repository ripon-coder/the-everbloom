<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\SlugRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AdminSeoController extends Controller
{
    public function index(): View
    {
        $stats = [
            'products_total' => Product::count(),
            'products_missing_meta' => Product::where(function ($q) {
                $q->whereNull('meta_title')->orWhere('meta_title', '')
                    ->orWhereNull('meta_description')->orWhere('meta_description', '');
            })->count(),
            'categories_total' => Category::count(),
            'categories_missing_meta' => Category::where(function ($q) {
                $q->whereNull('meta_title')->orWhere('meta_title', '')
                    ->orWhereNull('meta_description')->orWhere('meta_description', '');
            })->count(),
            'pages_total' => Page::count(),
            'pages_missing_meta' => Page::where(function ($q) {
                $q->whereNull('meta_title')->orWhere('meta_title', '')
                    ->orWhereNull('meta_description')->orWhere('meta_description', '');
            })->count(),
        ];

        $redirects = SlugRedirect::latest('id')->paginate(15);

        return view('admin.seo.index', compact('stats', 'redirects'));
    }

    public function storeRedirect(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'old_path' => ['required', 'string', 'max:255', 'unique:slug_redirects,old_path'],
            'new_path' => ['required', 'string', 'max:255'],
            'status_code' => ['nullable', 'in:301,302'],
        ]);

        $oldPath = '/' . ltrim(trim($validated['old_path']), '/');
        $newPath = str_starts_with(trim($validated['new_path']), 'http')
            ? trim($validated['new_path'])
            : '/' . ltrim(trim($validated['new_path']), '/');

        SlugRedirect::create([
            'old_path' => $oldPath,
            'new_path' => $newPath,
            'status_code' => (int) ($validated['status_code'] ?? 301),
        ]);

        return back()->with('success', 'Slug redirect created.');
    }

    public function destroyRedirect(SlugRedirect $redirect): RedirectResponse
    {
        $redirect->delete();

        return back()->with('success', 'Redirect deleted.');
    }

    public function flushSitemap(): RedirectResponse
    {
        Cache::forget('sitemap_xml');

        return back()->with('success', 'Sitemap cache flushed.');
    }
}

