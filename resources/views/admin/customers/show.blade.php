@extends('admin.layouts.app')

@section('title', 'Customer Details')

@section('content')
    <div class="p-6">
        <div class="flex items-center space-x-4 mb-6">
            <a href="{{ route('admin.customers.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Customer Details</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Customer Info Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                    <div class="flex flex-col items-center">
                        <div class="h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-3xl font-bold mb-4">
                            {{ substr($customer->name, 0, 1) }}
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $customer->name }}</h2>
                        <p class="text-gray-500 text-sm mb-4">Customer since {{ $customer->created_at->format('M Y') }}</p>
                        
                        <div class="mb-4">
                            <form action="{{ route('admin.customers.update-status', $customer->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-1 rounded-full text-sm font-medium transition-colors {{ $customer->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                    {{ $customer->is_active ? 'Account Active' : 'Account Inactive' }}
                                </button>
                            </form>
                        </div>
                        
                        <div class="w-full space-y-3 border-t border-gray-100 pt-4 mt-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Email:</span>
                                <span class="font-medium text-gray-900">{{ $customer->email }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Phone:</span>
                                <span class="font-medium text-gray-900">{{ $customer->phone ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Total Orders:</span>
                                <span class="font-medium text-gray-900">{{ $customer->orders->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Card -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h3 class="text-lg font-bold text-gray-800">Recent Orders</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Order #</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($customer->orders as $order)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:underline font-medium">
                                                {{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            {{ $currency_sign ?? '$' }}{{ number_format($order->total_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full font-medium {{ $order->getStatusColor() }}">
                                                {{ $order->getStatusText() }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                            No orders found for this customer.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
