<x-layouts.app title="Order #{{ $order->order_number }} | Feriwalarhat">
    <div class="bg-gray-50 py-6 md:py-10">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumbs -->
            <nav class="flex text-xs font-medium text-gray-500 uppercase tracking-wider mb-6 md:mb-8">
                <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ route('account', 'orders') }}" class="hover:text-red-600 transition-colors">My Orders</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900">Order #{{ $order->order_number }}</span>
            </nav>

            <div class="flex flex-col md:flex-row gap-8">
                
                @include('pages.account.partials.sidebar', ['section' => 'orders'])

                <!-- Main Content -->
                <div class="flex-1">
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <!-- Header -->
                        <div class="p-6 border-b border-gray-100 flex flex-wrap items-center justify-between gap-4 bg-gray-50/50">
                            <div>
                                <h1 class="text-xl font-bold text-gray-900 uppercase tracking-widest mb-1">Order Details</h1>
                                <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Order #{{ $order->order_number }} â€¢ Placed on {{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col items-end gap-1">
                                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">Order Status</span>
                                    <span class="px-3 py-1 {{ $order->getStatusColor() }} text-[10px] font-bold rounded-full uppercase tracking-wider">
                                        {{ $order->getStatusText() }}
                                    </span>
                                </div>
                                @if($order->payment_status)
                                    <div class="flex flex-col items-end gap-1">
                                        <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">Payment Status</span>
                                        <span class="px-3 py-1 {{ $order->getPaymentStatusColor() }} text-[10px] font-bold rounded-full uppercase tracking-wider">
                                            @if($order->payment_method === 'cod' && $order->payment_status === 'pending')
                                                COD
                                            @else
                                                {{ $order->getPaymentStatusText() }}
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="p-6 space-y-8">
                            <!-- Items Table -->
                            <div>
                                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Order Items</h3>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-sm text-gray-600">
                                        <thead class="bg-gray-50 text-gray-500 text-[10px] uppercase tracking-wider font-bold border-y border-gray-100">
                                            <tr>
                                                <th class="px-4 py-3 min-w-[200px]">Product</th>
                                                <th class="px-4 py-3 text-center">Price</th>
                                                <th class="px-4 py-3 text-center">Qty</th>
                                                <th class="px-4 py-3 text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            @foreach($order->orderProducts as $item)
                                                <tr>
                                                    <td class="px-4 py-4">
                                                        <div class="flex items-center gap-3">
                                                            @if($item->product && $item->product->firstImage)
                                                                <img src="{{ $item->product->firstImage->getImageUrl() }}" alt="{{ $item->product->name }}" class="w-12 h-12 rounded object-cover border border-gray-100 flex-shrink-0">
                                                            @else
                                                                <div class="w-12 h-12 bg-gray-100 rounded border border-gray-100 flex items-center justify-center text-gray-400 flex-shrink-0">
                                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                                </div>
                                                            @endif
                                                            <div class="min-w-0">
                                                                <p class="font-bold text-gray-900 leading-tight">{{ $item->product_name ?? $item->product->name ?? 'Product' }}</p>
                                                                @if($item->variant_name)
                                                                    <p class="text-[10px] text-gray-500 uppercase tracking-wide mt-0.5">{{ $item->variant_name }}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-4 text-center whitespace-nowrap">Tk. {{ number_format($item->unit_price, 2) }}</td>
                                                    <td class="px-4 py-4 text-center whitespace-nowrap">{{ $item->quantity }}</td>
                                                    <td class="px-4 py-4 text-right font-bold text-gray-900 whitespace-nowrap">Tk. {{ number_format($item->total_price, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="border-t border-gray-100 bg-gray-50/30">
                                            <tr>
                                                <td colspan="3" class="px-4 py-3 text-right text-gray-500 font-medium whitespace-nowrap">Subtotal</td>
                                                <td class="px-4 py-3 text-right font-bold text-gray-900 whitespace-nowrap">Tk. {{ number_format($order->subtotal, 2) }}</td>
                                            </tr>
                                            @if($order->discount_amount > 0)
                                            <tr>
                                                <td colspan="3" class="px-4 py-3 text-right text-gray-500 font-medium whitespace-nowrap">Discount</td>
                                                <td class="px-4 py-3 text-right font-bold text-red-600 whitespace-nowrap">- Tk. {{ number_format($order->discount_amount, 2) }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td colspan="3" class="px-4 py-3 text-right text-gray-500 font-medium whitespace-nowrap">Shipping</td>
                                                <td class="px-4 py-3 text-right font-bold text-gray-900 whitespace-nowrap">Tk. {{ number_format($order->shipping_amount, 2) }}</td>
                                            </tr>
                                            <tr class="bg-gray-100/50">
                                                <td colspan="3" class="px-4 py-4 text-right text-sm font-bold text-gray-900 uppercase tracking-widest whitespace-nowrap">Total</td>
                                                <td class="px-4 py-4 text-right text-lg font-bold text-red-600 whitespace-nowrap">Tk. {{ number_format($order->total_amount, 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Addresses & Summary -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                                <!-- Shipping Address -->
                                <div class="bg-gray-50 rounded-lg p-5 border border-gray-100">
                                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        Shipping Address
                                    </h3>
                                    <div class="text-sm text-gray-700 leading-relaxed">
                                        <p class="font-bold text-gray-900 mb-1">{{ $order->orderAddress->name ?? $user->name }}</p>
                                        <p>{{ $order->orderAddress->address ?? 'N/A' }}</p>
                                        <p>{{ $order->orderAddress->district->name ?? '' }}</p>
                                        <p class="mt-2 text-xs text-gray-500 font-medium tracking-wide">Phone: {{ $order->orderAddress->phone ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <!-- Payment Info -->
                                <div class="bg-gray-50 rounded-lg p-5 border border-gray-100">
                                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                        Payment Information
                                    </h3>
                                    <div class="text-sm text-gray-700 leading-relaxed">
                                        <p class="mb-2"><span class="text-gray-500 font-medium">Method:</span> <span class="font-bold text-gray-900 uppercase tracking-wide">{{ str_replace('_', ' ', $order->payment_method) }}</span></p>
                                        <p class="mb-2">
                                            <span class="text-gray-500 font-medium">Status:</span> 
                                            <span class="font-bold text-gray-900 uppercase tracking-wide">
                                                @if($order->payment_method === 'cod' && $order->payment_status === 'pending')
                                                    COD (Pending)
                                                @else
                                                    {{ $order->payment_status }}
                                                @endif
                                            </span>
                                        </p>
                                        @if($order->notes)
                                            <div class="mt-4 pt-4 border-t border-gray-200">
                                                <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mb-1">Order Notes:</p>
                                                <p class="text-xs italic">{{ $order->notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Footer Actions -->
                        <div class="p-6 bg-gray-50 border-t border-gray-100 flex items-center justify-end">
                            <a href="{{ route('account', 'orders') }}" class="text-xs font-bold text-gray-500 hover:text-gray-900 uppercase tracking-widest transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                Back to Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
