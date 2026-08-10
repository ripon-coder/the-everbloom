@extends('admin.layouts.app')

@section('title', 'Variant Management - ' . $product->name)

@section('content')
    <div class="p-4 sm:p-6" x-data="variantPageManager()">
        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-2">
                    <a href="{{ route('admin.variants.index') }}" class="hover:text-blue-600 flex items-center font-medium transition">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Variant Products List
                    </a>
                    <span>/</span>
                    <span class="text-gray-700 font-semibold truncate max-w-[200px]">{{ $product->name }}</span>
                </nav>
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $product->name }}</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold {{ $product->status === 'active' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                        <span class="w-1.5 h-1.5 {{ $product->status === 'active' ? 'bg-green-500' : 'bg-red-500' }} mr-1.5"></span>
                        {{ ucfirst($product->status) }}
                    </span>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.variants.index') }}"
                    class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-medium py-2.5 px-4 shadow-sm transition duration-200 flex items-center text-xs">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>

        <!-- Product Overview Header Card -->
        <div class="bg-white shadow-sm border border-gray-200 p-5 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="flex items-start space-x-4">
                    @php
                        $mainImg = $product->firstImage?->getImageUrl() ?? $product->anyImage?->getImageUrl();
                    @endphp
                    @if($mainImg)
                        <img src="{{ $mainImg }}" alt="{{ $product->name }}" class="w-20 h-20 object-cover border border-gray-200 shadow-sm flex-shrink-0">
                    @else
                        <div class="w-20 h-20 bg-gray-100 border border-gray-200 flex items-center justify-center flex-shrink-0 text-gray-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif

                    <div class="space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="bg-blue-50 text-blue-700 font-semibold px-2.5 py-0.5 border border-blue-100">
                                Category: {{ $product->category->name ?? 'N/A' }}
                            </span>
                            <span class="bg-gray-100 text-gray-700 font-semibold px-2.5 py-0.5 border border-gray-200">
                                Brand: {{ $product->brand->name ?? 'N/A' }}
                            </span>
                            @if($product->is_free_delivery)
                                <span class="bg-emerald-50 text-emerald-700 font-semibold px-2.5 py-0.5 border border-emerald-100">
                                    🚚 Free Delivery
                                </span>
                            @endif
                            @if($product->is_featured)
                                <span class="bg-amber-50 text-amber-700 font-semibold px-2.5 py-0.5 border border-amber-100">
                                    ⭐ Featured
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 font-mono">Slug: {{ $product->slug }}</p>
                        @if($product->short_description)
                            <p class="text-xs text-gray-600 line-clamp-2 max-w-2xl">{{ $product->short_description }}</p>
                        @endif
                    </div>
                </div>

                <!-- Quick Stats Badge Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-50/80 p-3 border border-gray-200 flex-shrink-0">
                    <div class="text-center px-3 py-1">
                        <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Total Variants</p>
                        <p class="text-lg font-bold text-gray-900 mt-0.5">{{ $product->variants->count() }}</p>
                    </div>
                    <div class="text-center px-3 py-1 border-l border-gray-200">
                        <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Active Variants</p>
                        <p class="text-lg font-bold text-emerald-600 mt-0.5">{{ $product->variants->where('status', 'active')->count() }}</p>
                    </div>
                    <div class="text-center px-3 py-1 border-l border-gray-200">
                        <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Total Stock</p>
                        <p class="text-lg font-bold text-blue-600 mt-0.5">{{ $product->variants->sum('stock') }}</p>
                    </div>
                    <div class="text-center px-3 py-1 border-l border-gray-200">
                        <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Price Range</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">
                            @if($product->variants->count() > 0)
                                {{ $currency_sign }}{{ number_format($product->variants->min('sell_price'), 2) }}
                                @if($product->variants->min('sell_price') != $product->variants->max('sell_price'))
                                    - {{ $currency_sign }}{{ number_format($product->variants->max('sell_price'), 2) }}
                                @endif
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- INLINE ADD VARIANT CARD FORM -->
        <div x-show="showAddForm" x-cloak x-transition class="bg-white shadow-sm border border-gray-200 mb-6 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                <div class="flex items-center space-x-2">
                    <span class="w-8 h-8 bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold">✨</span>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Add New Variant</h2>
                        <p class="text-xs text-gray-500">Create a new variant option with attributes, pricing, and stock for this product.</p>
                    </div>
                </div>
                <button type="button" @click="showAddForm = false" class="text-gray-400 hover:text-gray-600 transition p-1.5 hover:bg-gray-200/50" title="Close Form">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <form action="{{ route('admin.variants.store-variant', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <!-- SKU -->
                        <div>
                            <label for="sku" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                SKU <span class="text-gray-400 font-normal lowercase">(optional - auto generated if empty)</span>
                            </label>
                            <input type="text" name="sku" id="sku" value="{{ old('sku') }}" placeholder="e.g. EVB-SHIRT-RED-XL"
                                class="w-full px-3.5 py-2 border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition">
                            @error('sku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Stock -->
                        <div>
                            <label for="stock" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Stock Quantity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="stock" id="stock" value="{{ old('stock', 10) }}" min="0" step="1" required
                                class="w-full px-3.5 py-2 border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition">
                            @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Selling Price -->
                        <div>
                            <label for="sell_price" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Selling Price ({{ $currency_sign }}) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="sell_price" id="sell_price" value="{{ old('sell_price') }}" step="0.01" min="0" placeholder="0.00" required
                                class="w-full px-3.5 py-2 border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition">
                            @error('sell_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Buying Price -->
                        <div>
                            <label for="buying_price" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Buying Price ({{ $currency_sign }})
                            </label>
                            <input type="number" name="buying_price" id="buying_price" value="{{ old('buying_price', 0) }}" step="0.01" min="0" placeholder="0.00"
                                class="w-full px-3.5 py-2 border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition">
                            @error('buying_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Discount Price -->
                        <div>
                            <label for="discount_price" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Discount Price ({{ $currency_sign }}) <span class="text-gray-400 font-normal lowercase">(optional)</span>
                            </label>
                            <input type="number" name="discount_price" id="discount_price" value="{{ old('discount_price') }}" step="0.01" min="0" placeholder="0.00"
                                class="w-full px-3.5 py-2 border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition">
                            @error('discount_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Weight -->
                        <div>
                            <label for="weight" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Weight (kg)
                            </label>
                            <input type="number" name="weight" id="weight" value="{{ old('weight', 0) }}" step="0.01" min="0" placeholder="0.00"
                                class="w-full px-3.5 py-2 border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition">
                            @error('weight') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                class="w-full px-3.5 py-2 border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition">
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Dynamic Variant Attributes Selection -->
                    <div class="border-t border-gray-100 pt-5">
                        <div class="flex justify-between items-center mb-3">
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Variant Attributes <span class="text-gray-400 font-normal lowercase">(e.g. Color: Red, Size: XL)</span>
                            </label>
                            <button type="button" @click="addAttribute()" class="text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center bg-blue-50 px-2.5 py-1 border border-blue-100 transition">
                                + Add Attribute
                            </button>
                        </div>

                        <template x-for="(item, index) in attributesList" :key="index">
                            <div class="flex items-center gap-3 mb-3 bg-gray-50 p-2.5 border border-gray-200">
                                <div class="flex-1">
                                    <select :name="'variant_attributes[' + index + '][attribute_id]'"
                                        x-model="item.attribute_id"
                                        @change="item.attribute_value_id = ''"
                                        class="w-full border-gray-300 text-xs focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Attribute</option>
                                        <template x-for="attr in availableAttributes" :key="attr.id">
                                            <option :value="attr.id"
                                                    x-text="isAttributeSelected(attr.id, index) ? attr.name + ' (Already selected)' : attr.name"
                                                    :disabled="isAttributeSelected(attr.id, index)">
                                            </option>
                                        </template>
                                    </select>
                                </div>
                                <div class="flex-1">
                                    <select :name="'variant_attributes[' + index + '][attribute_value_id]'"
                                        x-model="item.attribute_value_id"
                                        class="w-full border-gray-300 text-xs focus:ring-blue-500 focus:border-blue-500"
                                        :disabled="!item.attribute_id">
                                        <option value="">Select Value</option>
                                        <template x-for="val in getValuesForAttribute(item.attribute_id)" :key="val.id">
                                            <option :value="val.id" x-text="val.value"></option>
                                        </template>
                                    </select>
                                </div>
                                <button type="button" @click="removeAttribute(index)" x-show="attributesList.length > 1" class="p-1.5 text-red-500 hover:bg-red-50 transition">
                                    ✕
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Image Upload -->
                    <div class="border-t border-gray-100 pt-5">
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">
                            Variant Image <span class="text-gray-400 font-normal lowercase">(Optional)</span>
                        </label>
                        <input type="file" name="image" accept="image/*"
                            @change="handleImagePreview($event)"
                            class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-none file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition cursor-pointer">
                        
                        <!-- Instant Image Preview -->
                        <div x-show="imagePreview" x-cloak class="mt-3">
                            <p class="text-[11px] font-semibold text-gray-600 mb-2 flex items-center">
                                <span class="w-2 h-2 bg-blue-500 mr-1.5 inline-block"></span>
                                Selected Image Preview:
                            </p>
                            <div class="w-20 h-20 overflow-hidden border border-gray-200 shadow-sm bg-gray-50">
                                <img :src="imagePreview" class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="showAddForm = false"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-5 transition text-xs">
                            Cancel
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-6 transition shadow-lg shadow-blue-500/20 text-xs">
                            Save Variant
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Variants List Section -->
        <div class="bg-white shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50">
                <div>
                    <h2 class="text-base font-bold text-gray-900 flex items-center">
                        <span class="w-6 h-6 bg-purple-100 text-purple-600 flex items-center justify-center text-xs mr-2 font-bold">📦</span>
                        Configured Variants ({{ $product->variants->count() }})
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Manage stock, status, attributes, and options for this product.</p>
                </div>
                
                <!-- ADD VARIANT BUTTON ON TABLE HEADER TOGGLES INLINE FORM CARD -->
                <button type="button" @click="showAddForm = !showAddForm"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 transition text-xs flex items-center shadow-md shadow-blue-500/10">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Variant
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/70">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                            <th class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attributes</th>
                            <th class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price ({{ $currency_sign }})</th>
                            <th class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3.5 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($product->variants as $variant)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($variant->images && $variant->images->count() > 0)
                                            <img src="{{ $variant->images->first()->getImageUrl() }}" alt="" class="w-9 h-9 object-cover mr-3 border border-gray-200">
                                        @endif
                                        <div>
                                            <span class="text-xs font-normal text-gray-800 bg-gray-50 px-2 py-1 border border-gray-200/80">
                                                {{ $variant->sku }}
                                            </span>
                                            @if($variant->weight > 0)
                                                <span class="block text-[11px] text-gray-400 mt-1 font-normal">{{ number_format($variant->weight, 2) }} kg</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse($variant->variantAttributes as $attr)
                                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-normal bg-blue-50/80 text-blue-700 border border-blue-100">
                                                {{ $attr->attribute->name }}: <span class="ml-1 text-blue-800 font-medium">{{ $attr->attributeValue->value }}</span>
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-400 italic">No attributes defined</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs">
                                    <span class="font-medium text-gray-900">{{ $currency_sign }}{{ number_format($variant->sell_price, 2) }}</span>
                                    @if($variant->discount_price)
                                        <span class="block text-[11px] text-emerald-600 font-normal mt-0.5">
                                            Disc: {{ $currency_sign }}{{ number_format($variant->discount_price, 2) }}
                                        </span>
                                    @endif
                                    @if($variant->buying_price > 0)
                                        <span class="block text-[10px] text-gray-400 font-normal">
                                            Cost: {{ $currency_sign }}{{ number_format($variant->buying_price, 2) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('admin.variants.update-stock', $variant->id) }}" method="POST" class="flex items-center space-x-1.5">
                                        @csrf
                                        <input type="number" name="stock" value="{{ $variant->stock }}" min="0"
                                            class="w-16 px-2 py-1 text-xs border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-normal {{ $variant->stock <= 5 ? 'text-red-600 border-red-300 bg-red-50/50' : 'text-gray-900' }}">
                                        <button type="submit" class="p-1 text-blue-600 hover:bg-blue-50 transition" title="Save Stock">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('admin.variants.update-status', $variant->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1 text-xs font-normal transition-all {{ $variant->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-red-50 text-red-700 border border-red-200 hover:bg-red-100' }}">
                                            <span class="w-1.5 h-1.5 {{ $variant->status === 'active' ? 'bg-emerald-500' : 'bg-red-500' }} mr-1.5"></span>
                                            {{ ucfirst($variant->status) }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium">
                                    <div class="flex justify-end items-center space-x-1.5">
                                        <!-- Edit Variant Page Link -->
                                        <a href="{{ route('admin.variants.edit', $variant->id) }}"
                                           class="p-1.5 text-amber-600 hover:text-amber-700 hover:bg-amber-50 transition"
                                           title="Edit Variant">
                                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.variants.destroy', $variant->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this variant option?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 transition" title="Delete Variant">
                                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="max-w-sm mx-auto space-y-3">
                                        <div class="w-12 h-12 bg-blue-50 text-blue-500 flex items-center justify-center mx-auto text-xl font-bold">
                                            ✨
                                        </div>
                                        <p class="text-sm font-semibold text-gray-700">No variants created yet</p>
                                        <p class="text-xs text-gray-500">Add the first variant option for this product to configure pricing, SKUs, and stock.</p>
                                        <button @click="showAddForm = true"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition shadow-md">
                                            + Add First Variant
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function variantPageManager() {
        return {
            showAddForm: false,
            attributesList: [ { attribute_id: '', attribute_value_id: '' } ],
            availableAttributes: @json($attributes),
            addAttribute() {
                this.attributesList.push({ attribute_id: '', attribute_value_id: '' });
            },
            removeAttribute(index) {
                this.attributesList.splice(index, 1);
            },
            isAttributeSelected(attrId, currentIndex) {
                if (!attrId) return false;
                return this.attributesList.some((item, idx) => idx !== currentIndex && item.attribute_id == attrId);
            },
            imagePreview: null,
            handleImagePreview(event) {
                this.imagePreview = null;
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },
            getValuesForAttribute(attrId) {
                if (!attrId) return [];
                const attr = this.availableAttributes.find(a => a.id == attrId);
                if (!attr) return [];
                return attr.attribute_values || attr.attributeValues || [];
            }
        };
    }
</script>
@endpush
