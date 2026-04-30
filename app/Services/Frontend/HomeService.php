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
        return [
            'featuredProducts' => $this->transformProducts($this->getFeaturedProducts()),
            'bestSellingProducts' => $this->transformProducts($this->getBestSellingProducts()),
            'newArrivals' => $this->transformProducts($this->getNewArrivals()),
            'campaignProducts' => $this->transformProducts($this->getCampaignProducts()),
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
            ->with(['firstImage', 'category'])
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
            ->with(['firstImage', 'category'])
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
            ->with(['firstImage', 'category'])
            ->take($limit)
            ->get();
    }

    /**
     * Get products for campaigns.
     *
     * @param int $limit
     * @return Collection
     */
    public function getCampaignProducts(int $limit = 3): Collection
    {
        return Product::active()
            ->inRandomOrder()
            ->with(['firstImage', 'category'])
            ->take($limit)
            ->get();
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
            return (object) [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'old_price' => $product->old_price ?? null,
                'badge' => $product->badge ?? null,
                'img' => $product->firstImage ? $product->firstImage->getImageUrl() : asset('images/image1.jpg'),
                'slug' => $product->slug,
                'category_name' => $product->category ? $product->category->name : null,
                'short_description' => $product->short_description,
            ];
        });
    }
}
