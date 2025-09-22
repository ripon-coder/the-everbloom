<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductVariantImageRequest;
use App\Http\Requests\UpdateProductVariantImageRequest;
use App\Models\ProductVariantImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductVariantImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $variantImages = ProductVariantImage::with(['productVariant.product'])
            ->latest()
            ->paginate(10);

        return view("admin.product-variant-images.index", compact('variantImages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productVariants = ProductVariant::active()->get();
        
        return view("admin.product-variant-images.create", compact('productVariants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductVariantImageRequest $request)
    {
        try {
            $path = $request->file('image')->store('product-variants', 'public');
            
            ProductVariantImage::create([
                'product_variant_id' => $request->product_variant_id,
                'image' => $path,
                'is_default' => $request->is_default ?? false,
            ]);

            return redirect()->route('admin.product-variant-images.index')
                ->with('success', 'Product variant image created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating product variant image: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductVariantImage $productVariantImage)
    {
        $productVariantImage->load(['productVariant.product']);
        
        return view("admin.product-variant-images.show", compact('productVariantImage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductVariantImage $productVariantImage)
    {
        $productVariants = ProductVariant::active()->get();
        $productVariantImage->load(['productVariant.product']);
        
        return view("admin.product-variant-images.edit", compact('productVariantImage', 'productVariants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductVariantImageRequest $request, ProductVariantImage $productVariantImage)
    {
        try {
            $data = [
                'is_default' => $request->is_default ?? false,
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                Storage::disk('public')->delete($productVariantImage->image);
                
                // Store new image
                $path = $request->file('image')->store('product-variants', 'public');
                $data['image'] = $path;
            }

            $productVariantImage->update($data);

            return redirect()->route('admin.product-variant-images.index')
                ->with('success', 'Product variant image updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating product variant image: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductVariantImage $productVariantImage)
    {
        try {
            // Delete image file
            Storage::disk('public')->delete($productVariantImage->image);
            
            // Delete database record
            $productVariantImage->delete();

            return redirect()->route('admin.product-variant-images.index')
                ->with('success', 'Product variant image deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting product variant image: ' . $e->getMessage());
        }
    }

    /**
     * Set image as default
     */
    public function setDefault(ProductVariantImage $productVariantImage)
    {
        try {
            // Remove default from all other images of the same variant
            ProductVariantImage::where('product_variant_id', $productVariantImage->product_variant_id)
                ->where('id', '!=', $productVariantImage->id)
                ->update(['is_default' => false]);

            // Set this image as default
            $productVariantImage->update(['is_default' => true]);

            return redirect()->back()
                ->with('success', 'Image set as default successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error setting image as default: ' . $e->getMessage());
        }
    }
}
