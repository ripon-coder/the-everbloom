<?php
namespace App\Services\Api;


use App\Http\Resources\ProductResource;
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
        $productShop = $this->productRepository->shopProduct($data);
        $outData['pagination'] = $productShop['pagination'];
        $outData['products'] = ProductResource::collection($productShop['products']);
        return $outData;
    }
    public function ShopCategoryBrand(array $data)
    {
        return $this->productRepository->ShopCategoryBrand($data);
    }

    public function ShopAttribute(array $data)
    {
        return $this->productRepository->ShopAttribute($data);
    }
}