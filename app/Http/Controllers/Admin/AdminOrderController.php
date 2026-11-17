<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Orders\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items', 'invoice'])->latest('placed_at');

        if ($request->filled('status') && strtolower((string) $request->status) !== 'all') {
            $query->whereRaw('LOWER(status) = ?', [strtolower((string) $request->status)]);
        }

        if ($request->filled('payment_status') && strtolower((string) $request->payment_status) !== 'all') {
            $query->whereRaw('LOWER(payment_status) = ?', [strtolower((string) $request->payment_status)]);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('placed_at', '>=', (string) $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('placed_at', '<=', (string) $request->date_to);
        }

        $orders = $query->paginate(15)->withQueryString();
        $orders->setCollection(
            $orders->getCollection()->map(fn (Order $o) => $o->toAdminArray())
        );

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
        $orderModel = $this->findOrder($id);
        $order = $orderModel->toAdminArray();

        $customer = $orderModel->user?->toArray() ?? [
            'first_name' => $order['shipping_address']['first_name'] ?? 'Guest',
            'last_name' => $order['shipping_address']['last_name'] ?? 'Customer',
            'email' => $orderModel->customer_email,
            'phone' => $orderModel->customer_phone,
        ];

        return view('admin.orders.show', compact('order', 'customer'));
    }

    public function updateStatus(Request $request, $id, OrderStatusService $orderStatusService)
    {
        $order = $this->findOrder($id);

        $validated = $request->validate([
            'status' => ['required', 'in:Placed,Processing,Shipped,Delivered,Cancelled'],
            'tracking_number' => ['nullable', 'string', 'max:80'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $orderStatusService->transition(
            order: $order,
            newStatus: $validated['status'],
            trackingNumber: $validated['tracking_number'] ?? null,
            note: $validated['note'] ?? null,
            adminId: Auth::guard('admin')->id(),
        );

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    public function updateNotes(Request $request, $id)
    {
        $order = $this->findOrder($id);

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $order->update([
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Order internal notes saved.');
    }

    private function findOrder($id): Order
    {
        return Order::with(['items.batches', 'user', 'invoice', 'statusHistories.admin'])
            ->where(function ($q) use ($id) {
                $q->where('order_number', $id);
                if (is_numeric($id)) {
                    $q->orWhere('id', (int) $id);
                }
            })
            ->firstOrFail();
    }
}
