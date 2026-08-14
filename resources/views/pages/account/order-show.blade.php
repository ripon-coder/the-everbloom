<x-layouts.app title="Order #{{ $order->order_number }} | Feriwalarhat">
    <div class="account-page-container bg-gray-50 py-6 md:py-10">
        <div class="max-w-[1200px] mx-auto px-1.5 sm:px-6 lg:px-8">
            
            <!-- Breadcrumbs -->
            <nav class="flex text-xs font-medium text-gray-500 uppercase tracking-wider mb-6 md:mb-8">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ route('account', 'orders') }}" class="hover:text-primary transition-colors">My Orders</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900">Order #{{ $order->order_number }}</span>
            </nav>

            <div class="flex flex-col md:flex-row gap-8">
                
                <!-- Sidebar -->
                @include('pages.account.partials.sidebar', ['section' => 'orders'])

                <!-- Main Content -->
                <div class="flex-1 space-y-6">
                    
                    <!-- Section Header -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-gray-200">
                        <div>
                            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-widest">Order Details #{{ $order->order_number }}</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('account.order.invoice', $order->order_number) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 border border-emerald-600 px-3.5 py-1.5 transition-colors shadow-xs">
                                <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Download Invoice</span>
                            </a>
                            <a href="{{ route('account', 'orders') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-700 hover:text-primary uppercase tracking-wider border border-gray-300 hover:border-gray-400 px-3 py-1.5 bg-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                <span>Back to Orders</span>
                            </a>
                        </div>
                    </div>

                    <!-- Order Overview Card -->
                    <div class="bg-white border border-gray-200 p-5 sm:p-6 space-y-6">
                        <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-gray-100">
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Order Status</span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold {{ $order->getStatusColor() }} border border-gray-200">
                                    <span class="w-2 h-2 rounded-full bg-current opacity-75"></span>
                                    {{ $order->getStatusText() }}
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Payment Status</span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold {{ $order->getPaymentStatusColor() }} border border-gray-200">
                                    <span class="w-2 h-2 rounded-full bg-current opacity-75"></span>
                                    @if($order->payment_method === 'cod' && $order->payment_status === 'pending')
                                        COD Pending
                                    @else
                                        {{ $order->getPaymentStatusText() }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Order Progress Stepper Timeline -->
                        <div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Order Status Timeline</h3>
                            @php
                                $statuses = ['pending' => 'Order Placed', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered'];
                                $statusKeys = array_keys($statuses);
                                $currentStatusIndex = array_search($order->status, $statusKeys);
                                if ($currentStatusIndex === false) $currentStatusIndex = 0;
                                $isCancelled = $order->status === 'cancelled';
                            @endphp

                            @if($isCancelled)
                                <div class="p-4 bg-red-50 border border-red-200 text-center">
                                    <p class="text-sm font-bold text-red-700">This order has been cancelled.</p>
                                </div>
                            @else
                                <div class="relative flex items-center justify-between">
                                    <!-- Progress Line -->
                                    <div class="absolute left-0 top-1/2 transform -translate-y-1/2 h-1 bg-gray-100 w-full rounded-full z-0"></div>
                                    <div class="absolute left-0 top-1/2 transform -translate-y-1/2 h-1 bg-emerald-500 rounded-full z-0 transition-all duration-500"
                                         style="width: {{ count($statusKeys) > 1 ? ($currentStatusIndex / (count($statusKeys) - 1)) * 100 : 0 }}%"></div>

                                    @foreach($statuses as $key => $label)
                                        @php
                                            $index = array_search($key, $statusKeys);
                                            $isCompleted = $index <= $currentStatusIndex;
                                            $isCurrent = $index === $currentStatusIndex;
                                        @endphp
                                        <div class="relative z-10 flex flex-col items-center group">
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300 {{ $isCompleted ? 'bg-emerald-600 text-white shadow-sm ring-4 ring-white' : 'bg-white border-2 border-gray-200 text-gray-400 ring-4 ring-white' }}">
                                                @if($isCompleted && !$isCurrent)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @else
                                                    {{ $index + 1 }}
                                                @endif
                                            </div>
                                            <span class="mt-2 text-[11px] font-bold tracking-tight text-center {{ $isCurrent ? 'text-emerald-700' : ($isCompleted ? 'text-gray-800' : 'text-gray-400') }}">
                                                {{ $label }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Ordered Items List (No HTML Tables!) -->
                    <div class="bg-white border border-gray-200 overflow-hidden">
                        <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-xs font-bold text-gray-900 uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z"></path>
                                </svg>
                                Purchased Items ({{ $order->orderProducts->count() }})
                            </h3>
                        </div>

                        <!-- Card List -->
                        <div class="divide-y divide-gray-100">
                            @foreach($order->orderProducts as $item)
                                @php
                                    $product = $item->product;
                                    $imageUrl = null;
                                    if ($product) {
                                        $imageUrl = $product->firstImage ? $product->firstImage->getImageUrl() : ($product->anyImage ? $product->anyImage->getImageUrl() : null);
                                    }
                                    
                                    // Variant name
                                    $variantTitle = $item->variant_name;
                                    if (!$variantTitle && $item->productVariant && $item->productVariant->variantAttributes) {
                                        $variantTitle = $item->productVariant->variantAttributes->map(fn($attr) => ($attr->attribute->name ?? '') . ': ' . ($attr->attributeValue->value ?? ''))->join(', ');
                                    }
                                @endphp
                                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50/50 transition-colors">
                                    <div class="flex items-center gap-4 min-w-0">
                                        <!-- Product Image -->
                                        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gray-50 border border-gray-200 flex-shrink-0 flex items-center justify-center overflow-hidden">
                                            @if($imageUrl)
                                                <img src="{{ $imageUrl }}" alt="{{ $item->product_name ?? ($product->name ?? 'Product') }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            @endif
                                        </div>

                                        <!-- Product Info -->
                                        <div class="min-w-0 flex-1">
                                            <h4 class="font-bold text-gray-900 text-sm leading-snug truncate">
                                                @if($product)
                                                    <a href="{{ route('product.show', $product->slug ?? $product->id) }}" class="hover:text-emerald-600 transition-colors">
                                                        {{ $item->product_name ?? $product->name }}
                                                    </a>
                                                @else
                                                    {{ $item->product_name ?? 'Product' }}
                                                @endif
                                            </h4>

                                            @if($variantTitle)
                                                <div class="mt-0.5">
                                                    <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                                        {{ $variantTitle }}
                                                    </span>
                                                </div>
                                            @endif

                                            <div class="mt-1 flex items-center gap-2.5 text-xs text-gray-500">
                                                <span class="font-medium text-gray-700">Tk. {{ number_format($item->unit_price, 2) }}</span>
                                                <span class="text-gray-300">&times;</span>
                                                <span class="px-2 py-0.5 bg-gray-100 font-bold text-gray-800 text-[10px] border border-gray-200">
                                                    Qty: {{ $item->quantity }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Price Subtotal -->
                                    <div class="text-right sm:text-right border-t sm:border-t-0 border-gray-100 pt-2 sm:pt-0 flex sm:flex-col justify-between items-center sm:items-end">
                                        <span class="sm:hidden text-xs font-semibold text-gray-400">Total Price:</span>
                                        <span class="text-sm sm:text-base font-black text-gray-900">
                                            Tk. {{ number_format($item->total_price, 2) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Financial Summary Breakdown -->
                        <div class="p-5 bg-gray-50/80 border-t border-gray-200 space-y-2.5 text-xs sm:text-sm">
                            <div class="flex justify-between items-center text-gray-600">
                                <span>Items Subtotal</span>
                                <span class="font-bold text-gray-900">Tk. {{ number_format($order->subtotal, 2) }}</span>
                            </div>

                            @if(($order->discount_amount ?? 0) > 0 || ($order->coupon_discount_amount ?? 0) > 0)
                                @php $discountVal = max($order->discount_amount ?? 0, $order->coupon_discount_amount ?? 0); @endphp
                                <div class="flex justify-between items-center text-emerald-600 font-semibold">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        Discount {{ $order->coupon_used ? "({$order->coupon_used})" : '' }}
                                    </span>
                                    <span>- Tk. {{ number_format($discountVal, 2) }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center text-gray-600">
                                <span class="flex items-center gap-1.5">
                                    Shipping Charge
                                    @if($order->orderAddress && $order->orderAddress->district)
                                        <span class="text-[10px] px-1.5 py-0.5 bg-gray-200 text-gray-700 font-bold border border-gray-300/60">
                                            {{ $order->orderAddress->district->name }}
                                        </span>
                                    @endif
                                </span>
                                <span class="font-bold text-gray-900">Tk. {{ number_format($order->shipping_amount, 2) }}</span>
                            </div>

                            <div class="pt-3 border-t border-gray-200 flex justify-between items-center">
                                <span class="text-xs font-black text-gray-900 uppercase tracking-wider">Total Amount</span>
                                <span class="text-lg sm:text-xl font-black text-emerald-600">
                                    Tk. {{ number_format($order->total_amount, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Address & Payment Details Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Shipping Address Card -->
                        <div class="bg-white border border-gray-200 p-5 space-y-3">
                            <div class="flex items-center gap-2.5 pb-2.5 border-b border-gray-100">
                                <div class="w-8 h-8 rounded bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Shipping Address</h3>
                                </div>
                            </div>

                            @php
                                $addr = $order->orderAddress;
                                $name = $addr->name ?? ($user->name ?? 'Guest Customer');
                                $phone = $addr->phone_number ?? ($addr->phone ?? 'N/A');
                                $addressText = $addr->address ?? 'N/A';
                                $districtName = $addr->district->name ?? '';
                            @endphp

                            <div class="space-y-1.5 text-xs">
                                <p class="font-bold text-gray-900 text-sm">{{ $name }}</p>
                                <p class="text-gray-600 leading-relaxed">{{ $addressText }}</p>
                                @if($districtName)
                                    <p class="text-gray-500 font-semibold flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $districtName }}
                                    </p>
                                @endif
                                <div class="pt-1.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 text-gray-700 font-bold text-xs border border-gray-200">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        {{ $phone }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Info Card -->
                        <div class="bg-white border border-gray-200 p-5 space-y-3">
                            <div class="flex items-center gap-2.5 pb-2.5 border-b border-gray-100">
                                <div class="w-8 h-8 rounded bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Payment Information</h3>
                                </div>
                            </div>

                            <div class="space-y-2.5 text-xs">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 font-medium">Method</span>
                                    <span class="font-bold text-gray-900 uppercase tracking-wide px-2 py-0.5 bg-gray-100 border border-gray-200 text-[11px]">
                                        {{ str_replace('_', ' ', $order->payment_method ?? 'COD') }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 font-medium">Payment Status</span>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 text-[11px] font-bold {{ $order->getPaymentStatusColor() }} border border-gray-200">
                                        @if($order->payment_method === 'cod' && $order->payment_status === 'pending')
                                            COD (Pending)
                                        @else
                                            {{ $order->getPaymentStatusText() }}
                                        @endif
                                    </span>
                                </div>

                                @if($order->notes)
                                    <div class="pt-2 border-t border-gray-100">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Customer Notes</span>
                                        <p class="text-xs text-gray-600 italic bg-gray-50 p-2 border border-gray-200">
                                            "{{ $order->notes }}"
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
