<?php
namespace App\Services\Api;


use App\Http\Resources\ProductResource;
use App\Repositories\Contracts\CategoryRepository;
use App\Repositories\Contracts\ProductRepository;

class ProductsServiceApi
{

    public $productRepository;
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    public function ShopProducts(array $data)
    {
        $page = $data['current_page'] ?? 1;
        $perPage = $data['per_page'] ?? 20;
        $offset = ($page - 1) * $perPage;

        $productShop = $this->productRepository->shopProduct($page, $perPage, $offset, $data);
        $outData['products'] = ProductResource::collection($productShop['products']);
        $total = $productShop['total'] ?? 0;
        $outData['pagination'] = [
            "current_page" => $page,
            "per_page" => $perPage,
            "total" => $total,
            "last_page" => ceil($total / $perPage),
        ];
        return $outData;
    }
    public function ShopCategoryBrand(array $data)
    {

        $categoryRepository = app(CategoryRepository::class);
        $category_slug = $data['category'] ?? null;
        $category_id = null;
        $categoryIds = [];

        if ($category_slug) {
            $category = $categoryRepository->FindBySlug($category_slug);
            $category_id = $category->id ?? null;

            if ($category_id) {
                $categoryIds = $this->productRepository->getCategoryWithSiblings($category_id);
            }
        }
        return $this->productRepository->ShopCategoryBrand($category_id, $categoryIds ?? []);

    }

    public function ShopAttribute(array $data)
    {
        $category_slug = $data['category'] ?? null;
        $category_id = null;
        $categoryIds = [];

        if ($category_slug) {
            $category = app(CategoryRepository::class)->FindBySlug($category_slug);
            $category_id = $category->id ?? null;
            if ($category_id) {
                $categoryIds = $this->productRepository->getCategoryWithSiblings($category_id);
            }
        }
        return $this->productRepository->ShopAttribute($categoryIds);
    }
}