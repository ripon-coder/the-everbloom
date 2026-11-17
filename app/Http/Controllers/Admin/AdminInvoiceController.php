<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', ''));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $invoices = Invoice::with('order.items')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('invoice_no', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%")
                        ->orWhereHas('order', fn ($oq) => $oq->where('order_number', 'like', "%{$search}%"));
                });
            })
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->when($dateFrom, fn ($q) => $q->whereDate('issued_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('issued_at', '<=', $dateTo))
            ->latest('issued_at')
            ->get()
            ->map(fn (Invoice $inv) => [
                'invoice_no' => $inv->invoice_no,
                'order_id' => $inv->order?->order_number ?? $inv->order_id,
                'customer_name' => $inv->customer_name,
                'customer_email' => $inv->customer_email,
                'date' => $inv->issued_at?->format('F j, Y') ?? '',
                'amount' => (float) $inv->amount,
                'status' => $inv->status,
                'status_badge' => $inv->status === 'Paid' ? 'success' : 'warning',
                'payment_method' => $inv->payment_method,
            ])
            ->all();

        return view('admin.invoices.index', compact('invoices'));
    }

    public function show($id)
    {
        $orderModel = Order::with(['items', 'user', 'invoice'])
            ->where(function ($q) use ($id) {
                $q->where('order_number', $id);
                if (is_numeric($id)) {
                    $q->orWhere('id', (int) $id);
                }
            })
            ->firstOrFail();

        $order = $orderModel->toAdminArray();
        $customer = $orderModel->user?->toArray() ?? [
            'first_name' => $order['shipping_address']['first_name'] ?? 'Guest',
            'last_name' => $order['shipping_address']['last_name'] ?? 'Customer',
            'email' => $orderModel->customer_email,
            'phone' => $orderModel->customer_phone,
        ];

        return view('admin.invoices.show', compact('order', 'customer'));
    }
}
