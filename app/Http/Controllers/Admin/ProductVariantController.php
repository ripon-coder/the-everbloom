<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of all product variants.
     */
    public function index(Request $request): View
    {
        $query = ProductVariant::with([
            'product:id,name',
            'product.firstImage',
            'variantAttributes.attribute',
            'variantAttributes.attributeValue'
        ]);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('sku', 'LIKE', "%{$search}%")
                  ->orWhereHas('product', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
        }

        $variants = $query->latest()->paginate(20);

        return view('admin.variants.index', compact('variants'));
    }

    /**
     * Update the status of the specified variant.
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $variant = ProductVariant::findOrFail($id);
        $variant->status = $variant->status === 'active' ? 'inactive' : 'active';
        $variant->save();

        return back()->with('success', 'Variant status updated successfully.');
    }

    /**
     * Update the stock of the specified variant.
     */
    public function updateStock(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'stock' => 'required|integer|min:0'
        ]);

        $variant = ProductVariant::findOrFail($id);
        $variant->stock = $request->stock;
        $variant->save();

        return back()->with('success', 'Stock updated successfully.');
    }

    /**
     * Show the form for editing the specified variant.
     */
    public function edit(int $id): View
    {
        $variant = ProductVariant::with(['product', 'variantAttributes.attribute', 'variantAttributes.attributeValue'])->findOrFail($id);
        return view('admin.variants.edit', compact('variant'));
    }

    /**
     * Update the specified variant in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'sku' => 'required|string|unique:product_variants,sku,' . $id,
            'sell_price' => 'required|numeric|min:0',
            'buying_price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:active,inactive',
        ]);

        $variant = ProductVariant::findOrFail($id);
        $variant->update($request->all());

        return redirect()->route('admin.variants.index')->with('success', 'Variant updated successfully.');
    }

    /**
     * Remove the specified variant from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $variant = ProductVariant::findOrFail($id);
        $variant->delete();

        return back()->with('success', 'Variant deleted successfully.');
    }
}
