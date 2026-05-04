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
        $query = Product::active()->with(['firstImage', 'category', 'flashSales' => function ($query) { $query->active(); }]);

        if (!empty($filters['category'])) {
            $query->whereHas('category', function ($q) use ($filters) {
                $q->where('slug', $filters['category']);
            });
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        $sort = $filters['sort'] ?? 'latest';
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->popular();
                break;
            default:
                $query->latest();
                break;
        }

        $paginator = $query->paginate(24)->withQueryString();
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
                'images', 
                'variants.variantAttributes.attribute', 
                'variants.variantAttributes.attributeValue', 
                'variants.images',
                'firstImage', 
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
            ->with(['firstImage', 'category', 'flashSales' => function ($query) { $query->active(); }])
            ->take($limit)
            ->get();

        if ($related->isEmpty()) {
            $related = Product::active()
                ->where('id', '!=', $product->id)
                ->with(['firstImage', 'category', 'flashSales' => function ($query) { $query->active(); }])
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
        return Category::active()->root()->with('children')->ordered()->get();
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
            $price = $product->price;
            $oldPrice = $product->old_price ?? null;

            if ($product->relationLoaded('flashSales') && $product->flashSales->isNotEmpty()) {
                $flashSale = $product->flashSales->first();
                $discountPrice = $flashSale->pivot->discount_price;
                $discountPercentage = $flashSale->pivot->discount_percentage;
                
                $oldPrice = $product->price;
                $price = $discountPrice 
                    ? ($product->price - $discountPrice) 
                    : ($product->price - ($product->price * ($discountPercentage / 100)));
                $price = max(0, $price);
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
}
