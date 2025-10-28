<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Repositories\Contracts\DistrictRepository;
use App\Services\Api\CheckoutServiceApi;
use Faker\Provider\Base;
use Illuminate\Http\Request;

class CheckoutApiController extends BaseApiController
{
    protected $checkoutService;
    public function __construct(CheckoutServiceApi $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }
    public function DistrictList()
    {
        return app(DistrictRepository::class)->districtList();
    }

    public function Calculate(Request $request)
    {
        try {
            return $this->successResponse($this->checkoutService->CheckoutCalculate($request->all()), 'Checkout Calculate');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage());
        }
    }
}
