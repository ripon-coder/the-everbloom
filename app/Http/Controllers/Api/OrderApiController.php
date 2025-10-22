<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Api\OrderServiceApi;
use App\Services\OrderService;
use Barryvdh\Debugbar\Controllers\BaseController;
use Faker\Provider\Base;
use Illuminate\Http\Request;

class OrderApiController extends BaseApiController
{
    protected $orderService;
    public function __construct(OrderServiceApi $orderService)
    {
        $this->orderService = $orderService;
    }
    public function CreateOrder(Request $request)
    {
        $user_id = 1;

        return $this->orderService->createOrder(array_merge(
            $request->all(),
            ['user_id' => $user_id]
        ));
    }
}
