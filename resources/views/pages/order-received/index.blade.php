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
                padding: 20px !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>

    <div class="order-received-page-container bg-gray-50/70 min-h-screen py-2 md:py-2">
        <div class="max-w-5xl mx-auto px-1.5 sm:px-6 lg:px-8">
            
            <!-- SINGLE PRINTABLE CARD -->
            <div id="printable-order-card" class="bg-white border border-gray-200 rounded-none shadow-sm overflow-hidden">
                
                <!-- Card Header Accent Line -->
                <div class="h-2 bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600"></div>

                <div class="p-6 sm:p-10 space-y-8">
                    
                    <!-- 1. Success Icon & Header -->
                    <div class="text-center space-y-3 pb-6 border-b border-gray-100">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-emerald-50 text-emerald-600 rounded-none flex items-center justify-center mx-auto border-4 border-emerald-100/80 shadow-inner">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Thank You! Your Order is Placed</h1>
                        <p class="text-xs sm:text-sm text-gray-600 max-w-md mx-auto">We've received your order and are preparing it for delivery. A confirmation has been saved to your account.</p>

                        <div class="pt-2">
                            <span class="inline-flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded-none text-xs font-mono font-bold tracking-wider shadow-xs">
                                <span>ORDER NUMBER:</span>
                                <span class="text-amber-400">#{{ $order->order_number }}</span>
                            </span>
                        </div>
                    </div> 

                    <!-- 2. Order & Customer Info Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 p-4 bg-gray-50/80 rounded-none border border-gray-100 text-xs">
                        <div>
                            <span class="font-extrabold text-gray-400 uppercase tracking-widest block mb-1">Order Date</span>
                            <span class="font-bold text-gray-900 block">{{ $order->created_at ? $order->created_at->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="font-extrabold text-gray-400 uppercase tracking-widest block mb-1">Payment Method</span>
                            <span class="font-bold text-gray-900 block uppercase">{{ strtoupper($order->payment_method ?? 'COD') }}</span>
                        </div>
                        <div>
                            <span class="font-extrabold text-gray-400 uppercase tracking-widest block mb-1">Status</span>
                            <span class="inline-flex items-center font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-none text-[11px] uppercase">
                                {{ ucfirst($order->status ?? 'pending') }}
                            </span>
                        </div>
                        <div>
                            <span class="font-extrabold text-gray-400 uppercase tracking-widest block mb-1">Total Amount</span>
                            <span class="font-black text-primary block text-sm">Tk. {{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>

                    <!-- 3. Delivery Address -->
                    @if($order->orderAddress)
                        <div class="p-4 rounded-none border border-gray-100 bg-gray-50/50 space-y-1.5 text-xs sm:text-sm">
                            <h3 class="text-xs font-extrabold text-gray-900 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Delivery Address</span>
                            </h3>
                            <p class="font-extrabold text-gray-900 text-sm">{{ $order->orderAddress->name }} &bull; {{ $order->orderAddress->phone_number }}</p>
                            <p class="text-gray-600">{{ $order->orderAddress->address }}, {{ optional($order->orderAddress->district)->name }}</p>
                        </div>
                    @endif

                    <!-- 4. Ordered Items List -->
                    <div class="space-y-4">
                        <h3 class="text-xs font-extrabold text-gray-900 uppercase tracking-wider border-b border-gray-100 pb-2">Order Items</h3>
                        
                        <div class="divide-y divide-gray-100">
                            @foreach($order->orderProducts as $item)
                                <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-14 h-14 bg-gray-50 rounded-none border border-gray-200 p-1 flex-shrink-0 flex items-center justify-center overflow-hidden">
                                            @php
                                                $productObj = $item->product;
                                                $imgUrl = $productObj?->firstImage?->getImageUrl() ?? $productObj?->anyImage?->getImageUrl();
                                            @endphp
                                            @if($imgUrl)
                                                <img src="{{ $imgUrl }}" alt="{{ optional($productObj)->name }}" class="max-w-full max-h-full object-contain">
                                            @else
                                                <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400 text-[10px] font-bold">No Image</div>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="text-xs sm:text-sm font-extrabold text-gray-900 leading-tight">
                                                {{ optional($item->product)->name ?? 'Product' }}
                                            </h4>
                                            @if($item->productVariant && $item->productVariant->variantAttributes)
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    @foreach($item->productVariant->variantAttributes as $va)
                                                        <span class="inline-flex items-center text-[10px] font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-none">
                                                            <span class="text-slate-400 mr-1">{{ optional($va->attribute)->name }}:</span>
                                                            {{ optional($va->attributeValue)->value }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <div class="text-[11px] text-gray-500 font-medium mt-1">
                                                <span>Qty: {{ $item->quantity }}</span> &times; <span class="text-gray-900 font-bold">Tk. {{ number_format($item->unit_price, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right font-black text-xs sm:text-sm text-gray-900 whitespace-nowrap">
                                        Tk. {{ number_format($item->total_price, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- 5. Cost Summary Breakdown -->
                    <div class="pt-4 border-t border-gray-100 space-y-2.5 text-xs sm:text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-bold text-gray-900">Tk. {{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping Charge</span>
                            <span class="font-bold text-gray-900">Tk. {{ number_format($order->shipping_amount, 2) }}</span>
                        </div>
                        @if($order->coupon_discount_amount > 0)
                            <div class="flex justify-between text-primary font-semibold">
                                <span>Discount (Coupon: {{ $order->coupon_used }})</span>
                                <span>- Tk. {{ number_format($order->coupon_discount_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="pt-3 border-t border-gray-200 flex justify-between items-end">
                            <span class="text-sm font-extrabold text-gray-900 uppercase">Total Amount</span>
                            <span class="text-xl font-black text-primary">Tk. {{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>

                    <!-- 6. Bottom Actions (Hidden on Print) -->
                    <div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 no-print">
                        <a href="{{ auth()->check() ? route('account.order.show', $order->order_number) : route('track-order') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-dark text-white font-bold py-3 px-6 rounded-none text-xs uppercase tracking-wider transition-colors shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span>Track Order</span>
                        </a>

                        <a href="{{ route('shop') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-900 hover:bg-black text-white font-bold py-3 px-6 rounded-none text-xs uppercase tracking-wider transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span>Continue Shopping</span>
                        </a>

                        <button type="button" onclick="window.print()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-5 rounded-none text-xs uppercase tracking-wider transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Print</span>
                        </button>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-layouts.app>
