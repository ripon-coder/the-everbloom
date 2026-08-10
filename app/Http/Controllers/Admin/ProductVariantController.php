<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\VariantAttribute;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of products where product_type = 'variant'.
     */
    public function index(Request $request): View
    {
        $query = Product::where('product_type', 'variant')
            ->with([
                'brand:id,name',
                'category:id,name',
                'firstImage',
                'anyImage',
                'variants' => function($q) {
                    $q->with(['variantAttributes.attribute', 'variantAttributes.attributeValue']);
                }
            ]);

        if ($request->has('search') && !empty($request->search)) {
            $search = trim($request->get('search'));
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhereHas('brand', function($b) use ($search) {
                      $b->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('category', function($c) use ($search) {
                      $c->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('variants', function($v) use ($search) {
                      $v->where('sku', 'LIKE', "%{$search}%");
                  });
            });
        }

        $products = $query->latest()->paginate(20);

        return view('admin.variants.index', compact('products'));
    }

    /**
     * Display the specified product and its variants with "Add Variant" functionality.
     */
    public function show(int $id): View
    {
        $product = Product::with([
            'brand:id,name',
            'category:id,name',
            'images',
            'variants' => function($q) {
                $q->with([
                    'variantAttributes.attribute',
                    'variantAttributes.attributeValue',
                    'images'
                ]);
            }
        ])->findOrFail($id);

        $attributes = Attribute::with(['attributeValues' => function($q) {
            $q->active();
        }])->active()->get();

        return view('admin.variants.show', compact('product', 'attributes'));
    }

    /**
     * Store a new variant for the specified product.
     */
    public function storeVariant(Request $request, int $productId): RedirectResponse
    {
        $product = Product::findOrFail($productId);

        $request->validate([
            'sku' => 'nullable|string|max:255|unique:product_variants,sku',
            'sell_price' => 'required|numeric|min:0',
            'buying_price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:active,inactive',
            'variant_attributes' => 'nullable|array',
            'variant_attributes.*.attribute_id' => 'required_with:variant_attributes|exists:attributes,id',
            'variant_attributes.*.attribute_value_id' => 'required_with:variant_attributes|exists:attribute_values,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $sku = $request->filled('sku') ? $request->sku : 'EVB-' . strtoupper(Str::slug(substr($product->name, 0, 10))) . '-' . rand(1000, 9999);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => $sku,
            'buying_price' => $request->buying_price ?? 0,
            'sell_price' => $request->sell_price,
            'discount_price' => $request->discount_price,
            'stock' => $request->stock,
            'weight' => $request->weight ?? 0,
            'status' => $request->status,
        ]);

        if ($request->has('variant_attributes') && is_array($request->variant_attributes)) {
            foreach ($request->variant_attributes as $attrData) {
                if (!empty($attrData['attribute_id']) && !empty($attrData['attribute_value_id'])) {
                    VariantAttribute::create([
                        'product_variant_id' => $variant->id,
                        'attribute_id' => $attrData['attribute_id'],
                        'attribute_value_id' => $attrData['attribute_value_id'],
                    ]);
                }
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $variantImage = $variant->images()->create();
                    $variantImage->uploadImage($image, 'variant_images');
                }
            }
        }

        return redirect()->route('admin.variants.show', $product->id)->with('success', 'Variant added successfully.');
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
            'variant_attributes' => 'nullable|array',
            'variant_attributes.*.attribute_id' => 'required_with:variant_attributes|exists:attributes,id',
            'variant_attributes.*.attribute_value_id' => 'required_with:variant_attributes|exists:attribute_values,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $variant = ProductVariant::findOrFail($id);
        $variant->update($request->only([
            'sku', 'sell_price', 'buying_price', 'discount_price', 'stock', 'weight', 'status'
        ]));

        if ($request->has('variant_attributes') && is_array($request->variant_attributes)) {
            $variant->variantAttributes()->delete();
            foreach ($request->variant_attributes as $attrData) {
                if (!empty($attrData['attribute_id']) && !empty($attrData['attribute_value_id'])) {
                    VariantAttribute::create([
                        'product_variant_id' => $variant->id,
                        'attribute_id' => $attrData['attribute_id'],
                        'attribute_value_id' => $attrData['attribute_value_id'],
                    ]);
                }
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $variantImage = $variant->images()->create();
                    $variantImage->uploadImage($image, 'variant_images');
                }
            }
        }

        return redirect()->route('admin.variants.show', $variant->product_id)->with('success', 'Variant updated successfully.');
    }

    /**
     * Remove the specified variant from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $variant = ProductVariant::findOrFail($id);
        $productId = $variant->product_id;
        $variant->delete();

        return back()->with('success', 'Variant deleted successfully.');
    }
}
