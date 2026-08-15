@extends('admin.layouts.app')

@section('title', 'Coupons Management')

@section('content')
    @php
        $currentSearch = request('search', '');
        $currentStatus = request('status', '');
        $currentType = request('type', '');
        $hasFilters = filled($currentSearch) || filled($currentStatus) || filled($currentType);
    @endphp

    <div class="space-y-6" x-data="{
        copiedText: null,
        copyToClipboard(text, label) {
            navigator.clipboard.writeText(text);
            this.copiedText = label;
            setTimeout(() => this.copiedText = null, 2000);
        }
    }">
        <!-- Single Unified Coupons Card (Sharp / Non-rounded) -->
        <div class="bg-white border border-gray-200 shadow-sm">
            
            <!-- Card Top Header -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-1.5">
                            <span class="text-gray-700 font-semibold">Marketing</span>
                            <span class="text-gray-300">/</span>
                            <span>Coupons</span>
                        </nav>

                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Coupons Management</h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $coupons->total() }} Total {{ Str::plural('Coupon', $coupons->total()) }}
                            </span>
                        </div>
                    </div>

                    <!-- Header Action Buttons -->
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($hasFilters)
                            <a href="{{ route('admin.coupons.index') }}"
                               class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear Filters
                            </a>
                        @endif

                        <a href="{{ route('admin.coupons.create') }}"
                           class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Coupon
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search & Filters Bar -->
            <div class="p-5 sm:p-6 bg-gray-50/50 border-b border-gray-200">
                <form action="{{ route('admin.coupons.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                    
                    <!-- Search Input -->
                    <div class="lg:col-span-4">
                        <label for="search" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Search Coupons</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ $currentSearch }}"
                                   placeholder="Search Code, ID..."
                                   class="w-full pl-9 pr-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        </div>
                    </div>

                    <!-- Discount Type Filter -->
                    <div class="lg:col-span-3">
                        <label for="type" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Type</label>
                        <select name="type" id="type"
                                class="w-full py-2 px-3 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">All Types</option>
                            <option value="percentage" {{ $currentType == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                            <option value="fixed_amount" {{ $currentType == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="lg:col-span-3">
                        <label for="status" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Status</label>
                        <select name="status" id="status"
                                class="w-full py-2 px-3 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">All Statuses</option>
                            <option value="active" {{ $currentStatus == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $currentStatus == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="expired" {{ $currentStatus == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>

                    <!-- Submit & Reset Buttons -->
                    <div class="lg:col-span-2 flex items-end gap-2">
                        <button type="submit"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-3 text-xs transition duration-150 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filter
                        </button>

                        @if ($hasFilters)
                            <a href="{{ route('admin.coupons.index') }}"
                               class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-2.5 text-xs transition flex items-center justify-center"
                               title="Reset Search">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Coupons Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                        <tr>
                            <th class="px-5 py-3.5">Coupon Code</th>
                            <th class="px-5 py-3.5">Discount</th>
                            <th class="px-5 py-3.5">Min Spend</th>
                            <th class="px-5 py-3.5">Usage Limit</th>
                            <th class="px-5 py-3.5">Validity Period</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($coupons as $coupon)
                            <tr class="hover:bg-gray-50/80 transition duration-150">
                                
                                <!-- Coupon Code -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <div class="flex items-center space-x-1.5">
                                            <span class="font-mono font-bold text-xs bg-blue-50 text-blue-700 px-2 py-0.5 border border-blue-200">
                                                {{ $coupon->code }}
                                            </span>
                                            <button type="button" @click="copyToClipboard('{{ $coupon->code }}', '{{ $coupon->id }}')" 
                                                    class="text-gray-400 hover:text-gray-600 transition" title="Copy Code">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <p class="text-[10px] text-gray-400 font-mono">#{{ $coupon->id }}</p>
                                    </div>
                                </td>

                                <!-- Discount Value & Type -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="space-y-0.5">
                                        @if ($coupon->type == 'percentage')
                                            <span class="font-bold text-gray-900 text-xs">{{ $coupon->value }}% OFF</span>
                                            <span class="block text-[10px] text-purple-700 bg-purple-50 px-1.5 py-0.2 border border-purple-200 inline-block">Percentage</span>
                                        @else
                                            <span class="font-bold text-gray-900 text-xs">{{ $currency_sign ?? '$' }}{{ number_format($coupon->value, 2) }} OFF</span>
                                            <span class="block text-[10px] text-emerald-700 bg-emerald-50 px-1.5 py-0.2 border border-emerald-200 inline-block">Fixed Amount</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Min Order Amount -->
                                <td class="px-5 py-4 whitespace-nowrap text-gray-700 font-medium">
                                    {{ $currency_sign ?? '$' }}{{ number_format($coupon->min_order_amount, 2) }}
                                </td>

                                <!-- Usage Progress -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if ($coupon->usage_limit)
                                        <div class="space-y-1">
                                            <span class="inline-flex px-2 py-0.5 text-[11px] font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                                {{ $coupon->used_count }} / {{ $coupon->usage_limit }} used
                                            </span>
                                            <div class="w-24 bg-gray-200 h-1.5">
                                                @php $pct = min(100, round(($coupon->used_count / $coupon->usage_limit) * 100)); @endphp
                                                <div class="bg-blue-600 h-1.5" style="width: {{ $pct }}%"></div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 text-[11px] font-medium bg-gray-50 text-gray-600 border border-gray-200">
                                            {{ $coupon->used_count }} / &infin; (Unlimited)
                                        </span>
                                    @endif
                                </td>

                                <!-- Validity Period -->
                                <td class="px-5 py-4 whitespace-nowrap text-gray-600 text-[11px]">
                                    <div>
                                        <span class="text-gray-400">Start:</span> {{ $coupon->start_date ? $coupon->start_date->format('M d, Y') : 'N/A' }}
                                    </div>
                                    <div>
                                        <span class="text-gray-400">End:</span> {{ $coupon->end_date ? $coupon->end_date->format('M d, Y') : 'N/A' }}
                                    </div>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if ($coupon->status == 'active')
                                        <span class="inline-flex px-2.5 py-0.5 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Active
                                        </span>
                                    @elseif ($coupon->status == 'inactive')
                                        <span class="inline-flex px-2.5 py-0.5 text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                            Inactive
                                        </span>
                                    @else
                                        <span class="inline-flex px-2.5 py-0.5 text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            {{ ucfirst($coupon->status) }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Action Buttons (View + Edit, no delete button) -->
                                <td class="px-5 py-4 whitespace-nowrap text-right font-medium">
                                    <div class="flex items-center justify-end space-x-1.5">
                                        <a href="{{ route('admin.coupons.show', $coupon->id) }}"
                                           class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition"
                                           title="View Coupon">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View
                                        </a>
                                        <a href="{{ route('admin.coupons.edit', $coupon->id) }}"
                                           class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition"
                                           title="Edit Coupon">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div class="w-12 h-12 bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-base font-semibold text-gray-900">No coupons found</p>
                                        <p class="text-xs text-gray-500">
                                            @if ($hasFilters)
                                                No promotional coupons match your search criteria.
                                            @else
                                                Get started by creating your first promotional coupon.
                                            @endif
                                        </p>
                                        @if ($hasFilters)
                                            <a href="{{ route('admin.coupons.index') }}"
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
            @if ($coupons->hasPages())
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    {{ $coupons->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection
