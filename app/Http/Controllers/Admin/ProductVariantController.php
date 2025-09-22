<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductVariantRequest;
use App\Http\Requests\UpdateProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use App\Models\ProductVariantImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $variants = ProductVariant::with(['product', 'variantAttributes.attribute', 'variantAttributes.attributeValue', 'images'])
            ->latest()
            ->paginate(10);

        return view("admin.product-variants.index", compact('variants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::active()->get();
        
        return view("admin.product-variants.create", compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductVariantRequest $request)
    {
        DB::beginTransaction();

        try {
            $variant = ProductVariant::create([
                'product_id' => $request->product_id,
                'sku' => $request->sku,
                'price' => $request->price,
                'stock' => $request->stock,
                'status' => $request->status,
            ]);

            // Handle variant attributes
            if ($request->has('attributes')) {
                foreach ($request->attributes as $attributeData) {
                    $variant->variantAttributes()->create([
                        'attribute_id' => $attributeData['attribute_id'],
                        'attribute_value_id' => $attributeData['attribute_value_id'],
                    ]);
                }
            }

            // Handle variant images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('product-variants', 'public');
                    
                    $variant->images()->create([
                        'image' => $path,
                        'is_default' => $index === 0,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.product-variants.index')
                ->with('success', 'Product variant created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating product variant: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductVariant $productVariant)
    {
        $productVariant->load(['product', 'variantAttributes.attribute', 'variantAttributes.attributeValue', 'images']);
        
        return view("admin.product-variants.show", compact('productVariant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductVariant $productVariant)
    {
        $products = Product::active()->get();
        $productVariant->load(['product', 'variantAttributes.attribute', 'variantAttributes.attributeValue', 'images']);
        
        return view("admin.product-variants.edit", compact('productVariant', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductVariantRequest $request, ProductVariant $productVariant)
    {
        DB::beginTransaction();

        try {
            $productVariant->update([
                'sku' => $request->sku,
                'price' => $request->price,
                'stock' => $request->stock,
                'status' => $request->status,
            ]);

            // Handle variant attributes
            if ($request->has('attributes')) {
                // Delete existing attributes
                $productVariant->variantAttributes()->delete();

                // Add new attributes
                foreach ($request->attributes as $attributeData) {
                    $productVariant->variantAttributes()->create([
                        'attribute_id' => $attributeData['attribute_id'],
                        'attribute_value_id' => $attributeData['attribute_value_id'],
                    ]);
                }
            }

            // Handle variant images
            if ($request->hasFile('images')) {
                // Delete existing images
                foreach ($productVariant->images as $image) {
                    Storage::disk('public')->delete($image->image);
                    $image->delete();
                }

                // Add new images
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('product-variants', 'public');
                    
                    $productVariant->images()->create([
                        'image' => $path,
                        'is_default' => $index === 0,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.product-variants.index')
                ->with('success', 'Product variant updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating product variant: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductVariant $productVariant)
    {
        DB::beginTransaction();

        try {
            // Delete variant images
            foreach ($productVariant->images as $image) {
                Storage::disk('public')->delete($image->image);
                $image->delete();
            }

            // Delete variant attributes
            $productVariant->variantAttributes()->delete();

            // Soft delete variant
            $productVariant->delete();

            DB::commit();

            return redirect()->route('admin.product-variants.index')
                ->with('success', 'Product variant deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error deleting product variant: ' . $e->getMessage());
        }
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        $variant = ProductVariant::withTrashed()->findOrFail($id);
        $variant->restore();

        return redirect()->route('admin.product-variants.index')
            ->with('success', 'Product variant restored successfully.');
    }

    /**
     * Force delete the specified resource from storage.
     */
    public function forceDelete($id)
    {
        $variant = ProductVariant::withTrashed()->findOrFail($id);
        
        DB::beginTransaction();

        try {
            // Delete variant images permanently
            foreach ($variant->images as $image) {
                Storage::disk('public')->delete($image->image);
                $image->forceDelete();
            }

            // Delete variant attributes permanently
            $variant->variantAttributes()->forceDelete();

            // Force delete variant
            $variant->forceDelete();

            DB::commit();

            return redirect()->route('admin.product-variants.index')
                ->with('success', 'Product variant permanently deleted.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error permanently deleting product variant: ' . $e->getMessage());
        }
    }
}
