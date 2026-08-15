@extends('admin.layouts.app')

@section('title', 'Order Details - ' . $order->order_number)

@section('content')
    @php
        $currency = $currency_sign ?? 'Tk.';
        $flashSale = collect($order->flashSale ?? [])->isNotEmpty()
            ? collect($order->flashSale)->keyBy('product_variant_id')->toArray()
            : [];
        $totalBuyingPrice = 0;
        $totalItemsCount = $order->orderProducts->sum('quantity');
    @endphp

    <div class="max-w-7xl mx-auto space-y-6" x-data="{
        copiedText: null,
        copyToClipboard(text, label) {
            navigator.clipboard.writeText(text);
            this.copiedText = label;
            setTimeout(() => this.copiedText = null, 2000);
        }
    }">
        <!-- Single Unified Order Card (Sharp / Non-rounded) -->
        <div class="bg-white border border-gray-200 shadow-sm">
            
            <!-- Card Header: Title, Breadcrumbs & Actions -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="space-y-2">
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-xs text-gray-500">
                            <a href="{{ route('admin.orders.index') }}" class="hover:text-blue-600 font-medium transition flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Orders
                            </a>
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-700 font-semibold">{{ $order->order_number }}</span>
                        </nav>

                        <!-- Title & Status Badges -->
                        <div class="flex flex-wrap items-center gap-2.5">
                            <div class="flex items-center space-x-2">
                                <h1 class="text-2xl font-bold font-mono text-gray-900 tracking-tight">{{ $order->order_number }}</h1>
                                <button type="button" @click="copyToClipboard('{{ $order->order_number }}', 'order_num')"
                                        class="text-gray-400 hover:text-gray-700 transition" title="Copy Order Number">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                                <span x-show="copiedText === 'order_num'" class="text-xs text-emerald-600 font-bold">Copied!</span>
                            </div>

                            <!-- Fulfillment Status Pill -->
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold {{ $order->getStatusColor() }} border border-gray-200">
                                <span class="w-1.5 h-1.5 bg-current mr-1.5"></span>
                                {{ $order->getStatusText() }}
                            </span>

                            <!-- Payment Status Pill -->
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold {{ $order->getPaymentStatusColor() }} border border-gray-200">
                                {{ $order->getPaymentStatusText() }}
                            </span>

                            <span class="text-xs text-gray-500 font-mono">
                                Placed on {{ $order->created_at ? $order->created_at->format('M d, Y · H:i') : 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center gap-2">
                        @if (\Illuminate\Support\Facades\Route::has('account.order.invoice'))
                            <a href="{{ route('account.order.invoice', $order->order_number) }}" target="_blank"
                               class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-sm transition">
                                <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                View Invoice
                            </a>
                        @endif

                        <button onclick="window.print()"
                                class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-white bg-gray-900 hover:bg-black shadow-sm transition">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print Order
                        </button>

                        <a href="{{ route('admin.orders.index') }}"
                           class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-sm transition">
                            <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Orders
                        </a>
                    </div>
                </div>
            </div>

            <!-- Top Status Management & Quick KPI Bar -->
            <div class="p-5 sm:p-6 bg-gray-50/50 border-b border-gray-200">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-center">
                    
                    <!-- Left: Status Updaters -->
                    <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Order Status Form -->
                        <div class="bg-white p-3.5 border border-gray-200">
                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Fulfillment Status</label>
                            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                <select name="status" onchange="this.form.submit()"
                                        class="w-full text-xs font-semibold py-1.5 px-2.5 border border-gray-300 bg-white text-gray-800 focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                    @foreach(\App\Models\Order::getStatusOptions() as $value => $label)
                                        <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>

                        <!-- Payment Status Form -->
                        <div class="bg-white p-3.5 border border-gray-200">
                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Payment Status</label>
                            <form action="{{ route('admin.orders.update-payment-status', $order->id) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                <select name="payment_status" onchange="this.form.submit()"
                                        class="w-full text-xs font-semibold py-1.5 px-2.5 border border-gray-300 bg-white text-gray-800 focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                    @foreach(\App\Models\Order::getPaymentStatusOptions() as $value => $label)
                                        <option value="{{ $value }}" {{ $order->payment_status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>

                    <!-- Right: Quick Financial Stats Grid -->
                    <div class="lg:col-span-5 grid grid-cols-2 gap-3">
                        <div class="bg-white p-3.5 border border-gray-200">
                            <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Total Amount</p>
                            <p class="text-lg font-bold text-gray-900 mt-0.5 truncate">{{ $currency }}{{ number_format($order->total_amount, 2) }}</p>
                        </div>

                        <div class="bg-white p-3.5 border border-gray-200">
                            <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Ordered Items</p>
                            <p class="text-lg font-bold text-gray-900 mt-0.5">
                                {{ $totalItemsCount }} <span class="text-xs font-normal text-gray-500">({{ $order->orderProducts->count() }} unique)</span>
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Main Content Area: 2 Columns Grid -->
            <div class="p-5 sm:p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Left 8 Cols: Order Items Table & Notes -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- Order Items Section -->
                    <div class="border border-gray-200">
                        <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Order Items ({{ $order->orderProducts->count() }})</h2>
                            </div>
                            <span class="text-xs text-gray-500">Total Quantity: {{ $totalItemsCount }}</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                                <thead class="bg-gray-50/50 text-gray-500 uppercase font-semibold">
                                    <tr>
                                        <th class="px-4 py-3">Product</th>
                                        <th class="px-4 py-3">Unit Price</th>
                                        <th class="px-4 py-3 text-center">Qty</th>
                                        <th class="px-4 py-3 text-right">Total Price</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach ($order->orderProducts as $orderProduct)
                                        @php
                                            $variantId = $orderProduct->product_variant_id;
                                            $flashItem = $flashSale[$variantId] ?? null;
                                            $totalBuyingPrice += (float) $orderProduct->buying_price * $orderProduct->quantity;
                                            $itemImage = $orderProduct->product?->firstImage?->getImageUrl();
                                        @endphp
                                        <tr class="hover:bg-gray-50/70 transition">
                                            <!-- Product Details & Image -->
                                            <td class="px-4 py-3.5">
                                                <div class="flex items-start space-x-3">
                                                    @if ($itemImage)
                                                        <img src="{{ $itemImage }}" alt="Product" class="w-12 h-12 object-cover border border-gray-200 flex-shrink-0">
                                                    @else
                                                        <div class="w-12 h-12 bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 flex-shrink-0">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                        </div>
                                                    @endif

                                                    <div class="space-y-1 min-w-0">
                                                        @if ($orderProduct->product)
                                                            <a href="{{ route('admin.products.show', $orderProduct->product->id) }}"
                                                               class="font-semibold text-gray-900 hover:text-blue-600 transition truncate block">
                                                                {{ $orderProduct->product->name }}
                                                            </a>
                                                        @else
                                                            <span class="font-semibold text-gray-500 italic">Product Removed / Unavailable</span>
                                                        @endif

                                                        <!-- SKU & Variant Attributes -->
                                                        <div class="flex flex-wrap items-center gap-1.5 text-[11px]">
                                                            @if ($orderProduct->productVariant && $orderProduct->productVariant->sku)
                                                                <span class="font-mono text-gray-500 bg-gray-100 px-1.5 py-0.2 border border-gray-200">
                                                                    SKU: {{ $orderProduct->productVariant->sku }}
                                                                </span>
                                                            @endif

                                                            @if ($orderProduct->productVariant && $orderProduct->productVariant->variantAttributes->count())
                                                                @foreach ($orderProduct->productVariant->variantAttributes as $attr)
                                                                    <span class="bg-gray-100 text-gray-700 px-1.5 py-0.2 border border-gray-200">
                                                                        {{ $attr->attribute?->name }}: <strong class="text-gray-900">{{ $attr->attributeValue?->value }}</strong>
                                                                    </span>
                                                                @endforeach
                                                            @endif
                                                        </div>

                                                        <!-- Offers / Shipping Tag -->
                                                        <div class="flex flex-wrap items-center gap-2 pt-0.5">
                                                            @if ($flashItem && isset($flashItem['discounted_price']))
                                                                <span class="inline-flex items-center text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.2">
                                                                    ⚡ Flash Sale (-{{ $currency }}{{ number_format($flashItem['discounted_price'] * $orderProduct->quantity, 2) }})
                                                                </span>
                                                            @endif

                                                            @if ($orderProduct->is_free_shipping)
                                                                <span class="inline-flex items-center text-[10px] font-semibold text-cyan-700 bg-cyan-50 border border-cyan-200 px-1.5 py-0.2">
                                                                    🚚 Free Shipping Item
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Unit Price -->
                                            <td class="px-4 py-3.5 whitespace-nowrap text-gray-700">
                                                {{ $currency }}{{ number_format($orderProduct->unit_price, 2) }}
                                            </td>

                                            <!-- Qty -->
                                            <td class="px-4 py-3.5 whitespace-nowrap text-center font-bold text-gray-900">
                                                × {{ $orderProduct->quantity }}
                                            </td>

                                            <!-- Total -->
                                            <td class="px-4 py-3.5 whitespace-nowrap text-right font-bold text-gray-900">
                                                {{ $currency }}{{ number_format($orderProduct->total_price, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Order Notes (if available) -->
                    @if ($order->notes)
                        <div class="p-4 bg-amber-50/50 border border-amber-200/80 space-y-1">
                            <h3 class="text-xs font-bold text-amber-900 uppercase tracking-wider flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                                Customer / Order Notes
                            </h3>
                            <p class="text-xs text-amber-900/90 leading-relaxed">{{ $order->notes }}</p>
                        </div>
                    @endif

                </div>

                <!-- Right 4 Cols: Summary, Customer, Shipping & Profit Breakdown -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- Financial Calculation Breakdown -->
                    <div class="border border-gray-200 p-5 bg-white space-y-4">
                        <h2 class="text-xs font-bold text-gray-900 uppercase tracking-wider pb-2 border-b border-gray-100 flex items-center justify-between">
                            <span>Financial Summary</span>
                            <span class="text-gray-400 font-normal">Details</span>
                        </h2>

                        <div class="space-y-2.5 text-xs">
                            <div class="flex justify-between text-gray-600">
                                <span>Items Subtotal</span>
                                <span class="font-semibold text-gray-900">{{ $currency }}{{ number_format($order->subtotal, 2) }}</span>
                            </div>

                            @if ($order->coupon_discount_amount > 0)
                                <div class="flex justify-between items-center bg-emerald-50 text-emerald-800 p-2 border border-emerald-200">
                                    <div class="flex flex-col">
                                        <span class="font-bold">Coupon Applied</span>
                                        <span class="text-[10px] font-mono text-emerald-600">Code: {{ $order->coupon_used }}</span>
                                    </div>
                                    <span class="font-bold">-{{ $currency }}{{ number_format($order->coupon_discount_amount, 2) }}</span>
                                </div>
                            @endif

                            @if ($order->flash_discount_amount > 0)
                                <div class="flex justify-between items-center bg-amber-50 text-amber-800 p-2 border border-amber-200">
                                    <span class="font-bold">Flash Sale Discount</span>
                                    <span class="font-bold">-{{ $currency }}{{ number_format($order->flash_discount_amount, 2) }}</span>
                                </div>
                            @endif

                            @if ($order->tax_amount > 0)
                                <div class="flex justify-between text-gray-600">
                                    <span>Tax</span>
                                    <span class="font-semibold text-gray-900">{{ $currency }}{{ number_format($order->tax_amount, 2) }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between text-gray-600">
                                <span>Shipping Fee</span>
                                <span class="font-semibold text-gray-900">
                                    {{ $order->shipping_amount > 0 ? $currency . number_format($order->shipping_amount, 2) : 'Free' }}
                                </span>
                            </div>

                            <div class="border-t border-gray-200 pt-3 flex justify-between items-center text-sm font-bold">
                                <span class="text-gray-900">Grand Total</span>
                                <span class="text-base text-blue-700">{{ $currency }}{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Profit Analysis Card -->
                    <div class="border border-gray-200 p-5 bg-gray-50/60 space-y-3">
                        <h2 class="text-xs font-bold text-gray-900 uppercase tracking-wider pb-2 border-b border-gray-200">
                            Profit & Cost Analysis
                        </h2>

                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between text-gray-600">
                                <span>Buying / Sourcing Cost:</span>
                                <span class="font-semibold text-gray-800">{{ $currency }}{{ number_format($totalBuyingPrice, 2) }}</span>
                            </div>

                            @if ($order->admin_shipping_amount)
                                <div class="flex justify-between text-gray-600">
                                    <span>Admin Shipping Cost:</span>
                                    <span class="font-semibold text-rose-600">-{{ $currency }}{{ number_format($order->admin_shipping_amount, 2) }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center pt-2 border-t border-gray-200 text-xs font-bold">
                                <span class="text-gray-900">Estimated Net Profit:</span>
                                <span class="text-sm {{ $order->profit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $currency }}{{ number_format($order->profit, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information & Shipping Destination -->
                    <div class="border border-gray-200 p-5 bg-white space-y-4">
                        <h2 class="text-xs font-bold text-gray-900 uppercase tracking-wider pb-2 border-b border-gray-100 flex items-center justify-between">
                            <span>Customer & Delivery</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </h2>

                        <!-- Customer Details -->
                        <div class="space-y-1.5 text-xs">
                            <p class="text-gray-500 font-medium">Customer Account:</p>
                            @if ($order->user)
                                <p class="font-semibold text-gray-900">{{ $order->user->name }}</p>
                                <p class="text-gray-600">{{ $order->user->email }}</p>
                            @else
                                <p class="text-gray-700 italic">Guest Checkout / Incomplete Account</p>
                            @endif
                        </div>

                        <!-- Delivery Address Details -->
                        <div class="pt-3 border-t border-gray-100 space-y-1.5 text-xs">
                            <p class="text-gray-500 font-medium">Delivery Destination:</p>
                            @if ($order->orderAddress)
                                <div class="bg-gray-50 p-2.5 border border-gray-200 text-gray-700 space-y-1 leading-relaxed">
                                    <p class="font-bold text-gray-900">{{ $order->orderAddress['name'] ?? '' }}</p>
                                    <p>{{ $order->orderAddress['address'] ?? '' }}</p>
                                    @if (!empty($order->orderAddress['zone']))
                                        <p>{{ $order->orderAddress['zone'] }}</p>
                                    @endif
                                    @if ($order->orderAddress->district)
                                        <p class="font-medium text-gray-900">📍 {{ $order->orderAddress->district->name }}</p>
                                    @endif
                                    @if (!empty($order->orderAddress['phone_number']))
                                        <p class="font-mono text-blue-700 pt-1">📞 {{ $order->orderAddress['phone_number'] }}</p>
                                    @endif
                                </div>
                            @else
                                <p class="text-gray-400 italic">No delivery address provided.</p>
                            @endif
                        </div>

                        <!-- Payment Method -->
                        <div class="pt-3 border-t border-gray-100 space-y-1.5 text-xs">
                            <p class="text-gray-500 font-medium">Payment Information:</p>
                            <div class="flex items-center justify-between text-gray-800">
                                <span>Method:</span>
                                <span class="font-bold uppercase">{{ $order->payment_method ?? 'Cash on Delivery' }}</span>
                            </div>
                            @if ($order->payment_account)
                                <div class="flex items-center justify-between text-gray-800">
                                    <span>Account / TRX:</span>
                                    <span class="font-mono">{{ $order->payment_account }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

            </div>

            <!-- Card Footer -->
            <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-gray-500">
                <span>Internal Order ID: #{{ $order->id }}</span>
                <span>Last Updated: {{ $order->updated_at ? $order->updated_at->format('M d, Y · H:i:s') : 'N/A' }}</span>
            </div>

        </div>
    </div>
@endsection
