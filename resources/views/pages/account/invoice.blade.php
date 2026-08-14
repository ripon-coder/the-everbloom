<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #INV-{{ $order->order_number }} | Feriwalarhat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .invoice-card {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen font-sans text-gray-800 antialiased p-4 sm:p-8">

    <!-- Top Action Bar (Hide in Print) -->
    <div class="max-w-4xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-4 no-print bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center gap-2">
            <span class="font-bold text-gray-900 text-sm">Invoice #INV-{{ $order->order_number }}</span>
            <span class="text-xs px-2 py-0.5 rounded {{ $order->getStatusColor() }} font-bold uppercase border border-gray-200">
                {{ $order->getStatusText() }}
            </span>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('account.order.show', $order->order_number) }}" 
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to Order</span>
            </a>
            <button onclick="window.print()" 
                class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg shadow transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print / Download PDF</span>
            </button>
        </div>
    </div>

    <!-- Printable Invoice Sheet -->
    <div class="invoice-card max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-2xl shadow-sm border border-gray-200 space-y-8">
        
        <!-- Store & Invoice Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start border-b border-gray-200 pb-8 gap-6">
            <div>
                <h1 class="text-2xl font-black text-emerald-600 tracking-tight">Feriwalarhat</h1>
                <p class="text-xs text-gray-500 font-medium mt-1">Premium E-Commerce Shopping</p>
                <p class="text-xs text-gray-500 mt-2">Support: support@feriwalarhat.com</p>
            </div>
            <div class="sm:text-right">
                <h2 class="text-xl font-extrabold text-gray-900 uppercase tracking-wide">INVOICE</h2>
                <p class="text-xs font-mono font-bold text-gray-700 mt-1">#INV-{{ $order->order_number }}</p>
                <p class="text-xs text-gray-500 mt-1">Date: {{ $order->created_at->format('F d, Y') }}</p>
            </div>
        </div>

        <!-- Billed To & Payment Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-xs">
            <!-- Customer Address -->
            <div>
                <h3 class="font-extrabold text-gray-400 uppercase tracking-widest text-[10px] mb-2">Billed To</h3>
                @php
                    $addr = $order->orderAddress;
                    $name = $addr->name ?? ($user->name ?? 'Customer');
                    $phone = $addr->phone_number ?? ($addr->phone ?? 'N/A');
                    $addressText = $addr->address ?? 'N/A';
                    $districtName = $addr->district->name ?? '';
                @endphp
                <p class="font-bold text-gray-900 text-sm mb-1">{{ $name }}</p>
                <p class="text-gray-600 leading-relaxed">{{ $addressText }}</p>
                @if($districtName)
                    <p class="text-gray-600 font-medium">{{ $districtName }}</p>
                @endif
                <p class="text-gray-700 font-semibold mt-2">Phone: {{ $phone }}</p>
            </div>

            <!-- Payment Info -->
            <div class="sm:text-right">
                <h3 class="font-extrabold text-gray-400 uppercase tracking-widest text-[10px] mb-2">Payment Details</h3>
                <p class="text-gray-700 font-medium">Method: <span class="font-bold text-gray-900 uppercase">{{ str_replace('_', ' ', $order->payment_method ?? 'COD') }}</span></p>
                <p class="text-gray-700 font-medium mt-1">Status: <span class="font-bold text-gray-900 uppercase">{{ $order->payment_status }}</span></p>
                <p class="text-gray-700 font-medium mt-1">Order Status: <span class="font-bold text-gray-900 uppercase">{{ $order->status }}</span></p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-y border-gray-200 text-gray-600 uppercase tracking-wider font-bold">
                        <th class="py-3 px-4">Item Description</th>
                        <th class="py-3 px-4 text-center">Unit Price</th>
                        <th class="py-3 px-4 text-center">Qty</th>
                        <th class="py-3 px-4 text-right">Total Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($order->orderProducts as $item)
                        @php
                            $variantTitle = $item->variant_name;
                            if (!$variantTitle && $item->productVariant && $item->productVariant->variantAttributes) {
                                $variantTitle = $item->productVariant->variantAttributes->map(fn($attr) => ($attr->attribute->name ?? '') . ': ' . ($attr->attributeValue->value ?? ''))->join(', ');
                            }
                        @endphp
                        <tr>
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-gray-900 text-sm">{{ $item->product_name ?? ($item->product->name ?? 'Product') }}</p>
                                @if($variantTitle)
                                    <p class="text-[11px] text-gray-500 mt-0.5">{{ $variantTitle }}</p>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center text-gray-700 font-medium">Tk. {{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-3.5 px-4 text-center text-gray-900 font-bold">{{ $item->quantity }}</td>
                            <td class="py-3.5 px-4 text-right font-black text-gray-900">Tk. {{ number_format($item->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Financial Breakdown -->
        <div class="flex justify-end pt-4 border-t border-gray-200">
            <div class="w-full sm:w-64 space-y-2 text-xs">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span class="font-bold text-gray-900">Tk. {{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if(($order->discount_amount ?? 0) > 0 || ($order->coupon_discount_amount ?? 0) > 0)
                    @php $discountVal = max($order->discount_amount ?? 0, $order->coupon_discount_amount ?? 0); @endphp
                    <div class="flex justify-between text-emerald-600 font-semibold">
                        <span>Discount</span>
                        <span>- Tk. {{ number_format($discountVal, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-gray-600">
                    <span>Shipping Charge</span>
                    <span class="font-bold text-gray-900">Tk. {{ number_format($order->shipping_amount, 2) }}</span>
                </div>
                <div class="pt-3 border-t border-gray-200 flex justify-between items-center text-sm font-black">
                    <span class="text-gray-900 uppercase">Total Amount</span>
                    <span class="text-emerald-600">Tk. {{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Thank You Note -->
        <div class="pt-8 border-t border-gray-200 text-center text-xs text-gray-500">
            <p class="font-bold text-gray-800">Thank you for your purchase!</p>
            <p class="mt-1">If you have any questions regarding this invoice, please contact support@feriwalarhat.com</p>
        </div>

    </div>

    @if(request()->has('print'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                window.print();
            });
        </script>
    @endif
</body>
</html>
