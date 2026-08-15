@extends('admin.layouts.app')

@section('title', 'Edit Coupon')

@section('content')
    <div class="space-y-6" x-data="{
        type: '{{ old('type', $coupon->type) }}'
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
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-500">Edit</span>
                        </nav>
                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Coupon</h1>
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                #{{ $coupon->id }}
                            </span>
                        </div>
                    </div>

                    <!-- Header Action -->
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.coupons.show', $coupon->id) }}"
                           class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 shadow-sm transition">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Details
                        </a>
                        <a href="{{ route('admin.coupons.index') }}"
                           class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-sm transition">
                            <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Coupons
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form Body -->
            <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-5 sm:p-6 space-y-6">
                    
                    <!-- Section 1: Coupon Code & Type -->
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 pb-2 mb-4 border-b border-gray-200">
                            Coupon Details
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Coupon Code (Readonly) -->
                            <div>
                                <label for="code" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Coupon Code <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="code" id="code" value="{{ old('code', $coupon->code) }}" readonly
                                       class="w-full px-3 py-2 text-xs font-mono uppercase bg-gray-100 border border-gray-300 text-gray-600 cursor-not-allowed">
                                <p class="mt-1 text-[11px] text-gray-500">Coupon code cannot be changed once created.</p>
                            </div>

                            <!-- Coupon Type -->
                            <div>
                                <label for="type" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Discount Type <span class="text-rose-500">*</span>
                                </label>
                                <select name="type" id="type" x-model="type" required
                                        class="w-full py-2 px-3 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600 cursor-pointer">
                                    <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>Percentage Discount (%)</option>
                                    <option value="fixed_amount" {{ old('type', $coupon->type) == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount Discount</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Values & Limitations -->
                    <div class="pt-4 border-t border-gray-200">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 pb-2 mb-4 border-b border-gray-200">
                            Discount & Limitations
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                            <!-- Value -->
                            <div>
                                <label for="value" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    <span x-text="type === 'percentage' ? 'Discount Percentage (%) *' : 'Discount Amount *'"></span>
                                </label>
                                <input type="number" step="0.01" min="0" name="value" id="value" required
                                       value="{{ old('value', $coupon->value) }}"
                                       class="w-full px-3 py-2 text-xs border @error('value') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                @error('value')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Min Order Amount -->
                            <div>
                                <label for="min_order_amount" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Min Spend / Order <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" name="min_order_amount" id="min_order_amount" required
                                       value="{{ old('min_order_amount', $coupon->min_order_amount) }}"
                                       class="w-full px-3 py-2 text-xs border @error('min_order_amount') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                @error('min_order_amount')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Max Discount Amount (Percentage Only) -->
                            <div x-show="type === 'percentage'">
                                <label for="max_discount_amount" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Max Discount Cap
                                </label>
                                <input type="number" step="0.01" min="0" name="max_discount_amount" id="max_discount_amount"
                                       value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}" placeholder="Optional cap"
                                       class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            </div>

                            <!-- Usage Limit -->
                            <div>
                                <label for="usage_limit" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Total Usage Limit
                                </label>
                                <input type="number" min="1" name="usage_limit" id="usage_limit"
                                       value="{{ old('usage_limit', $coupon->usage_limit) }}" placeholder="Leave blank for unlimited"
                                       class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <p class="mt-1 text-[11px] text-gray-500">Currently used: {{ $coupon->used_count }} times</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Schedule & Status -->
                    <div class="pt-4 border-t border-gray-200">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 pb-2 mb-4 border-b border-gray-200">
                            Schedule & Status
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <!-- Start Date -->
                            <div>
                                <label for="start_date" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Start Date & Time <span class="text-rose-500">*</span>
                                </label>
                                <input type="datetime-local" name="start_date" id="start_date" required
                                       value="{{ old('start_date', $coupon->start_date ? $coupon->start_date->format('Y-m-d\TH:i') : '') }}"
                                       class="w-full px-3 py-2 text-xs border @error('start_date') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                @error('start_date')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- End Date -->
                            <div>
                                <label for="end_date" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    End Date & Time <span class="text-rose-500">*</span>
                                </label>
                                <input type="datetime-local" name="end_date" id="end_date" required
                                       value="{{ old('end_date', $coupon->end_date ? $coupon->end_date->format('Y-m-d\TH:i') : '') }}"
                                       class="w-full px-3 py-2 text-xs border @error('end_date') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                @error('end_date')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Status <span class="text-rose-500">*</span>
                                </label>
                                <select name="status" id="status" required
                                        class="w-full py-2 px-3 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600 cursor-pointer">
                                    <option value="active" {{ old('status', $coupon->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $coupon->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Description -->
                    <div class="pt-4 border-t border-gray-200">
                        <label for="description" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Description / Internal Notes
                        </label>
                        <textarea name="description" id="description" rows="3"
                                  placeholder="Terms, promotional campaign notes, or coupon conditions..."
                                  class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">{{ old('description', $coupon->description ?? '') }}</textarea>
                    </div>

                </div>

                <!-- Form Footer with Meta & Actions (No Delete Button) -->
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="text-gray-500 space-x-3">
                        <span>Created: {{ $coupon->created_at ? $coupon->created_at->format('M d, Y · H:i') : 'N/A' }}</span>
                        <span>&bull;</span>
                        <span>Updated: {{ $coupon->updated_at ? $coupon->updated_at->format('M d, Y · H:i') : 'N/A' }}</span>
                    </div>

                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.coupons.index') }}"
                           class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 transition">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                            Update Coupon
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection
