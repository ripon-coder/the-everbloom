@extends('admin.layouts.app')

@section('title', 'Coupon Details - ' . $coupon->code)

@section('content')
    <div class="space-y-6" x-data="{
        copiedText: null,
        copyToClipboard(text, label) {
            navigator.clipboard.writeText(text);
            this.copiedText = label;
            setTimeout(() => this.copiedText = null, 2000);
        }
    }">
        <!-- Single Unified Card (Sharp / Non-rounded) -->
        <div class="bg-white border border-gray-200 shadow-sm">
            
            <!-- Card Header -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-1.5">
                            <a href="{{ route('admin.coupons.index') }}" class="hover:text-gray-900 transition">Coupons</a>
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-700 font-semibold font-mono">{{ $coupon->code }}</span>
                        </nav>
                        
                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight font-mono">{{ $coupon->code }}</h1>
                            <button type="button" @click="copyToClipboard('{{ $coupon->code }}', '{{ $coupon->id }}')" 
                                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-2 py-1 border border-gray-300 transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <span x-text="copiedText ? 'Copied!' : 'Copy Code'"></span>
                            </button>

                            @if ($coupon->status == 'active')
                                <span class="inline-flex px-2.5 py-0.5 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex px-2.5 py-0.5 text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    {{ ucfirst($coupon->status) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Header Actions -->
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.coupons.edit', $coupon->id) }}"
                           class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 hover:bg-amber-100 shadow-sm transition">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Coupon
                        </a>
                        <a href="{{ route('admin.coupons.index') }}"
                           class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-sm transition">
                            <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to List
                        </a>
                    </div>
                </div>
            </div>

            <!-- Coupon Metric Panels -->
            <div class="p-5 sm:p-6">
                
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                    <div class="p-4 bg-gray-50 border border-gray-200 text-center">
                        <p class="text-[11px] font-semibold uppercase text-gray-500 tracking-wider">Discount Rate</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">
                            @if ($coupon->type == 'percentage')
                                {{ $coupon->value }}%
                            @else
                                {{ $currency_sign ?? '$' }}{{ number_format($coupon->value, 2) }}
                            @endif
                        </p>
                    </div>

                    <div class="p-4 bg-gray-50 border border-gray-200 text-center">
                        <p class="text-[11px] font-semibold uppercase text-gray-500 tracking-wider">Minimum Spend</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">
                            {{ $currency_sign ?? '$' }}{{ number_format($coupon->min_order_amount, 2) }}
                        </p>
                    </div>

                    <div class="p-4 bg-gray-50 border border-gray-200 text-center">
                        <p class="text-[11px] font-semibold uppercase text-gray-500 tracking-wider">Times Used</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">{{ $coupon->used_count }}</p>
                    </div>

                    <div class="p-4 bg-gray-50 border border-gray-200 text-center">
                        <p class="text-[11px] font-semibold uppercase text-gray-500 tracking-wider">Usage Limit</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">
                            {{ $coupon->usage_limit ?: 'Unlimited' }}
                        </p>
                    </div>
                </div>

                <!-- Detailed Specifications -->
                <div class="border border-gray-200">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">Coupon Specifications</h3>
                    </div>
                    
                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">Coupon ID</span>
                            <span class="font-mono font-semibold text-gray-900">#{{ $coupon->id }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">Discount Type</span>
                            <span class="font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $coupon->type)) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">Valid From</span>
                            <span class="font-semibold text-gray-900">{{ $coupon->start_date ? $coupon->start_date->format('M d, Y · H:i') : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">Valid Until</span>
                            <span class="font-semibold text-gray-900">{{ $coupon->end_date ? $coupon->end_date->format('M d, Y · H:i') : 'N/A' }}</span>
                        </div>
                        @if ($coupon->max_discount_amount)
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-500">Max Discount Cap</span>
                                <span class="font-semibold text-gray-900">{{ $currency_sign ?? '$' }}{{ number_format($coupon->max_discount_amount, 2) }}</span>
                            </div>
                        @endif
                        @if ($coupon->description)
                            <div class="md:col-span-2 py-2 border-b border-gray-100">
                                <span class="text-gray-500 block mb-1">Description / Notes</span>
                                <p class="text-gray-800 bg-gray-50 p-2.5 border border-gray-200">{{ $coupon->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Metadata Footer -->
            <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-gray-500">
                <div class="space-x-3">
                    <span>Created: {{ $coupon->created_at ? $coupon->created_at->format('M d, Y · H:i') : 'N/A' }}</span>
                    <span>&bull;</span>
                    <span>Last Updated: {{ $coupon->updated_at ? $coupon->updated_at->format('M d, Y · H:i') : 'N/A' }}</span>
                </div>

                <a href="{{ route('admin.coupons.index') }}"
                   class="px-4 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 transition inline-block text-center">
                    Back to All Coupons
                </a>
            </div>

        </div>
    </div>
@endsection
