<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\FlashSaleProductsResource;
use App\Repositories\Contracts\FlashSaleRepository;

class FlashSaleControllerApi extends Controller
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
        $flashSaleProducts = $this->flashSaleRepository->getFlashSaleProducts($flashSaleSlug, $current_page, $perPage);
        return [
            'flash_sale' => $flashSaleProducts['flash_sale'],
            'products' => FlashSaleProductsResource::collection($flashSaleProducts['products']),
            'total' => $flashSaleProducts['total'],
        ];
    }
}
