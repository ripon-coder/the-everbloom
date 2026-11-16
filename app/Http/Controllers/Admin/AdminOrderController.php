<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\PurchaseBatchItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

    public function updateStatus(Request $request, $id)
    {
        $order = Order::with('items')
            ->where('order_number', $id)
            ->orWhere('id', is_numeric($id) ? (int) $id : 0)
            ->firstOrFail();

        $validated = $request->validate([
            'status' => ['required', 'in:Placed,Processing,Shipped,Delivered,Cancelled'],
            'tracking_number' => ['nullable', 'string', 'max:80'],
        ]);

        $previousStatus = $order->status;
        $newStatus = $validated['status'];

        DB::transaction(function () use ($order, $validated, $previousStatus, $newStatus) {
            if ($previousStatus !== 'Cancelled' && $newStatus === 'Cancelled') {
                foreach ($order->items as $item) {
                    $qty = (int) $item->qty;
                    if ($qty <= 0 || !$item->product_id) {
                        continue;
                    }
                    $product = Product::lockForUpdate()->find($item->product_id);
                    if ($product) {
                        $newStock = (int) $product->stock_count + $qty;
                        $product->update([
                            'stock_count' => $newStock,
                            'stock_status' => $newStock > $product->min_threshold ? 'in_stock' : ($newStock > 0 ? 'low_stock' : 'out_of_stock'),
                        ]);
                    }
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
                        if ($variant) {
                            $newVarStock = (int) $variant->stock_count + $qty;
                            $variant->update([
                                'stock_count' => $newVarStock,
                                'stock_status' => $newVarStock > ($product?->min_threshold ?? 10) ? 'in_stock' : 'low_stock',
                            ]);
                        }
                    }
                    $batchItem = PurchaseBatchItem::where('product_id', $item->product_id)->latest('id')->first();
                    if ($batchItem) {
                        $batchItem->increment('remaining_qty', $qty);
                    }
                }
                Cache::forget('storefront_catalog_json');
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
