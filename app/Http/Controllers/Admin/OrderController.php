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
        $filters = $request->only(['status', 'payment_status', 'search', 'date_from', 'date_to', 'order_by', 'order_direction']);
        $orders = $this->orderService->getAllOrders($filters);
        
        $statusOptions = Order::getStatusOptions();
        $paymentStatusOptions = Order::getPaymentStatusOptions();
        
        return view('admin.orders.index', compact('orders', 'filters', 'statusOptions', 'paymentStatusOptions'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create(): View
    {
        $users = User::all();
        $products = Product::all();
        $statusOptions = Order::getStatusOptions();
        $paymentStatusOptions = Order::getPaymentStatusOptions();
        
        return view('admin.orders.create', compact('users', 'products', 'statusOptions', 'paymentStatusOptions'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.total_price' => 'required|numeric|min:0',
            'shipping_address' => 'required|array',
            'shipping_address.name' => 'required|string|max:255',
            'shipping_address.address' => 'required|string',
            'shipping_address.city' => 'required|string|max:255',
            'shipping_address.state' => 'required|string|max:255',
            'shipping_address.zip' => 'required|string|max:20',
            'shipping_address.country' => 'required|string|max:255',
            'shipping_address.phone' => 'required|string|max:20',
            'billing_address' => 'nullable|array',
            'billing_address.name' => 'nullable|string|max:255',
            'billing_address.address' => 'nullable|string',
            'billing_address.city' => 'nullable|string|max:255',
            'billing_address.state' => 'nullable|string|max:255',
            'billing_address.zip' => 'nullable|string|max:20',
            'billing_address.country' => 'nullable|string|max:255',
            'billing_address.phone' => 'nullable|string|max:20',
            'subtotal' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'nullable|string|in:pending,paid,failed,refunded,partially_paid',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $order = $this->orderService->createOrder($validated);
            return redirect()->route('admin.orders.show', $order->id)
                           ->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                         ->with('error', 'Error creating order: ' . $e->getMessage());
        }
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
     * Show the form for editing the specified order.
     */
    public function edit(int $id): View
    {
        $order = $this->orderService->getOrderById($id);
        $users = User::all();
        $products = Product::all();
        $statusOptions = Order::getStatusOptions();
        $paymentStatusOptions = Order::getPaymentStatusOptions();
        
        return view('admin.orders.edit', compact('order', 'users', 'products', 'statusOptions', 'paymentStatusOptions'));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.total_price' => 'required|numeric|min:0',
            'shipping_address' => 'required|array',
            'shipping_address.name' => 'required|string|max:255',
            'shipping_address.address' => 'required|string',
            'shipping_address.city' => 'required|string|max:255',
            'shipping_address.state' => 'required|string|max:255',
            'shipping_address.zip' => 'required|string|max:20',
            'shipping_address.country' => 'required|string|max:255',
            'shipping_address.phone' => 'required|string|max:20',
            'billing_address' => 'nullable|array',
            'billing_address.name' => 'nullable|string|max:255',
            'billing_address.address' => 'nullable|string',
            'billing_address.city' => 'nullable|string|max:255',
            'billing_address.state' => 'nullable|string|max:255',
            'billing_address.zip' => 'nullable|string|max:20',
            'billing_address.country' => 'nullable|string|max:255',
            'billing_address.phone' => 'nullable|string|max:20',
            'subtotal' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'nullable|string|in:pending,paid,failed,refunded,partially_paid',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->orderService->updateOrder($id, $validated);
            return redirect()->route('admin.orders.show', $id)
                           ->with('success', 'Order updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                         ->with('error', 'Error updating order: ' . $e->getMessage());
        }
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
     * Restore the specified deleted order.
     */
    public function restore(int $id): RedirectResponse
    {
        try {
            $this->orderService->restoreOrder($id);
            return redirect()->route('admin.orders.index')
                           ->with('success', 'Order restored successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error restoring order: ' . $e->getMessage());
        }
    }

    /**
     * Force delete the specified order.
     */
    public function forceDelete(int $id): RedirectResponse
    {
        try {
            $this->orderService->forceDeleteOrder($id);
            return redirect()->route('admin.orders.index')
                           ->with('success', 'Order permanently deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error permanently deleting order: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of the specified order.
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
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
