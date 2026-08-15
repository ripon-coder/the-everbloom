@extends('admin.layouts.app')

@section('title', 'Create District')

@section('content')
    <div class="space-y-6">
        <!-- Single Unified Card (Sharp / Non-rounded) -->
        <div class="bg-white border border-gray-200 shadow-sm">
            
            <!-- Card Header -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-1.5">
                            <a href="{{ route('admin.district.index') }}" class="hover:text-gray-900 transition">Delivery Districts</a>
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-700 font-semibold">New District</span>
                        </nav>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Create Delivery District</h1>
                    </div>

                    <!-- Header Action -->
                    <a href="{{ route('admin.district.index') }}"
                       class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-sm transition">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Districts
                    </a>
                </div>
            </div>

            <!-- Form Body -->
            <form action="{{ route('admin.district.store') }}" method="POST">
                @csrf

                <div class="p-5 sm:p-6 space-y-6">
                    
                    <!-- Details Section -->
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 pb-2 mb-4 border-b border-gray-200">
                            District & Shipping Configuration
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- District Name -->
                            <div>
                                <label for="name" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    District Name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" required
                                       value="{{ old('name') }}" placeholder="e.g., Dhaka, Chittagong, Sylhet"
                                       class="w-full px-3 py-2 text-xs border @error('name') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                @error('name')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Delivery Charge -->
                            <div>
                                <label for="delivery_charge" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Base Delivery Charge ({{ $currency_sign ?? '$' }}) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500 font-bold text-xs">
                                        {{ $currency_sign ?? '$' }}
                                    </div>
                                    <input type="number" step="0.01" name="delivery_charge" id="delivery_charge" required
                                           value="{{ old('delivery_charge', '0') }}" placeholder="0.00" min="0"
                                           class="w-full pl-7 pr-3 py-2 text-xs font-mono border @error('delivery_charge') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                </div>
                                @error('delivery_charge')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Special Information / Note -->
                        <div class="mt-5">
                            <label for="information" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Special Delivery Notice / Information
                            </label>
                            <textarea name="information" id="information" rows="3"
                                      placeholder="Optional notes regarding courier transit times or coverage area..."
                                      class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">{{ old('information') }}</textarea>
                            @error('information')
                                <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                </div>

                <!-- Form Footer Actions -->
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.district.index') }}"
                       class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                        Create District
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection
