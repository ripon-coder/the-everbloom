<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        // BACKEND: Order::with(['customer', 'items'])->latest()->paginate(15)
        $orders = config('demo.account.orders') ?? [];

        if ($request->filled('status')) {
            $status = strtolower($request->status);
            if ($status !== 'all') {
                $orders = array_filter($orders, fn($o) => strtolower($o['status'] ?? '') === $status);
            }
        }

        $allCount = count(config('demo.account.orders') ?? []);
        $processingCount = count(array_filter(config('demo.account.orders') ?? [], fn($o) => ($o['status'] ?? '') === 'Processing'));
        $shippedCount = count(array_filter(config('demo.account.orders') ?? [], fn($o) => ($o['status'] ?? '') === 'Shipped'));
        $deliveredCount = count(array_filter(config('demo.account.orders') ?? [], fn($o) => ($o['status'] ?? '') === 'Delivered'));

        return view('admin.orders.index', compact(
            'orders',
            'allCount',
            'processingCount',
            'shippedCount',
            'deliveredCount'
        ));
    }

    public function show($id)
    {
        // BACKEND: Order::with(['customer', 'items.product', 'shippingAddress'])->findOrFail($id)
        $orders = config('demo.account.orders') ?? [];
        $order = collect($orders)->firstWhere('id', $id) ?? $orders[0];
        $customer = config('demo.account.customer') ?? [];

        return view('admin.orders.show', compact('order', 'customer'));
    }

    public function updateStatus(Request $request, $id)
    {
        // BACKEND: Update order status & trigger notification email
        return redirect()->back()->with('success', 'Order status updated successfully.');
    }
}
