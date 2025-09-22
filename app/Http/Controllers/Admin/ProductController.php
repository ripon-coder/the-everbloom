<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['brand', 'category', 'variants'])
            ->latest()
            ->paginate(10);

        return view("admin.products.index", compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brand::active()->get();
        $categories = Category::active()->get();
         $attributes = Attribute::with('attributeValues')->get(); // <-- important
        return view("admin.products.create", compact('brands', 'categories','attributes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        DB::beginTransaction();

        try {
            // Create slug from name
            $slug = Str::slug($request->name);
            
            // Ensure unique slug
            $originalSlug = $slug;
            $counter = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Create product
            $product = Product::create([
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'price' => $request->price,
                'status' => $request->status,
            ]);

            // Handle product images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');
                    
                    $product->images()->create([
                        'image' => $path,
                        'is_default' => $index === 0, // First image is default
                    ]);
                }
            }

            // Handle product variants
            if ($request->has('variants')) {
                foreach ($request->variants as $variantData) {
                    $variant = $product->variants()->create([
                        'sku' => $variantData['sku'],
                        'price' => $variantData['price'] ?? $product->price,
                        'stock' => $variantData['stock'],
                        'status' => $variantData['status'] ?? 'active',
                    ]);

                    // Handle variant attributes
                    if (isset($variantData['attributes'])) {
                        foreach ($variantData['attributes'] as $attributeData) {
                            $variant->variantAttributes()->create([
                                'attribute_id' => $attributeData['attribute_id'],
                                'attribute_value_id' => $attributeData['attribute_value_id'],
                            ]);
                        }
                    }

                    // Handle variant images
                    if (isset($variantData['images']) && is_array($variantData['images'])) {
                        foreach ($variantData['images'] as $index => $image) {
                            if ($image instanceof \Illuminate\Http\UploadedFile) {
                                $path = $image->store('product-variants', 'public');
                                
                                $variant->images()->create([
                                    'image' => $path,
                                    'is_default' => $index === 0,
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating product: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['brand', 'category', 'variants.variantAttributes.attribute', 'variants.variantAttributes.attributeValue', 'images', 'variants.images']);
        
        return view("admin.products.show", compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $brands = Brand::active()->get();
        $categories = Category::active()->get();
        $attributes = Attribute::with('attributeValues')->get();
        $product->load(['brand', 'category', 'variants.variantAttributes.attribute', 'variants.variantAttributes.attributeValue', 'images', 'variants.images']);
        
        return view("admin.products.edit", compact('product', 'brands', 'categories', 'attributes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        DB::beginTransaction();

        try {
            // Update slug if name changed
            if ($request->name !== $product->name) {
                $slug = Str::slug($request->name);
                
                // Ensure unique slug
                $originalSlug = $slug;
                $counter = 1;
                while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                
                $product->slug = $slug;
            }

            // Update product
            $product->update([
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'status' => $request->status,
            ]);

            // Handle product images
            if ($request->hasFile('images')) {
                // Delete existing images
                foreach ($product->images as $image) {
                    Storage::disk('public')->delete($image->image);
                    $image->delete();
                }

                // Add new images
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');
                    
                    $product->images()->create([
                        'image' => $path,
                        'is_default' => $index === 0,
                    ]);
                }
            }

            // Handle product variants
            if ($request->has('variants')) {
                // Get existing variant SKUs to track which ones to keep
                $existingVariantSkus = $product->variants()->pluck('sku')->toArray();
                $newVariantSkus = collect($request->variants)->pluck('sku')->toArray();
                
                // Delete variants that are no longer present
                $variantsToDelete = array_diff($existingVariantSkus, $newVariantSkus);
                foreach ($variantsToDelete as $skuToDelete) {
                    $variant = $product->variants()->where('sku', $skuToDelete)->first();
                    if ($variant) {
                        // Delete variant images
                        foreach ($variant->images as $image) {
                            Storage::disk('public')->delete($image->image);
                            $image->delete();
                        }
                        // Delete variant attributes
                        $variant->variantAttributes()->delete();
                        $variant->delete();
                    }
                }

                // Update or create variants
                foreach ($request->variants as $variantData) {
                    $variant = $product->variants()->updateOrCreate(
                        ['sku' => $variantData['sku']],
                        [
                            'price' => $variantData['price'] ?? $product->price,
                            'stock' => $variantData['stock'],
                            'status' => $variantData['status'] ?? 'active',
                        ]
                    );

                    // Handle variant attributes
                    if (isset($variantData['attributes'])) {
                        // Delete existing attributes
                        $variant->variantAttributes()->delete();

                        // Add new attributes
                        foreach ($variantData['attributes'] as $attributeData) {
                            $variant->variantAttributes()->create([
                                'attribute_id' => $attributeData['attribute_id'],
                                'attribute_value_id' => $attributeData['attribute_value_id'],
                            ]);
                        }
                    }

                    // Handle variant images
                    if (isset($variantData['images']) && is_array($variantData['images'])) {
                        // Only delete and replace images if new ones are uploaded
                        $hasNewImages = false;
                        foreach ($variantData['images'] as $image) {
                            if ($image instanceof \Illuminate\Http\UploadedFile) {
                                $hasNewImages = true;
                                break;
                            }
                        }

                        if ($hasNewImages) {
                            // Delete existing images
                            foreach ($variant->images as $image) {
                                Storage::disk('public')->delete($image->image);
                                $image->delete();
                            }

                            // Add new images
                            foreach ($variantData['images'] as $index => $image) {
                                if ($image instanceof \Illuminate\Http\UploadedFile) {
                                    $path = $image->store('product-variants', 'public');
                                    
                                    $variant->images()->create([
                                        'image' => $path,
                                        'is_default' => $index === 0,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();

        try {
            // Delete product images
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image);
                $image->delete();
            }

            // Delete variant images and variants
            foreach ($product->variants as $variant) {
                foreach ($variant->images as $image) {
                    Storage::disk('public')->delete($image->image);
                    $image->delete();
                }
                $variant->variantAttributes()->delete();
                $variant->delete();
            }

            // Soft delete product
            $product->delete();

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error deleting product: ' . $e->getMessage());
        }
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product restored successfully.');
    }

    /**
     * Force delete the specified resource from storage.
     */
    public function forceDelete($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        
        DB::beginTransaction();

        try {
            // Delete product images permanently
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image);
                $image->forceDelete();
            }

            // Delete variant images and variants permanently
            foreach ($product->variants as $variant) {
                foreach ($variant->images as $image) {
                    Storage::disk('public')->delete($image->image);
                    $image->forceDelete();
                }
                $variant->variantAttributes()->forceDelete();
                $variant->forceDelete();
            }

            // Force delete product
            $product->forceDelete();

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product permanently deleted.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error permanently deleting product: ' . $e->getMessage());
        }
    }
}
