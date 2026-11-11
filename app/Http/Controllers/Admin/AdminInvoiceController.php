<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminInvoiceController extends Controller
{
    public function index()
    {
        // BACKEND: Invoice::with('order.customer')->latest()->paginate(15)
        $orders = config('demo.account.orders') ?? [];

        $invoices = array_map(function($o, $idx) {
            return [
                'invoice_no' => 'INV-2026-' . (1001 + $idx),
                'order_id' => $o['id'],
                'customer_name' => $o['shipping_address']['first_name'] . ' ' . $o['shipping_address']['last_name'],
                'customer_email' => 'eleanor.vance@example.com',
                'date' => $o['date'],
                'amount' => $o['total'],
                'status' => 'Paid',
                'status_badge' => 'success',
                'payment_method' => $o['payment_method'],
            ];
        }, $orders, array_keys($orders));

        return view('admin.invoices.index', compact('invoices'));
    }

    public function show($id)
    {
        $orders = config('demo.account.orders') ?? [];
        $order = collect($orders)->firstWhere('id', $id) ?? $orders[0];
        $customer = config('demo.account.customer') ?? [];
        return view('admin.invoices.show', compact('order', 'customer'));
    }
}
