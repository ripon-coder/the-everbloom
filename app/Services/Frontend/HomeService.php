<?php

namespace App\Services\Frontend;

use App\Models\Product;
use Illuminate\Support\Collection;

class HomeService
{
    /**
     * Get data for the home page.
     *
     * @return array
     */
    public function getHomeData(): array
    {
        $flashSale = $this->getActiveFlashSale();
        
        return [
            'sliders' => \App\Models\Slider::where('status', true)->orderBy('sort_order')->get(),
            'featuredCategories' => \App\Models\Category::active()->where('is_featured', true)->with('media')->get(),
            'featuredProducts' => $this->transformProducts($this->getFeaturedProducts()),
            'bestSellingProducts' => $this->transformProducts($this->getBestSellingProducts()),
            'newArrivals' => $this->transformProducts($this->getNewArrivals()),
            'flashSale' => $flashSale,
            'campaignProducts' => $flashSale ? $this->transformFlashSaleProducts($flashSale->products) : collect(),
        ];
    }

    /**
     * Get featured products.
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeaturedProducts(int $limit = 12): Collection
    {
        return Product::active()
            ->where('is_featured', true)
            ->with(['firstImage.media', 'category', 'firstActiveVariant', 'flashSales' => function ($query) { $query->active(); }])
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get best selling products based on popular scope.
     *
     * @param int $limit
     * @return Collection
     */
    public function getBestSellingProducts(int $limit = 12): Collection
    {
        return Product::active()
            ->popular()
            ->with(['firstImage.media', 'category', 'firstActiveVariant', 'flashSales' => function ($query) { $query->active(); }])
            ->take($limit)
            ->get();
    }

    /**
     * Get new arrival products.
     *
     * @param int $limit
     * @return Collection
     */
    public function getNewArrivals(int $limit = 12): Collection
    {
        return Product::active()
            ->latest()
            ->with(['firstImage.media', 'category', 'firstActiveVariant', 'flashSales' => function ($query) { $query->active(); }])
            ->take($limit)
            ->get();
    }

    /**
     * Get the active flash sale.
     *
     * @return \App\Models\FlashSale|null
     */
    public function getActiveFlashSale()
    {
        return \App\Models\FlashSale::active()
            ->with(['products' => function ($query) {
                $query->active()->with(['firstImage.media', 'category', 'firstActiveVariant']);
            }])
            ->latest()
            ->first();
    }
    /**
     * Transform a collection of products into plain data objects for the view.
     *
     * @param Collection $products
     * @return Collection
     */
    protected function transformProducts(\Illuminate\Support\Collection $products): \Illuminate\Support\Collection
    {
        return $products->map(function ($product) {
            $basePrice = $product->display_price;
            $oldPrice = $product->display_old_price;

            if ($product->relationLoaded('flashSales') && $product->flashSales->isNotEmpty()) {
                $flashSale = $product->flashSales->first();
                $discountPrice = $flashSale->pivot->discount_price;
                $discountPercentage = $flashSale->pivot->discount_percentage;
                
                $oldPrice = $basePrice;
                $price = $discountPrice 
                    ? ($basePrice - $discountPrice) 
                    : ($basePrice - ($basePrice * ($discountPercentage / 100)));
                $price = max(0, $price);
            } else {
                $price = $basePrice;
            }

            return (object) [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $price,
                'old_price' => $oldPrice,
                'badge' => $product->badge ?? null,
                'img' => $product->firstImage ? $product->firstImage->getImageUrl() : asset('images/image1.jpg'),
                'slug' => $product->slug,
                'category_name' => $product->category ? $product->category->name : null,
                'short_description' => $product->short_description,
            ];
        });
    }

    /**
     * Transform a collection of flash sale products into plain data objects for the view.
     *
     * @param Collection $products
     * @return Collection
     */
    protected function transformFlashSaleProducts(\Illuminate\Support\Collection $products): \Illuminate\Support\Collection
    {
        return $products->map(function ($product) {
            $basePrice = $product->display_price;
            $discountPrice = $product->pivot->discount_price;
            $discountPercentage = $product->pivot->discount_percentage;
            
            $price = $discountPrice 
                ? ($basePrice - $discountPrice) 
                : ($basePrice - ($basePrice * ($discountPercentage / 100)));
            $oldPrice = $basePrice;
            
            return (object) [
                'id' => $product->id,
                'name' => $product->name,
                'price' => max(0, $price), // Ensure price doesn't go below 0
                'old_price' => $oldPrice,
                'discount_percentage' => $discountPercentage ?? round((($oldPrice - max(0, $price)) / $oldPrice) * 100),
                'badge' => $product->badge ?? null,
                'img' => $product->firstImage ? $product->firstImage->getImageUrl() : asset('images/image1.jpg'),
                'slug' => $product->slug,
                'category_name' => $product->category ? $product->category->name : null,
                'short_description' => $product->short_description,
            ];
        });
    }
}
