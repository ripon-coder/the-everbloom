<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        // BACKEND: replace with Product::query()->with('category')->paginate(10)
        $products = config('demo.products') ?? [];
        $categories = config('demo.categories') ?? [];

        if ($request->filled('category')) {
            $products = array_filter($products, fn($p) => ($p['category_slug'] ?? '') === $request->category);
        }

        if ($request->filled('search')) {
            $term = strtolower($request->search);
            $products = array_filter($products, fn($p) => 
                str_contains(strtolower($p['name'] ?? ''), $term) || 
                str_contains(strtolower($p['sku'] ?? ''), $term)
            );
        }

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = config('demo.categories') ?? [];
        return view('admin.products.create', compact('categories'));
    }

    public function edit($id)
    {
        $products = config('demo.products') ?? [];
        $product = collect($products)->firstWhere('id', (int) $id) ?? $products[0];
        $categories = config('demo.categories') ?? [];
        return view('admin.products.create', [
            'product' => $product,
            'categories' => $categories,
            'isEdit' => true,
        ]);
    }

    public function store(Request $request)
    {
        // BACKEND: Validate request and persist new product record with media upload
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function update(Request $request, $id)
    {
        // BACKEND: Validate request and update product record
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        // BACKEND: Delete product record
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
