<x-layouts.app title="Order Received | Feriwalarhat">
    <style>
        @media print {
            body * {
                visibility: hidden !important;
            }
            #printable-order-card, #printable-order-card * {
                visibility: visible !important;
            }
            #printable-order-card {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                border: none !important;
                box-shadow: none !important;
                padding: 10px !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>

    <div class="order-received-page-container bg-gray-50/70 min-h-screen py-6 md:py-10">
        <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8">
            
            <!-- MAIN ORDER RECEIVED CARD -->
            <div id="printable-order-card" class="bg-white border border-gray-200 rounded-none shadow-xs overflow-hidden">
                
                <!-- Top Accent Line -->
                <div class="h-1.5 bg-emerald-600"></div>

                <div class="p-5 sm:p-8 md:p-10 space-y-6 md:space-y-8">
                    
                    <!-- 1. Header & Success Badge -->
                    <div class="text-center space-y-2.5 pb-6 border-b border-gray-100">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto border-2 border-emerald-200/80 shadow-xs">
                            <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-bold !text-gray-900 tracking-tight">Thank You! Your Order has been Placed</h1>
                        <p class="text-xs sm:text-sm !text-gray-600 max-w-md mx-auto leading-relaxed">
                            We've received your order and our team is preparing it for shipment. A confirmation has been recorded in your account.
                        </p>

                        <div class="pt-2 flex items-center justify-center gap-2">
                            <span class="inline-flex items-center gap-2 bg-slate-900 !text-white px-3.5 py-1.5 rounded-none text-xs font-mono font-semibold tracking-wider">
                                <span class="text-gray-400">ORDER NO:</span>
                                <span class="text-emerald-400 font-bold">#{{ $order->order_number }}</span>
                            </span>
                        </div>
                    </div> 

                    <!-- 2. Order Metadata Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 p-4 bg-gray-50/70 border border-gray-200 text-xs">
                        <div class="space-y-1">
                            <span class="font-medium !text-gray-500 uppercase tracking-wide block text-[11px]">Order Date</span>
                            <span class="font-semibold !text-gray-900 block">{{ $order->created_at ? $order->created_at->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="space-y-1">
                            <span class="font-medium !text-gray-500 uppercase tracking-wide block text-[11px]">Payment Method</span>
                            <span class="font-semibold !text-gray-900 block uppercase">{{ strtoupper($order->payment_method ?? 'COD') }}</span>
                        </div>
                        <div class="space-y-1">
                            <span class="font-medium !text-gray-500 uppercase tracking-wide block text-[11px]">Order Status</span>
                            <span class="inline-flex items-center font-semibold text-xs px-2 py-0.5 border {{ $order->getStatusColor() ?? 'bg-yellow-50 !text-yellow-800 border-yellow-200' }}">
                                {{ $order->getStatusText() ?? ucfirst($order->status ?? 'Pending') }}
                            </span>
                        </div>
                        <div class="space-y-1">
                            <span class="font-medium !text-gray-500 uppercase tracking-wide block text-[11px]">Total Amount</span>
                            <span class="font-bold !text-emerald-700 block text-sm sm:text-base">Tk. {{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>

                    <!-- 3. Delivery Address & Shipping Details -->
                    @if($order->orderAddress)
                        <div class="p-4 sm:p-5 border border-gray-200 bg-white space-y-2">
                            <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <h3 class="text-xs sm:text-sm font-semibold !text-gray-900 uppercase tracking-wide">Shipping Address</h3>
                            </div>
                            <div class="text-xs sm:text-sm space-y-1 pt-1">
                                <p class="font-semibold !text-gray-900">{{ $order->orderAddress->name }}</p>
                                <p class="!text-gray-600 leading-relaxed">{{ $order->orderAddress->address }}, {{ optional($order->orderAddress->district)->name }}</p>
                                <div class="pt-1 flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-gray-100 !text-gray-700 text-xs font-medium border border-gray-200">
                                        <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        {{ $order->orderAddress->phone_number ?? $order->orderAddress->phone }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- 4. Purchased Items List -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between border-b border-gray-200 pb-2.5">
                            <h3 class="text-xs sm:text-sm font-semibold !text-gray-900 uppercase tracking-wide">Ordered Items ({{ $order->orderProducts->count() }})</h3>
                        </div>
                        
                        <div class="divide-y divide-gray-100 border-b border-gray-100">
                            @foreach($order->orderProducts as $item)
                                <div class="py-3.5 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <div class="w-14 h-14 bg-gray-50 border border-gray-200 p-0.5 flex-shrink-0 flex items-center justify-center overflow-hidden">
                                            @php
                                                $productObj = $item->product;
                                                $imgUrl = $productObj?->firstImage?->getImageUrl() ?? $productObj?->anyImage?->getImageUrl();
                                            @endphp
                                            @if($imgUrl)
                                                <img src="{{ $imgUrl }}" alt="{{ optional($productObj)->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400 text-[10px] font-medium">No Image</div>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h4 class="text-xs sm:text-sm font-semibold !text-gray-900 leading-snug truncate">
                                                {{ optional($item->product)->name ?? ($item->product_name ?? 'Product') }}
                                            </h4>
                                            @if($item->productVariant && $item->productVariant->variantAttributes)
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    @foreach($item->productVariant->variantAttributes as $va)
                                                        <span class="inline-flex items-center text-[10px] font-medium text-gray-600 bg-gray-100 px-1.5 py-0.5 border border-gray-200">
                                                            <span class="text-gray-400 mr-1">{{ optional($va->attribute)->name }}:</span>
                                                            {{ optional($va->attributeValue)->value }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div class="text-xs !text-gray-500 mt-1 flex items-center gap-2">
                                                <span>Qty: <strong class="!text-gray-800">{{ $item->quantity }}</strong></span>
                                                <span class="text-gray-300">&times;</span>
                                                <span>Tk. {{ number_format($item->unit_price, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right font-semibold text-xs sm:text-sm !text-gray-900 whitespace-nowrap">
                                        Tk. {{ number_format($item->total_price, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- 5. Cost Summary Breakdown -->
                    <div class="p-4 sm:p-5 bg-gray-50/80 border border-gray-200 space-y-2.5 text-xs sm:text-sm">
                        <div class="flex justify-between !text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-semibold !text-gray-900">Tk. {{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between !text-gray-600">
                            <span>Shipping Charge</span>
                            <span class="font-semibold !text-gray-900">Tk. {{ number_format($order->shipping_amount, 2) }}</span>
                        </div>
                        @if(($order->discount_amount ?? 0) > 0 || ($order->coupon_discount_amount ?? 0) > 0)
                            @php $discountVal = max($order->discount_amount ?? 0, $order->coupon_discount_amount ?? 0); @endphp
                            <div class="flex justify-between !text-emerald-700 font-semibold">
                                <span>Discount {{ $order->coupon_used ? "({$order->coupon_used})" : '' }}</span>
                                <span>- Tk. {{ number_format($discountVal, 2) }}</span>
                            </div>
                        @endif
                        <div class="pt-3 border-t border-gray-200 flex justify-between items-center">
                            <span class="text-xs sm:text-sm font-bold uppercase tracking-wide !text-gray-900">Total Amount</span>
                            <span class="text-base sm:text-lg font-bold !text-emerald-700">Tk. {{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>

                    <!-- 6. Action Buttons Bar -->
                    <div class="pt-6 border-t border-gray-200 flex flex-wrap items-center justify-between gap-3 no-print">
                        <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
                            <a href="{{ auth()->check() ? route('account.order.show', $order->order_number) : route('track-order') }}" class="inline-flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 !text-white font-semibold py-2.5 px-4 rounded-none text-xs uppercase tracking-wide transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <span>Track Order</span>
                            </a>

                            <a href="{{ route('account.order.invoice', $order->order_number) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 bg-slate-900 hover:bg-black !text-white font-semibold py-2.5 px-4 rounded-none text-xs uppercase tracking-wide transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Download Invoice</span>
                            </a>
                        </div>

                        <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
                            <a href="{{ route('shop') }}" class="inline-flex items-center justify-center gap-1.5 bg-white border border-gray-300 hover:border-gray-400 !text-gray-800 font-semibold py-2.5 px-4 rounded-none text-xs uppercase tracking-wide transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span>Continue Shopping</span>
                            </a>

                            <button type="button" onclick="window.print()" class="inline-flex items-center justify-center gap-1.5 bg-gray-100 hover:bg-gray-200 !text-gray-700 font-semibold py-2.5 px-4 rounded-none text-xs uppercase tracking-wide transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Print</span>
                            </button>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-layouts.app>
