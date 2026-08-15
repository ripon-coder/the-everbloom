@extends('admin.layouts.app')

@section('title', 'Customer Details - ' . $customer->name)

@section('content')
    <div class="space-y-6">
        <!-- Single Unified Customer Card (Sharp / Non-rounded) -->
        <div class="bg-white border border-gray-200 shadow-sm">
            
            <!-- Card Top Header -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-1.5">
                            <a href="{{ route('admin.customers.index') }}" class="hover:text-gray-900 transition">Customers</a>
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-700 font-semibold truncate max-w-[200px]">{{ $customer->name }}</span>
                        </nav>

                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $customer->name }}</h1>
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                #{{ $customer->id }}
                            </span>
                            <form action="{{ route('admin.customers.update-status', $customer->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold border transition {{ $customer->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' }}"
                                        title="Click to toggle status">
                                    {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Back Button -->
                    <a href="{{ route('admin.customers.index') }}"
                       class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-sm transition">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Customers
                    </a>
                </div>
            </div>

            <!-- Profile Summary Bar -->
            <div class="p-5 sm:p-6 border-b border-gray-200 bg-gray-50/50">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                    <div class="p-3 bg-white border border-gray-200">
                        <span class="text-gray-500 block mb-1">Email Address</span>
                        <span class="font-semibold text-gray-900 break-all">{{ $customer->email }}</span>
                    </div>

                    <div class="p-3 bg-white border border-gray-200">
                        <span class="text-gray-500 block mb-1">Phone Number</span>
                        <span class="font-semibold text-gray-900">{{ $customer->phone ?: 'Not provided' }}</span>
                    </div>

                    <div class="p-3 bg-white border border-gray-200">
                        <span class="text-gray-500 block mb-1">Total Orders</span>
                        <span class="font-bold text-gray-900 text-sm">{{ $customer->orders ? $customer->orders->count() : 0 }}</span>
                    </div>

                    <div class="p-3 bg-white border border-gray-200">
                        <span class="text-gray-500 block mb-1">Member Since</span>
                        <span class="font-semibold text-gray-900">{{ $customer->created_at ? $customer->created_at->format('M d, Y · H:i') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Section -->
            <div class="p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700">Recent Customer Orders</h2>
                    <span class="text-xs text-gray-500">Showing last {{ count($customer->orders) }} orders</span>
                </div>

                <div class="overflow-x-auto border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                        <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                            <tr>
                                <th class="px-5 py-3">Order #</th>
                                <th class="px-5 py-3">Placed Date</th>
                                <th class="px-5 py-3">Total Amount</th>
                                <th class="px-5 py-3">Payment Status</th>
                                <th class="px-5 py-3">Fulfillment Status</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($customer->orders as $order)
                                <tr class="hover:bg-gray-50/80 transition duration-150">
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="font-bold text-blue-600 hover:underline">
                                            #{{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-gray-500">
                                        {{ $order->created_at ? $order->created_at->format('M d, Y · H:i') : 'N/A' }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap font-bold text-gray-900">
                                        {{ $currency_sign ?? '$' }}{{ number_format($order->total_amount, 2) }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-0.5 text-[11px] font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                            {{ ucfirst($order->payment_status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-0.5 text-[11px] font-semibold {{ $order->getStatusColor() }} border border-gray-200">
                                            {{ $order->getStatusText() }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap text-right font-medium">
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                           class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition">
                                            View Order &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        No orders placed by this customer yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                <a href="{{ route('admin.customers.index') }}"
                   class="px-4 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 transition">
                    Back to All Customers
                </a>
            </div>

        </div>
    </div>
@endsection
