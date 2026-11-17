<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Inventory\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('items')->latest('placed_at');

        if ($request->filled('status') && strtolower($request->status) !== 'all') {
            $query->whereRaw('LOWER(status) = ?', [strtolower($request->status)]);
        }

        $orders = $query->get()->map(fn (Order $o) => $o->toViewArray())->all();

        $allCount = Order::count();
        $processingCount = Order::where('status', 'Processing')->count();
        $shippedCount = Order::where('status', 'Shipped')->count();
        $deliveredCount = Order::where('status', 'Delivered')->count();
        $totalProfit = (float) Order::where('status', '!=', 'Cancelled')->sum('total_profit');

        return view('admin.orders.index', compact(
            'orders',
            'allCount',
            'processingCount',
            'shippedCount',
            'deliveredCount',
            'totalProfit'
        ));
    }

    public function show($id)
    {
        $orderModel = Order::with(['items', 'user'])
            ->where('order_number', $id)
            ->orWhere('id', is_numeric($id) ? (int) $id : 0)
            ->firstOrFail();

        $order = $orderModel->toViewArray();
        $customer = $orderModel->user?->toArray() ?? [
            'first_name' => $order['shipping_address']['first_name'] ?? 'Guest',
            'last_name' => $order['shipping_address']['last_name'] ?? 'Customer',
            'email' => $orderModel->customer_email,
            'phone' => $orderModel->customer_phone,
        ];

        return view('admin.orders.show', compact('order', 'customer'));
    }

    public function updateStatus(Request $request, $id, StockService $stockService)
    {
        $order = Order::with('items.batches')
            ->where('order_number', $id)
            ->orWhere('id', is_numeric($id) ? (int) $id : 0)
            ->firstOrFail();

        $validated = $request->validate([
            'status' => ['required', 'in:Placed,Processing,Shipped,Delivered,Cancelled'],
            'tracking_number' => ['nullable', 'string', 'max:80'],
        ]);

        $previousStatus = $order->status;
        $newStatus = $validated['status'];

        DB::transaction(function () use ($order, $validated, $previousStatus, $newStatus, $stockService) {
            if ($previousStatus !== 'Cancelled' && $newStatus === 'Cancelled') {
                $stockService->restoreForCancelledOrder($order, Auth::guard('admin')->id());
            }

            $order->update([
                'status' => $newStatus,
                'current_step' => $newStatus === 'Cancelled' ? $order->current_step : $newStatus,
                'tracking_number' => $validated['tracking_number'] ?? $order->tracking_number,
            ]);
        });

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }
}
