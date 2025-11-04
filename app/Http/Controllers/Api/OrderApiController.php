<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Faker\Provider\Base;
use Illuminate\Http\Request;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\Api\OrderServiceApi;
use App\Http\Requests\Api\CreateOrderRequest;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\OrderDetailsResource;

class OrderApiController extends BaseApiController
{
    protected $orderService;
    public function __construct(OrderServiceApi $orderService)
    {
        $this->orderService = $orderService;
    }
    public function CreateOrder(CreateOrderRequest $request)
    {
        $user_id = auth()->guard('sanctum')->id();
        $data = array_merge($request->all(), ['user_id' => $user_id]);
        DB::beginTransaction();
        try {
            $order = $this->orderService->createOrder($data);
            DB::commit();
            return $this->successResponse($order, 'Order created successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create order', 500, $e->getMessage());
        }
    }
    public function GetOrder(Request $request)
    {
        $current_page = $request->input('current_page', 1);
        $per_page = $request->input('per_page', 10);
        $user_id = auth()->guard('sanctum')->id();
        $order = $this->orderService->getOrder($user_id, $current_page, $per_page);
        return $this->successResponse($order, 'Order retrieved successfully');
    }
    public function GetOrderDetails(Request $request)
    {
        $user_id = auth()->guard('sanctum')->id();
        $order_id = $request->order_id;
        $order = $this->orderService->getOrderDetails($order_id, $user_id);
        return $this->successResponse(OrderDetailsResource::make($order), 'Order details retrieved successfully');
    }
}
