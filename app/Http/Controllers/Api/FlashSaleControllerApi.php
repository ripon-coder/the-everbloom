<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\FlashSaleProductsResource;
use App\Repositories\Contracts\FlashSaleRepository;
use Faker\Provider\Base;

class FlashSaleControllerApi extends BaseApiController
{
    private $flashSaleRepository;
    public function __construct(FlashSaleRepository $flashSaleRepository)
    {
        $this->flashSaleRepository = $flashSaleRepository;
    }
    public function FlashSaleProducts(Request $request)
    {

        $flashSaleSlug = $request->input('flash_sale_slug');
        $current_page = $request->input('current_page', 1);
        $perPage = $request->input('per_page', 10);
        $flashSaleProducts = $this->flashSaleRepository->getFlashSaleProducts($request->all());
        $data = [
            'flash_sale' => $flashSaleProducts['flash_sale'],
            'products' => FlashSaleProductsResource::collection($flashSaleProducts['products']),
            'pagination' => $flashSaleProducts['pagination'],
        ];
        if($flashSaleProducts['products']){
            return $this->successResponse($data, 'Flash Sale Products retrieved successfully.');
        }

    }
}
