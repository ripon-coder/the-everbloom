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
use App\Repositories\Contracts\ProductRepository;
use App\Services\ProductService;

class ProductController extends Controller
{
    protected $productRepository;
    protected $productService;
    public function __construct(ProductRepository $productRepository, ProductService $productService){
        $this->productService = $productService;
        $this->productRepository = $productRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data["products"] = $this->productRepository->index();
        return view("admin.products.index", $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->productRepository->create();
        return view("admin.products.create", $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        return $this->productService->store($request->all());
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
                    $image->clearMediaCollection('product_images');
                    $image->delete();
                }

                // Add new images
                foreach ($request->file('images') as $index => $image) {
                    $productImage = $product->images()->create([
                        'is_default' => $index === 0,
                    ]);
                    
                    $productImage->addMedia($image)
                        ->toMediaCollection('product_images');
                }
            }

            // Handle product variants
            if ($request->has('variants') && is_array($request->variants) && !empty($request->variants)) {
                // Get existing variant SKUs to track which ones to keep
                $existingVariantSkus = $product->variants()->pluck('sku')->toArray();
                $newVariantSkus = collect($request->variants)->pluck('sku')->toArray();
                
                // Delete variants that are no longer present
                $variantsToDelete = array_diff($existingVariantSkus, $newVariantSkus);
                foreach ($variantsToDelete as $skuToDelete) {
                    $variant = $product->variants()->where('sku', $skuToDelete)->first();
                    if ($variant) {
                        // Delete variant images
                        if ($variant->images) {
                            foreach ($variant->images as $image) {
                                $image->clearMediaCollection('variant_images');
                                $image->delete();
                            }
                        }
                        // Delete variant attributes
                        $variant->variantAttributes()->delete();
                        $variant->delete();
                    }
                }

                // Update or create variants
                foreach ($request->variants as $variantData) {
                    // Check if variant ID is provided (for existing variants)
                    if (!empty($variantData['id'])) {
                        $variant = $product->variants()->find($variantData['id']);
                        if ($variant) {
                            $variant->update([
                                'sku' => $variantData['sku'],
                                'price' => $variantData['price'] ?? $product->price,
                                'stock' => $variantData['stock'],
                                'status' => $variantData['status'] ?? 'active',
                            ]);
                        } else {
                            // If ID is provided but variant not found, create new variant
                            $variant = $product->variants()->create([
                                'sku' => $variantData['sku'],
                                'price' => $variantData['price'] ?? $product->price,
                                'stock' => $variantData['stock'],
                                'status' => $variantData['status'] ?? 'active',
                            ]);
                        }
                    } else {
                        // For new variants, use updateOrCreate with SKU
                        $variant = $product->variants()->updateOrCreate(
                            ['sku' => $variantData['sku']],
                            [
                                'price' => $variantData['price'] ?? $product->price,
                                'stock' => $variantData['stock'],
                                'status' => $variantData['status'] ?? 'active',
                            ]
                        );
                    }

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
                                $image->clearMediaCollection('variant_images');
                                $image->delete();
                            }

                            // Add new images
                            foreach ($variantData['images'] as $index => $image) {
                                if ($image instanceof \Illuminate\Http\UploadedFile) {
                                    $variantImage = $variant->images()->create([
                                        'is_default' => $index === 0,
                                    ]);
                                    
                                    $variantImage->addMedia($image)
                                        ->toMediaCollection('variant_images');
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
                $image->clearMediaCollection('product_images');
                $image->delete();
            }

            // Delete variant images and variants
            if ($product->variants) {
                foreach ($product->variants as $variant) {
                    if ($variant->images) {
                        foreach ($variant->images as $image) {
                            $image->clearMediaCollection('variant_images');
                            $image->delete();
                        }
                    }
                    $variant->variantAttributes()->delete();
                    $variant->delete();
                }
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
                $image->clearMediaCollection('product_images');
                $image->forceDelete();
            }

            // Delete variant images and variants permanently
            if ($product->variants) {
                foreach ($product->variants as $variant) {
                    if ($variant->images) {
                        foreach ($variant->images as $image) {
                            $image->clearMediaCollection('variant_images');
                            $image->forceDelete();
                        }
                    }
                    $variant->variantAttributes()->forceDelete();
                    $variant->forceDelete();
                }
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
