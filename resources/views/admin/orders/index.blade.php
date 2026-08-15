@extends('admin.layouts.app')

@section('title', 'Orders Management')

@section('content')
    @php
        $currency = $currency_sign ?? 'Tk.';
        $currentStatus = request('status', '');
        $currentPaymentStatus = request('payment_status', '');
        $currentSearch = request('search', '');
        $hasFilters = filled($currentStatus) || filled($currentPaymentStatus) || filled($currentSearch);
    @endphp

    <div class="space-y-6" x-data="{
        copiedText: null,
        copyToClipboard(text, label) {
            navigator.clipboard.writeText(text);
            this.copiedText = label;
            setTimeout(() => this.copiedText = null, 2000);
        }
    }">
        <!-- Single Unified Orders Card (Sharp / Non-rounded) -->
        <div class="bg-white border border-gray-200 shadow-sm">
            
            <!-- Card Top Header -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-1.5">
                            <span class="text-gray-700 font-semibold">Orders</span>
                            <span class="text-gray-300">/</span>
                            <span>All Customer Orders</span>
                        </nav>

                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Orders Management</h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $orders->total() }} Total {{ Str::plural('Order', $orders->total()) }}
                            </span>
                        </div>
                    </div>

                    <!-- Header Actions -->
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($hasFilters)
                            <a href="{{ route('admin.orders.index') }}"
                               class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear Filters
                            </a>
                        @endif

                        <a href="{{ route('admin.orders.index') }}"
                           class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-sm transition">
                            <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Refresh
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search & Filters Bar -->
            <div class="p-5 sm:p-6 bg-gray-50/50 border-b border-gray-200">
                <form action="{{ route('admin.orders.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                    
                    <!-- Search Input -->
                    <div class="lg:col-span-4">
                        <label for="search" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Search Orders</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ $currentSearch }}"
                                   placeholder="Search Order #, Customer, Phone, Email..."
                                   class="w-full pl-9 pr-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        </div>
                    </div>

                    <!-- Order Status Dropdown -->
                    <div class="lg:col-span-3">
                        <label for="status" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Order Status</label>
                        <select name="status" id="status"
                                class="w-full py-2 px-3 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">All Statuses</option>
                            @foreach(\App\Models\Order::getStatusOptions() as $value => $label)
                                <option value="{{ $value }}" {{ $currentStatus == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Status Dropdown -->
                    <div class="lg:col-span-3">
                        <label for="payment_status" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Payment Status</label>
                        <select name="payment_status" id="payment_status"
                                class="w-full py-2 px-3 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">All Payments</option>
                            @foreach(\App\Models\Order::getPaymentStatusOptions() as $value => $label)
                                <option value="{{ $value }}" {{ $currentPaymentStatus == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Submit & Reset Buttons -->
                    <div class="lg:col-span-2 flex items-end gap-2">
                        <button type="submit"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 text-xs transition duration-150 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filter
                        </button>

                        @if ($hasFilters)
                            <a href="{{ route('admin.orders.index') }}"
                               class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-3 text-xs transition flex items-center justify-center"
                               title="Reset Search">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Orders Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                        <tr>
                            <th class="px-5 py-3.5">Order Info</th>
                            <th class="px-5 py-3.5">Customer Details</th>
                            <th class="px-5 py-3.5">Items</th>
                            <th class="px-5 py-3.5">Total Amount</th>
                            <th class="px-5 py-3.5">Order Status</th>
                            <th class="px-5 py-3.5">Payment</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($orders as $order)
                            @php
                                $customerName = $order->user->name ?? ($order->orderAddress->name ?? 'Guest User');
                                $customerEmail = $order->user->email ?? null;
                                $customerPhone = $order->orderAddress->phone_number ?? null;
                                $customerDistrict = $order->orderAddress->district?->name ?? null;
                                $itemsCount = $order->orderProducts->count();
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition duration-150">
                                
                                <!-- Order # & Date -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <div class="flex items-center space-x-1.5">
                                            <a href="{{ route('admin.orders.show', $order->id) }}" 
                                               class="font-mono font-bold text-blue-600 hover:text-blue-800 text-xs">
                                                {{ $order->order_number }}
                                            </a>
                                            <button type="button" @click="copyToClipboard('{{ $order->order_number }}', '{{ $order->order_number }}')" 
                                                    class="text-gray-400 hover:text-gray-600 transition" title="Copy Order Number">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="flex items-center space-x-2 text-[11px] text-gray-500">
                                            <span>#{{ $order->id }}</span>
                                            <span>&bull;</span>
                                            <span>{{ $order->created_at ? $order->created_at->format('M d, Y · H:i') : 'N/A' }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Customer Info -->
                                <td class="px-5 py-4">
                                    <div class="space-y-0.5 min-w-[170px]">
                                        <p class="font-semibold text-gray-900 text-xs truncate">{{ $customerName }}</p>
                                        
                                        @if ($customerPhone)
                                            <p class="text-[11px] text-gray-600 font-mono flex items-center">
                                                <svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                </svg>
                                                {{ $customerPhone }}
                                            </p>
                                        @elseif ($customerEmail)
                                            <p class="text-[11px] text-gray-500 truncate">{{ $customerEmail }}</p>
                                        @endif

                                        @if ($customerDistrict)
                                            <span class="inline-block mt-0.5 text-[10px] font-medium text-gray-600 bg-gray-100 px-1.5 py-0.2 border border-gray-200">
                                                📍 {{ $customerDistrict }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Items -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-semibold text-gray-900">
                                            {{ $itemsCount }} {{ Str::plural('item', $itemsCount) }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Total Amount -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="space-y-0.5">
                                        <span class="text-sm font-bold text-gray-900">
                                            {{ $currency }}{{ number_format($order->total_amount, 2) }}
                                        </span>
                                        @if ($order->shipping_amount > 0)
                                            <p class="text-[10px] text-gray-500">
                                                Incl. Shipping: {{ $currency }}{{ number_format($order->shipping_amount, 2) }}
                                            </p>
                                        @endif
                                    </div>
                                </td>

                                <!-- Order Status Badge -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-0.5 text-xs font-semibold {{ $order->getStatusColor() }} border border-gray-200">
                                        {{ $order->getStatusText() }}
                                    </span>
                                </td>

                                <!-- Payment Status Badge -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-0.5 text-xs font-semibold {{ $order->getPaymentStatusColor() }} border border-gray-200">
                                        {{ $order->getPaymentStatusText() }}
                                    </span>
                                </td>

                                <!-- Action Buttons -->
                                <td class="px-5 py-4 whitespace-nowrap text-right font-medium">
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition"
                                       title="View Full Order Details">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div class="w-12 h-12 bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                        <p class="text-base font-semibold text-gray-900">No orders found</p>
                                        <p class="text-xs text-gray-500">
                                            @if ($hasFilters)
                                                No orders matched your current search filters.
                                            @else
                                                There are currently no customer orders in the system.
                                            @endif
                                        </p>
                                        @if ($hasFilters)
                                            <a href="{{ route('admin.orders.index') }}"
                                               class="text-xs font-semibold text-blue-600 hover:text-blue-700 underline">
                                                Clear all search filters
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Card Pagination -->
            @if ($orders->hasPages())
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection
