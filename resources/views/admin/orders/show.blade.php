@extends('admin.layouts.app')

@section('title', 'Order Details - ' . $order->order_number)

@section('content')

    @php
        $flashSale = collect($order->flashSale ?? [])->isNotEmpty()
            ? collect($order->flashSale)->keyBy('product_variant_id')->toArray()
            : [];
        $totalBuyingPrice = 0;
    @endphp

    <div class="p-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.orders.index') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Order Details</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    {{ $order->order_number }}
                </span>
            </div>
            <div class="flex space-x-2">
                <button onclick="window.print()"
                    class="bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    Print Order
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Status Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Status</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Order Status</label>
                            <div class="flex items-center space-x-2">
                                <span
                                    class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                    {{ $order->getStatusColor() }}">
                                    {{ $order->getStatusText() }}
                                </span>
                                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()"
                                        class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>
                                            Processing</option>
                                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped
                                        </option>
                                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>
                                            Delivered</option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>
                                            Cancelled</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                            <div class="flex items-center space-x-2">
                                <span
                                    class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                    {{ $order->getPaymentStatusColor() }}">
                                    {{ $order->getPaymentStatusText() }}
                                </span>
                                <form action="{{ route('admin.orders.update-payment-status', $order->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <select name="payment_status" onchange="this.form.submit()"
                                        class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        <option value="pending"
                                            {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>
                                            Paid</option>
                                        <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>
                                            Failed</option>
                                        <option value="refunded"
                                            {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h2>
                    <div class="space-y-4">
                        @foreach ($order->orderProducts as $orderProduct)
                            <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                                @if ($orderProduct->product && $orderProduct->product->firstImage)
                                    <img src="{{ $orderProduct->product->firstImage->getImageUrl() }}"
                                        alt="{{ $orderProduct->product->name }}" class="w-16 h-16 object-cover rounded-lg">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <div class="flex justify-between">
                                        <div class="flex flex-col">
                                            <a href="{{ route('admin.products.show', $orderProduct->product->id) }}">
                                                <h3 class="font-medium text-blue-500 hover:text-blue-700">
                                                    {{ $orderProduct->product->name ?? 'Product Deleted' }}
                                                </h3>
                                            </a>
                                            @if($orderProduct->productVariant && $orderProduct->productVariant->sku)
                                                <span class="text-xs text-gray-500">SKU: {{ $orderProduct->productVariant->sku }}</span>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-gray-900">
                                                {{ $currency_sign }}{{ number_format($orderProduct->total_price, 2) }}</p>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Qty: {{ $orderProduct->quantity }} Ã—
                                        {{ $currency_sign }}{{ number_format($orderProduct->unit_price, 2) }}
                                    </p>

                                    @if ($orderProduct->productVariant && $orderProduct->productVariant->variantAttributes->count())
                                        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-gray-700">
                                            <span class="font-medium text-gray-600">Variant:</span>
                                            @foreach ($orderProduct->productVariant->variantAttributes as $attr)
                                                <span
                                                    class="inline-flex items-center bg-gray-100 border border-gray-200 rounded-full px-2.5 py-0.5">
                                                    <span
                                                        class="text-gray-800 font-medium">{{ $attr->attribute->name }}:</span>
                                                    <span
                                                        class="ml-1 text-gray-600">{{ $attr->attributeValue->value }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    @php
                                        $variantId = $orderProduct->product_variant_id;
                                        $flashItem = $flashSale[$variantId] ?? null;
                                        $totalBuyingPrice +=
                                            (float) $orderProduct->buying_price * $orderProduct->quantity;
                                    @endphp

                                    @if ($flashItem && isset($flashItem['discounted_price']))
                                        <div
                                            class="mt-2 flex items-center gap-2 text-sm text-emerald-600 font-medium bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>
                                                <span class="font-semibold">Flash Sale:</span>
                                                <span
                                                    class="text-emerald-700">{{ ucfirst($flashItem['flash_sale_slug']) }}</span>
                                                â€”
                                                <span class="text-emerald-800">-
                                                    {{ $currency_sign }}{{ number_format($flashItem['discounted_price'] * $orderProduct->quantity, 2) }}</span>
                                            </span>
                                        </div>
                                    @endif

                                    @if ($orderProduct->is_free_shipping)
                                        <div
                                            class="mt-2 flex items-center gap-2 text-sm text-sky-700 font-medium bg-sky-50 border border-sky-200 rounded-lg px-3 py-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-sky-500"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="2">
                                                <rect x="1" y="3" width="15" height="13" />
                                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8" />
                                                <circle cx="5.5" cy="18.5" r="1.5" />
                                                <circle cx="18.5" cy="18.5" r="1.5" />
                                            </svg>
                                            <span>Free Shipping Applied</span>
                                        </div>
                                    @endif

                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Notes -->
                @if ($order->notes)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Notes</h2>
                        <p class="text-gray-700">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Order Summary -->
            <div class="space-y-6">
                <!-- Customer Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h2>
                    @if ($order->user)
                        <div class="space-y-2">
                            <p class="text-sm"><span class="font-medium text-gray-700">Name:</span>
                                {{ $order->user->name }}</p>
                            <p class="text-sm"><span class="font-medium text-gray-700">Email:</span>
                                {{ $order->user->email }}</p>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Customer information not available</p>
                    @endif
                </div>

                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h2>
                    {{-- {{ $order->orderAddress }} --}}
                    @if ($order->orderAddress)
                        <div class="space-y-1 text-sm">
                            <p>{{ $order->orderAddress['name'] ?? '' }}</p>
                            <p>{{ $order->orderAddress['address'] ?? '' }}</p>
                            <p>{{ $order->orderAddress['zone'] ?? '' }}</p>
                            <p>{{ $order->orderAddress->district->name ?? '' }}</p>
                            <p>Phone: {{ $order->orderAddress['phone_number'] ?? '' }}</p>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Shipping address not available</p>
                    @endif
                </div>

                <!-- Order Summary -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <h2 class="text-xl font-semibold text-gray-800 mb-5 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                        Order Summary
                    </h2>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium text-gray-900">{{ $currency_sign }}{{ number_format($order->subtotal, 2) }}</span>
                        </div>

                        @if ($order->coupon_discount_amount > 0)
                            <div class="flex justify-between items-center bg-green-50 text-green-700 px-3 py-2 rounded-md">
                                <div class="flex flex-col">
                                    <span class="font-medium">Coupon Applied</span>
                                    <span class="text-xs text-green-600">Code: {{ $order->coupon_used }}</span>
                                </div>
                                <span class="font-semibold">-
                                    {{ $currency_sign }}{{ number_format($order->coupon_discount_amount, 2) }}</span>
                            </div>
                        @endif

                        @if ($order->flash_discount_amount > 0)
                            <div
                                class="flex justify-between items-center bg-yellow-50 text-yellow-700 px-3 py-2 rounded-md">
                                <div class="flex flex-col">
                                    <span class="font-medium">Flash Sale Discount</span>
                                    <span class="text-xs text-yellow-600">Limited Time Offer</span>
                                </div>
                                <span class="font-semibold">-
                                    {{ $currency_sign }}{{ number_format($order->flash_discount_amount, 2) }}</span>
                            </div>
                        @endif

                        @if ($order->tax_amount > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax:</span>
                                <span class="font-medium text-gray-900">{{ $currency_sign }}{{ number_format($order->tax_amount, 2) }}</span>
                            </div>
                        @endif

                        @if ($order->shipping_amount)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping:</span>
                                <span
                                    class="font-medium text-gray-900">{{ $currency_sign }}{{ number_format($order->shipping_amount, 2) }}</span>
                            </div>
                        @endif

                        <div class="border-t pt-3 mt-3">
                            <div class="flex justify-between">
                                <span class="text-base font-bold text-gray-900">Total:</span>
                                <span
                                    class="text-base font-bold text-indigo-600">{{ $currency_sign }}{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>

                    </div>
                </div>


                <!-- Profit Calculation -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Profit Calculation</h2>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Buying Cost ({{ $currency_sign }}):</span>
                            <span class="text-yellow-600 font-bold">{{ number_format($totalBuyingPrice, 2) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span>Shipping (Shop â†’ Customer) ({{ $currency_sign }}):</span>
                            <span class="text-red-600 font-bold">-{{ number_format($order->admin_shipping_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Estimated Profit ({{ $currency_sign }}):</span>
                            <span class="text-green-600 font-bold">{{ number_format($order->profit, 2) }}</span>
                        </div>

                    </div>
                </div>


                <!-- Payment Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Method:</span>
                            <span class="font-medium">{{ $order->payment_method ?? 'Not specified' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Account:</span>
                            <span class="font-medium">{{ $order->payment_account ?? 'Not specified' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order Date:</span>
                            <span class="font-medium">{{ $order->created_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
