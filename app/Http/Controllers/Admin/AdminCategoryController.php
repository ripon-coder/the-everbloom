<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    public function index()
    {
        // BACKEND: Category::withCount('products')->get()
        $categories = config('demo.categories') ?? [];
        $products = config('demo.products') ?? [];

        // Attach dynamic product count to each category
        foreach ($categories as &$cat) {
            $cat['count'] = count(array_filter($products, fn($p) => ($p['category_slug'] ?? '') === ($cat['slug'] ?? '')));
        }

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function edit($slug)
    {
        $categories = config('demo.categories') ?? [];
        $category = collect($categories)->firstWhere('slug', $slug) ?? $categories[0];
        return view('admin.categories.create', ['category' => $category, 'isEdit' => true]);
    }

    public function store(Request $request)
    {
        // BACKEND: Validate and save category
        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function update(Request $request, $slug)
    {
        // BACKEND: Update category
        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy($slug)
    {
        // BACKEND: Delete category
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
