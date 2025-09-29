@extends('admin.layouts.app')

@section('title', 'Edit Coupon')

@section('content')
    <div class="p-4 dark:bg-gray-900">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Coupon</h1>
            <a href="{{ route('admin.coupons.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Back
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST" id="couponForm"
            class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Coupon Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Coupon Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="code" name="code" value="{{ old('code', $coupon->code) }}" readonly
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Coupon code cannot be changed after creation.</p>
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Coupon Type <span class="text-red-500">*</span>
                        </label>
                        <select id="type" name="type" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            onchange="toggleMaxDiscount()">
                            <option value="">Select Type</option>
                            @foreach ($type_options as $value => $label)
                                <option value="{{ $value }}" {{ old('type', $coupon->type) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="value" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Discount Value <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="value" name="value" value="{{ old('value', $coupon->value) }}" 
                               step="0.01" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label for="min_order_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Minimum Order Amount <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="min_order_amount" name="min_order_amount" 
                               value="{{ old('min_order_amount', $coupon->min_order_amount) }}" step="0.01" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div id="max_discount_group" style="display: {{ old('type', $coupon->type) == 'percentage' ? 'block' : 'none' }};">
                        <label for="max_discount_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Maximum Discount Amount
                        </label>
                        <input type="number" id="max_discount_amount" name="max_discount_amount" 
                               value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}" step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Only applicable for percentage discounts.</p>
                    </div>

                    <div>
                        <label for="usage_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Usage Limit
                        </label>
                        <input type="number" id="usage_limit" name="usage_limit" 
                               value="{{ old('usage_limit', $coupon->usage_limit) }}" min="1"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave empty for unlimited usage.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" id="start_date" name="start_date" 
                               value="{{ old('start_date', $coupon->start_date->format('Y-m-d\TH:i')) }}" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            End Date <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" id="end_date" name="end_date" 
                               value="{{ old('end_date', $coupon->end_date->format('Y-m-d\TH:i')) }}" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">Select Status</option>
                            @foreach ($status_options as $value => $label)
                                <option value="{{ $value }}" {{ old('status', $coupon->status) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Usage Statistics
                        </label>
                        <div class="px-3 py-2 bg-gray-50 dark:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white">
                            @if ($coupon->usage_limit)
                                {{ $coupon->used_count }} / {{ $coupon->usage_limit }} used
                            @else
                                {{ $coupon->used_count }} / ∞ used
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ old('description', $coupon->description) }}</textarea>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.coupons.index') }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                    Cancel
                </a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Coupon
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
function toggleMaxDiscount() {
    var type = document.getElementById('type').value;
    var maxDiscountGroup = document.getElementById('max_discount_group');
    
    if (type === 'percentage') {
        maxDiscountGroup.style.display = 'block';
    } else {
        maxDiscountGroup.style.display = 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleMaxDiscount();
});
</script>
@endpush
