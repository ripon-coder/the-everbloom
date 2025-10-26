<?php

namespace App\Http\Controllers\Api;

use App\Services\Api\ProductsServiceApi;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Http\Resources\SingleProductVariantResource;
use App\Repositories\Contracts\ProductRepository;

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

    public function ShopAttribute(Request $request){
        $data = $this->productService->ShopAttribute($request->all());
        return $this->successResponse($data, "Category By Attribute");
    }

    public function Product(Request $request){
        return $this->productService->product($request->all());
    }

    public function Variant(Request $request){
        $data =  app(ProductRepository::class)->Variant($request->all());
        if(count($data) > 0){
            return SingleProductVariantResource::collection($data);
        }else{
            return [];
        }
    }
    public function Variants(Request $request){
        $ids = $request->ids;
        $data = app(ProductRepository::class)->getVariants($ids,['id','product_id','sku','sell_price','discount_price','weight','stock','status']);
        return $this->successResponse($data, "variants");
    }
}
