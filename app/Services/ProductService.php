<?php
namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Contracts\ProductRepository;

class ProductService
{

    protected $productRepository;
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    
    public function store(array $data)
    {
        try {
            DB::beginTransaction();
            $slug = Str::slug($data['name']);
            $originalSlug = $slug;
            $counter = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $data = array_merge(["slug" => $slug], $data);
            $product = $this->productRepository->store($data);

            // Handle product images
            if (isset($data['images']) && is_array($data['images'])) {
                $images = $data['images'];
                foreach ($images as $index => $image) {
                    // Check if the image is a valid UploadedFile instance
                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        try {
                            // Validate the image file
                            if (!$image->isValid()) {
                                Log::error('Invalid image file: ' . $image->getClientOriginalName());
                                continue;
                            }

                            $productImage = $product->images()->create([
                                'is_default' => $index === 0,
                            ]);

                            $productImage->addMedia($image)
                                ->toMediaCollection('product_images');
                            
                            Log::info('Image uploaded successfully: ' . $image->getClientOriginalName());
                        } catch (\Exception $e) {
                            Log::error('Error uploading image: ' . $e->getMessage());
                            // Continue with other images even if one fails
                        }
                    } else {
                        Log::warning('Invalid image data type for image index: ' . $index);
                    }
                }
            }

            // Handle product variants
            if (isset($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    $variant = $product->variants()->create([
                        'sku' => $variantData['sku'],
                        'buying_price' => $variantData['buying_price'],
                        'sell_price' => $variantData['sell_price'],
                        'discount_price' => $variantData['discount_price'],
                        'discount_amount' => "25",
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
                            // Check if the image is a valid UploadedFile instance
                            if ($image instanceof \Illuminate\Http\UploadedFile) {
                                try {
                                    // Validate the image file
                                    if (!$image->isValid()) {
                                        Log::error('Invalid variant image file: ' . $image->getClientOriginalName() . ' for variant: ' . $variantData['sku']);
                                        continue;
                                    }

                                    $variantImage = $variant->images()->create([
                                        'is_default' => $index === 0,
                                    ]);

                                    $variantImage->addMedia($image)
                                        ->toMediaCollection('variant_images');
                                    
                                    Log::info('Variant image uploaded successfully: ' . $image->getClientOriginalName() . ' for variant: ' . $variantData['sku']);
                                } catch (\Exception $e) {
                                    Log::error('Error uploading variant image: ' . $e->getMessage() . ' for variant: ' . $variantData['sku']);
                                    // Continue with other images even if one fails
                                }
                            } else {
                                Log::warning('Invalid variant image data type for image index: ' . $index . ' for variant: ' . $variantData['sku']);
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
                ->with('danger', 'Error creating product: ' . $e->getMessage());
        }

    }
    
    public function update(int $id, array $data)
    {
        DB::beginTransaction();

        try {
            // Update basic product information
            $productData = [
                'brand_id' => $data['brand_id'],
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'status' => $data['status'],
            ];
            
            $product = $this->productRepository->update($id, $productData);

            // Handle product images
            if (isset($data['images']) && is_array($data['images']) && !empty($data['images'])) {
                // Delete existing images
                foreach ($product->images as $image) {
                    $image->clearMediaCollection('product_images');
                    $image->delete();
                }

                // Add new images
                foreach ($data['images'] as $index => $image) {
                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        try {
                            if (!$image->isValid()) {
                                Log::error('Invalid image file: ' . $image->getClientOriginalName());
                                continue;
                            }

                            $productImage = $product->images()->create([
                                'is_default' => $index === 0,
                            ]);

                            $productImage->addMedia($image)
                                ->toMediaCollection('product_images');
                            
                            Log::info('Image uploaded successfully: ' . $image->getClientOriginalName());
                        } catch (\Exception $e) {
                            Log::error('Error uploading image: ' . $e->getMessage());
                        }
                    }
                }
            }

            // Handle product variants
            if (isset($data['variants']) && is_array($data['variants']) && !empty($data['variants'])) {
                // Get existing variant SKUs to track which ones to keep
                $existingVariantSkus = $product->variants()->pluck('sku')->toArray();
                $newVariantSkus = collect($data['variants'])->pluck('sku')->toArray();
                
                // Handle deleted variants (marked for deletion)
                if (isset($data['delete_variants']) && is_array($data['delete_variants'])) {
                    foreach ($data['delete_variants'] as $variantId) {
                        $variant = $product->variants()->find($variantId);
                        if ($variant) {
                            // Delete variant images
                            foreach ($variant->images as $image) {
                                $image->clearMediaCollection('variant_images');
                                $image->delete();
                            }
                            // Delete variant attributes
                            $variant->variantAttributes()->delete();
                            $variant->delete();
                        }
                    }
                }
                
                // Delete variants that are no longer present
                $variantsToDelete = array_diff($existingVariantSkus, $newVariantSkus);
                foreach ($variantsToDelete as $skuToDelete) {
                    $variant = $product->variants()->where('sku', $skuToDelete)->first();
                    if ($variant) {
                        // Delete variant images
                        foreach ($variant->images as $image) {
                            $image->clearMediaCollection('variant_images');
                            $image->delete();
                        }
                        // Delete variant attributes
                        $variant->variantAttributes()->delete();
                        $variant->delete();
                    }
                }

                // Update or create variants
                foreach ($data['variants'] as $variantData) {
                    // Check if variant ID is provided (for existing variants)
                    if (!empty($variantData['id'])) {
                        $variant = $product->variants()->find($variantData['id']);
                        if ($variant) {
                            $variant->update([
                                'sku' => $variantData['sku'],
                                'buying_price' => $variantData['buying_price'] ?? 0,
                                'sell_price' => $variantData['sell_price'] ?? $product->price,
                                'discount_price' => $variantData['discount_price'] ?? 0,
                                'stock' => $variantData['stock'],
                                'status' => $variantData['status'] ?? 'active',
                            ]);
                        } else {
                            // If ID is provided but variant not found, create new variant
                            $variant = $product->variants()->create([
                                'sku' => $variantData['sku'],
                                'buying_price' => $variantData['buying_price'] ?? 0,
                                'sell_price' => $variantData['sell_price'] ?? $product->price,
                                'discount_price' => $variantData['discount_price'] ?? 0,
                                'stock' => $variantData['stock'],
                                'status' => $variantData['status'] ?? 'active',
                            ]);
                        }
                    } else {
                        // For new variants, use updateOrCreate with SKU
                        $variant = $product->variants()->updateOrCreate(
                            ['sku' => $variantData['sku']],
                            [
                                'buying_price' => $variantData['buying_price'] ?? 0,
                                'sell_price' => $variantData['sell_price'] ?? $product->price,
                                'discount_price' => $variantData['discount_price'] ?? 0,
                                'stock' => $variantData['stock'],
                                'status' => $variantData['status'] ?? 'active',
                            ]
                        );
                    }

                    // Handle variant attributes
                    if (isset($variantData['attributes']) && is_array($variantData['attributes'])) {
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
                        // Handle deleted variant images
                        if (isset($variantData['delete_images']) && is_array($variantData['delete_images'])) {
                            foreach ($variantData['delete_images'] as $imageId) {
                                $image = $variant->images()->find($imageId);
                                if ($image) {
                                    $image->clearMediaCollection('variant_images');
                                    $image->delete();
                                }
                            }
                        }
                        
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
                                    try {
                                        if (!$image->isValid()) {
                                            Log::error('Invalid variant image file: ' . $image->getClientOriginalName() . ' for variant: ' . $variantData['sku']);
                                            continue;
                                        }

                                        $variantImage = $variant->images()->create([
                                            'is_default' => $index === 0,
                                        ]);

                                        $variantImage->addMedia($image)
                                            ->toMediaCollection('variant_images');
                                        
                                        Log::info('Variant image uploaded successfully: ' . $image->getClientOriginalName() . ' for variant: ' . $variantData['sku']);
                                    } catch (\Exception $e) {
                                        Log::error('Error uploading variant image: ' . $e->getMessage() . ' for variant: ' . $variantData['sku']);
                                    }
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
    
    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            $product = $this->productRepository->destroy($id);

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

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error deleting product: ' . $e->getMessage());
        }
    }
    
    public function restore(int $id)
    {
        try {
            $this->productRepository->restore($id);

            return redirect()->route('admin.products.index')
                ->with('success', 'Product restored successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error restoring product: ' . $e->getMessage());
        }
    }
    
    public function forceDelete(int $id)
    {
        DB::beginTransaction();

        try {
            $product = $this->productRepository->forceDelete($id);

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
