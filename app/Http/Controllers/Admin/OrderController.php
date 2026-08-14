<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of orders.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'payment_status', 'search']);
        $orders = $this->orderService->getAllOrders($filters);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(int $id): View
    {
        $order = $this->orderService->getOrderById($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->orderService->deleteOrder($id);
            return redirect()->route('admin.orders.index')
                ->with('success', 'Order deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting order: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of the specified order.
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:incomplete,pending,processing,shipped,delivered,cancelled',
        ]);

        try {
            $this->orderService->updateOrderStatus($id, $validated['status']);
            return back()->with('success', 'Order status updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating order status: ' . $e->getMessage());
        }
    }

    /**
     * Update the payment status of the specified order.
     */
    public function updatePaymentStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'payment_status' => 'required|string|in:pending,paid,failed,refunded,partially_paid',
        ]);

        try {
            $this->orderService->updateOrderPaymentStatus($id, $validated['payment_status']);
            return back()->with('success', 'Payment status updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating payment status: ' . $e->getMessage());
        }
    }
}
