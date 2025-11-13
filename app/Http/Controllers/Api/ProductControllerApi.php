<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Services\Api\ProductsServiceApi;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Contracts\ProductRepository;
use App\Http\Resources\SingleProductVariantResource;
use App\Http\Resources\SingleProductVariantResource2;

class ProductControllerApi extends BaseApiController
{
    public $productService;
    public function __construct(ProductsServiceApi $productService)
    {
        $this->productService = $productService;
    }
    public function ShopProducts(Request $request)
    {
        $data = $this->productService->ShopProducts($request->all());
        return $this->successResponse($data, "Shop products");
    }
    public function ShopCategoryBrand(Request $request)
    {
        $data = $this->productService->ShopCategoryBrand($request->all());
        return $this->successResponse($data, "Category & Brand");
    }

    public function ShopAttribute(Request $request)
    {
        $data = $this->productService->ShopAttribute($request->all());
        return $this->successResponse($data, "Category By Attribute");
    }

    public function Product(Request $request)
    {
        return $this->productService->product($request->all());
    }

    public function Variant(Request $request)
    {
        $data =  app(ProductRepository::class)->Variant($request->all());
        if (count($data) > 0) {
            return SingleProductVariantResource::collection($data);
        } else {
            return [];
        }
    }
    public function Variants(Request $request)
    {
        $data = app(ProductRepository::class)->getVariantsWithAttribute($request->all());
        return $this->successResponse(SingleProductVariantResource2::collection($data), "Varinat list got");
    }
    public function JustForYouProducts(Request $request)
    {
        $data = $this->productService->JustForYouProducts($request->all());
        return $this->successResponse($data, "Just for you products");
    }
}
