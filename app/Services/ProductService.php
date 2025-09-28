<?php
namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
}
