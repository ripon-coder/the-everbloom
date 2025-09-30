<?php

namespace App\Http\Controllers\Api;

use App\Services\Api\ProductsServiceApi;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Controllers\Controller;

class ProductControllerApi extends BaseApiController
{
    public $productService;
    public function __construct(ProductsServiceApi $productService){
        $this->productService = $productService;
    }
    public function ShopProducts(Request $request){
        $data = $this->productService->ShopProducts($request->all());
        return $this->successResponse($data,"Shop products");
       
    }
}
