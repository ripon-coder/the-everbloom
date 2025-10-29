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
        $user_id = auth()->guard('sanctum')->id();
        $data = array_merge($request->all(),['user_id' => $user_id]);
        try {
            $order = $this->orderService->createOrder($data);
            return $this->successResponse($order, 'Order created successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create order', 500, $e->getMessage());
        }

    }
}
