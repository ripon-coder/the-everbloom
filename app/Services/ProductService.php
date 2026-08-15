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
            // Auto-fill empty meta fields from product name & short description
            if (empty($data['meta_title'])) {
                $data['meta_title'] = $data['name'];
            }
            if (empty($data['meta_description'])) {
                $data['meta_description'] = Str::limit(strip_tags($data['short_description'] ?? $data['description'] ?? ''), 160);
            }

            $isVariantProduct = ($data['product_type'] ?? 'single') === 'variant';
            if ($isVariantProduct) {
                $data['price'] = null;
            }

            $data['is_free_delivery'] = $this->parseBoolean($data['is_free_delivery'] ?? null);
            $data['is_featured'] = $this->parseBoolean($data['is_featured'] ?? null);

            $data = array_merge($data, ["admin_id" => auth()->guard("admin")->id(), "slug" => $slug]);
            $product = $this->productRepository->store($data);

            // Handle product images
            if (isset($data['images']) && is_array($data['images'])) {
                $images = $data['images'];
                $thumbnailIndex = (int) ($data['thumbnail_index'] ?? 0);
                foreach ($images as $index => $image) {
                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        try {
                            if (!$image->isValid()) {
                                Log::error('Invalid image file: ' . $image->getClientOriginalName());
                                continue;
                            }

                            $productImage = $product->images()->create([
                                'is_default' => $index === $thumbnailIndex,
                            ]);

                            $productImage->uploadImage($image, 'product_images');

                            Log::info('Image uploaded successfully: ' . $image->getClientOriginalName());
                        } catch (\Exception $e) {
                            Log::error('Error uploading image: ' . $e->getMessage());
                        }
                    }
                }
            }

            // If Single Product mode is chosen (without variants), save basic data as a single variant row (no attributes)
            if (($data['product_type'] ?? 'single') === 'single') {
                $product->variants()->create([
                    'sell_price'     => $data['price'] ?? 0,
                    'discount_price' => isset($data['simple_discount_price']) && $data['simple_discount_price'] !== '' ? $data['simple_discount_price'] : null,
                    'buying_price'   => $data['simple_buying_price'] ?? 0,
                    'stock'          => $data['simple_stock'] ?? 10,
                    'sku'            => !empty($data['simple_sku']) ? $data['simple_sku'] : 'EVB-' . strtoupper(Str::slug(substr($data['name'], 0, 10))) . '-' . rand(100, 999),
                    'weight'         => $data['simple_weight'] ?? 0,
                    'status'         => 'active',
                ]);
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
            $isVariantProduct = ($data['product_type'] ?? 'single') === 'variant';

            // Update basic product information
            $productData = [
                'brand_id' => $data['brand_id'],
                'category_id' => $data['category_id'],
                "is_free_delivery" => $this->parseBoolean($data['is_free_delivery'] ?? null),
                "is_featured" => $this->parseBoolean($data['is_featured'] ?? null),
                'name' => $data['name'],
                'short_description'=> $data['short_description'] ?? null,
                'description' => $data['description'] ?? null,
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'price' => $isVariantProduct ? null : ($data['price'] ?? null),
                'status' => $data['status'] ?? 'active',
                'product_type' => $data['product_type'] ?? 'single',
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
                $thumbnailIndex = (int) ($data['thumbnail_index'] ?? 0);
                foreach ($data['images'] as $index => $image) {
                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        try {
                            if (!$image->isValid()) {
                                Log::error('Invalid image file: ' . $image->getClientOriginalName());
                                continue;
                            }

                            $productImage = $product->images()->create([
                                'is_default' => $index === $thumbnailIndex,
                            ]);

                            $productImage->uploadImage($image, 'product_images');

                            Log::info('Image uploaded successfully: ' . $image->getClientOriginalName());
                        } catch (\Exception $e) {
                            Log::error('Error uploading image: ' . $e->getMessage());
                        }
                    }
                }
            } elseif (isset($data['default_image_id'])) {
                $product->images()->update(['is_default' => false]);
                $product->images()->where('id', $data['default_image_id'])->update(['is_default' => true]);
            }

            // If Single Product mode is chosen (without variants), update or create basic data as a single variant row (no attributes)
            if (($data['product_type'] ?? 'single') === 'single') {
                // Retrieve the existing single variant (the one with no variantAttributes) or first variant
                $existingVariant = $product->variants()->doesntHave('variantAttributes')->first() ?? $product->firstActiveVariant;

                $singleData = [
                    'sell_price'     => $data['price'] ?? 0,
                    'discount_price' => isset($data['simple_discount_price']) && $data['simple_discount_price'] !== '' ? $data['simple_discount_price'] : null,
                    'buying_price'   => $data['simple_buying_price'] ?? 0,
                    'stock'          => $data['simple_stock'] ?? 10,
                    'sku'            => !empty($data['simple_sku']) ? $data['simple_sku'] : ($existingVariant?->sku ?? ('EVB-' . strtoupper(Str::slug(substr($data['name'], 0, 10))) . '-' . rand(100, 999))),
                    'weight'         => $data['simple_weight'] ?? 0,
                    'status'         => 'active',
                ];

                if ($existingVariant) {
                    $existingVariant->update($singleData);
                } else {
                    $product->variants()->create($singleData);
                }
            } else {
                // When switching to variant type, delete the plain single-type variant row (no attributes)
                $product->variants()->doesntHave('variantAttributes')->delete();
            }

            DB::commit();

            return redirect()->route('admin.products.edit', $id)
                ->with('success', 'Product updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating product: ' . $e->getMessage());
        }
    }

    public function show($product)
    {
        return $product->load([
            'brand:id,name',
            'category:id,name',
            'variants:id,product_id,sku,buying_price,sell_price,discount_price,discount_amount,stock,weight,status',
            'variants.variantAttributes:id,product_variant_id,attribute_id,attribute_value_id',
            'variants.variantAttributes.attribute:id,name',
            'variants.variantAttributes.attributeValue:id,value',
            'images',
            'variants.images'
        ]);
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            // Get the product first before deletion
            $product = \App\Models\Product::findOrFail($id);

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

            // Now delete the product
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
            // Get the product first before deletion
            $product = \App\Models\Product::withTrashed()->findOrFail($id);

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

            // Now force delete the product
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

    private function parseBoolean($value): int
    {
        if (is_array($value)) {
            $value = end($value);
        }
        return in_array($value, [1, '1', 'on', true, 'true'], true) ? 1 : 0;
    }
}
