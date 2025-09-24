<?php
namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
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
        DB::beginTransaction();
        $slug = Str::slug($data['name']);
        $originalSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        $data = array_merge(["slug" => $slug], $data);
        //$product = $this->productRepository->store($data);

        // Handle product images

        // if (isset($data['images'])) {
        //     $images = $data['images'];
        //     foreach ($images as $index => $image) {
        //         $productImage = $product->images()->create([
        //             'is_default' => $index === 0,
        //         ]);

        //         $productImage->addMedia($image)
        //             ->toMediaCollection('product_images');
        //     }
        // }
        
        // Handle product variants
        if(isset($data['variants'])) {
            dd($data['variants']);
        }
        DB::commit();

    }
}