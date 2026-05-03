@extends('admin.layouts.app')

@section('title', 'Edit Variant')

@section('content')
    <div class="p-6">
        <div class="flex items-center space-x-4 mb-6">
            <a href="{{ route('admin.variants.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Edit Variant</h1>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center">
                    @if($variant->product && $variant->product->firstImage)
                        <img class="h-16 w-16 rounded-lg object-cover mr-4 border border-gray-200" 
                             src="{{ $variant->product->firstImage->getImageUrl() }}" alt="">
                    @endif
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">{{ $variant->product->name ?? 'Product' }}</h2>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @foreach($variant->variantAttributes as $attr)
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $attr->attribute->name }}: {{ $attr->attributeValue->value }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.variants.update', $variant->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- SKU -->
                    <div class="col-span-1">
                        <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                        <input type="text" name="sku" id="sku" value="{{ old('sku', $variant->sku) }}" required
                            class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        @error('sku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Stock -->
                    <div class="col-span-1">
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', $variant->stock) }}" required min="0"
                            class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Selling Price -->
                    <div class="col-span-1">
                        <label for="sell_price" class="block text-sm font-medium text-gray-700 mb-1">Selling Price</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">{{ $currency_sign }}</span>
                            <input type="number" name="sell_price" id="sell_price" value="{{ old('sell_price', $variant->sell_price) }}" required step="0.01" min="0"
                                class="w-full pl-8 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        @error('sell_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Buying Price -->
                    <div class="col-span-1">
                        <label for="buying_price" class="block text-sm font-medium text-gray-700 mb-1">Buying Price</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">{{ $currency_sign }}</span>
                            <input type="number" name="buying_price" id="buying_price" value="{{ old('buying_price', $variant->buying_price) }}" step="0.01" min="0"
                                class="w-full pl-8 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        @error('buying_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Discount Price -->
                    <div class="col-span-1">
                        <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-1">Discount Price (Optional)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">{{ $currency_sign }}</span>
                            <input type="number" name="discount_price" id="discount_price" value="{{ old('discount_price', $variant->discount_price) }}" step="0.01" min="0"
                                class="w-full pl-8 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        @error('discount_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Weight -->
                    <div class="col-span-1">
                        <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                        <input type="number" name="weight" id="weight" value="{{ old('weight', $variant->weight) }}" step="0.01" min="0"
                            class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        @error('weight') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-span-1">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" required
                            class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="active" {{ old('status', $variant->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $variant->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('admin.variants.index') }}"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-6 rounded-lg transition duration-200">
                        Cancel
                    </a>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-10 rounded-lg transition duration-200 shadow-md">
                        Update Variant
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
