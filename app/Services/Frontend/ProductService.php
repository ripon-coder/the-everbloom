<?php

namespace App\Services\Frontend;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductService
{
    /**
     * Get products for the shop page with filtering and sorting.
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getShopProducts(array $filters): LengthAwarePaginator
    {
        $query = Product::query()->with([
            'firstImage.media', 
            'category', 
            'firstActiveVariant',
            'reviews',
            'flashSales' => function ($query) { $query->active(); }
        ]);

        $paginator = app(\Illuminate\Pipeline\Pipeline::class)
            ->send($query)
            ->through([
                \App\QueryFilters\Products\ActivePipe::class,
                \App\QueryFilters\Products\CategoryPipe::class,
                \App\QueryFilters\Products\SearchPipe::class,
                \App\QueryFilters\Products\PriceMinPipe::class,
                \App\QueryFilters\Products\PriceMaxPipe::class,
                \App\QueryFilters\Products\SortPipe::class,
            ])
            ->thenReturn()
            ->paginate(24)
            ->withQueryString();

        $paginator->setCollection($this->transformProducts($paginator->getCollection()));
        return $paginator;
    }

    /**
     * Get a product by its slug.
     *
     * @param string $slug
     * @return Product
     */
    public function getProductBySlug(string $slug): Product
    {
        return Product::where('slug', $slug)
            ->active()
            ->with([
                'images.media', 
                'variants' => function ($query) {
                    $query->active()->with([
                        'variantAttributes.attribute',
                        'variantAttributes.attributeValue',
                        'images.media'
                    ]);
                },
                'firstImage.media', 
                'firstActiveVariant.variantAttributes.attribute',
                'firstActiveVariant.variantAttributes.attributeValue',
                'category',
                'reviews.user',
                'flashSales' => function ($query) {
                    $query->active();
                }
            ])
            ->firstOrFail();
    }

    /**
     * Get related products for a given product.
     *
     * @param Product $product
     * @param int $limit
     * @return Collection
     */
    public function getRelatedProducts(Product $product, int $limit = 6): Collection
    {
        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['firstImage.media', 'category', 'firstActiveVariant', 'reviews', 'flashSales' => function ($query) { $query->active(); }])
            ->take($limit)
            ->get();

        if ($related->isEmpty()) {
            $related = Product::active()
                ->where('id', '!=', $product->id)
                ->with(['firstImage.media', 'category', 'firstActiveVariant', 'reviews', 'flashSales' => function ($query) { $query->active(); }])
                ->inRandomOrder()
                ->take($limit)
                ->get();
        }

        return $this->transformProducts($related);
    }

    /**
     * Get all active root categories with their children.
     *
     * @return Collection
     */
    public function getActiveCategories(): Collection
    {
        return Category::active()->root()->with(['children', 'media'])->ordered()->get();
    }

    /**
     * Perform a live search for products.
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    public function liveSearch(string $query, int $limit = 8): Collection
    {
        $products = Product::search($query)
            ->query(function ($query) {
                $query->active()->with(['firstImage.media', 'category', 'firstActiveVariant', 'reviews', 'flashSales' => function ($q) { $q->active(); }]);
            })
            ->take($limit)
            ->get();

        return $this->transformProducts($products);
    }
    /**
     * Transform a collection of products into plain data objects for the view.
     *
     * @param Collection $products
     * @return Collection
     */
    protected function transformProducts(Collection $products): Collection
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
                'reviews_count' => $product->reviews_count,
                'avg_rating' => $product->average_rating,
            ];
        });
    }
}
