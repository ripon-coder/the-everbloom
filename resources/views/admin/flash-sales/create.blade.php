@extends('admin.layouts.app')

@section('title', 'Create Flash Sale')

@section('content')
    <div class="p-4 dark:bg-gray-900">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Flash Sale</h1>
            <a href="{{ route('admin.flash-sales.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Back
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.flash-sales.store') }}" method="POST" id="flashSaleForm"
            class="space-y-6">
            @csrf

            <!-- Basic Flash Sale Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" 
                               placeholder="Enter flash sale name"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Slug
                        </label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug') }}" 
                               placeholder="Leave empty to auto-generate"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('slug') border-red-500 @enderror">
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave empty to generate from name, or enter your own.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" id="start_date" name="start_date" 
                               value="{{ old('start_date') }}" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('start_date') border-red-500 @enderror">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            End Date <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" id="end_date" name="end_date" 
                               value="{{ old('end_date') }}" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('end_date') border-red-500 @enderror">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('status') border-red-500 @enderror">
                            <option value="">Select Status</option>
                            @foreach ($status_options as $value => $label)
                                <option value="{{ $value }}" {{ old('status') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="banner_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Banner Image URL
                        </label>
                        <input type="text" id="banner_image" name="banner_image" 
                               value="{{ old('banner_image') }}"
                               placeholder="https://example.com/image.jpg"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('banner_image') border-red-500 @enderror">
                        @error('banner_image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter the full URL to the banner image.</p>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('description') border-red-500 @enderror"
                        placeholder="Enter flash sale description...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Product Selection Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Products</h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="products" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Select Products <span class="text-red-500">*</span>
                        </label>
                        <select id="products" name="products[]" multiple 
                                class="w-full h-32 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('products') border-red-500 @enderror">
                            @foreach($products ?? [] as $product)
                                <option value="{{ $product->id }}" 
                                        {{ in_array($product->id, old('products', [])) ? 'selected' : '' }}>
                                    {{ $product->name }} [{{$product->id}}] - {{ $currency_sign }}{{ number_format($product->price, 2) }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Hold Ctrl/Cmd to select multiple products</p>
                        @error('products')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Optional: Discount settings per product -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="discount_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Default Discount Price (optional)
                            </label>
                            <input type="number" id="discount_price" name="discount_price" step="0.01" 
                                   value="{{ old('discount_price') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('discount_price') border-red-500 @enderror"
                                   placeholder="Leave empty to use original price">
                            @error('discount_price')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="discount_percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Default Discount Percentage (optional)
                            </label>
                            <input type="number" id="discount_percentage" name="discount_percentage" min="0" max="100" 
                                   value="{{ old('discount_percentage') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('discount_percentage') border-red-500 @enderror"
                                   placeholder="e.g., 20 for 20% off">
                            @error('discount_percentage')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.flash-sales.index') }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                    Cancel
                </a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Create Flash Sale
                </button>
            </div>
        </form>
    </div>
@endsection
