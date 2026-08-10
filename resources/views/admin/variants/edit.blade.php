@extends('admin.layouts.app')

@section('title', 'Edit Variant - ' . $variant->sku)

@section('content')
    <div class="p-4 sm:p-6" x-data="variantEditManager()">
        <!-- Full Width Breadcrumb & Navigation -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-2">
                    <a href="{{ route('admin.variants.index') }}" class="hover:text-blue-600 font-medium transition">
                        Variant Products List
                    </a>
                    <span>/</span>
                    <a href="{{ route('admin.variants.show', $variant->product_id) }}" class="hover:text-blue-600 font-medium transition truncate max-w-[150px]">
                        {{ $variant->product->name ?? 'Product' }}
                    </a>
                    <span>/</span>
                    <span class="text-gray-700 font-semibold">Edit Variant</span>
                </nav>
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Variant</h1>
                    <span class="font-mono text-xs font-bold bg-amber-50 text-amber-700 px-2.5 py-1 border border-amber-200/80">
                        {{ $variant->sku }}
                    </span>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.variants.show', $variant->product_id) }}"
                    class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-medium py-2.5 px-4 shadow-sm transition duration-200 flex items-center text-xs">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Product
                </a>
            </div>
        </div>

        <!-- Full Width Product Reference Card -->
        <div class="bg-white shadow-sm border border-gray-200 p-5 mb-6">
            <div class="flex items-center space-x-4">
                @php
                    $prodImg = $variant->product?->firstImage?->getImageUrl() ?? $variant->product?->anyImage?->getImageUrl();
                @endphp
                @if($prodImg)
                    <img src="{{ $prodImg }}" alt="" class="w-16 h-16 object-cover border border-gray-200 shadow-sm flex-shrink-0">
                @else
                    <div class="w-16 h-16 bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 flex-shrink-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif

                <div class="space-y-1">
                    <h2 class="text-base font-bold text-gray-900">{{ $variant->product->name ?? 'Product' }}</h2>
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="bg-blue-50 text-blue-700 font-semibold px-2.5 py-0.5 border border-blue-100">
                            Category: {{ $variant->product->category->name ?? 'N/A' }}
                        </span>
                        <span class="bg-gray-100 text-gray-700 font-semibold px-2.5 py-0.5 border border-gray-200">
                            Brand: {{ $variant->product->brand->name ?? 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- FULL WIDTH EDIT FORM CARD -->
        <div class="bg-white shadow-sm border border-gray-200 overflow-hidden w-full">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="w-8 h-8 bg-amber-100 text-amber-600 flex items-center justify-center text-sm font-bold">✏️</span>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Update Variant Information</h2>
                        <p class="text-xs text-gray-500">Modify stock quantity, prices, variant attributes, and image.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.variants.update', $variant->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Primary Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- SKU -->
                    <div>
                        <label for="sku" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            SKU <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="sku" id="sku" value="{{ old('sku', $variant->sku) }}" required
                            class="w-full px-3.5 py-2.5 border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition">
                        @error('sku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Stock -->
                    <div>
                        <label for="stock" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Stock Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', $variant->stock) }}" required min="0" step="1"
                            class="w-full px-3.5 py-2.5 border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition">
                        @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Selling Price -->
                    <div>
                        <label for="sell_price" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Selling Price ({{ $currency_sign }}) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="sell_price" id="sell_price" value="{{ old('sell_price', $variant->sell_price) }}" required step="0.01" min="0"
                            class="w-full px-3.5 py-2.5 border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition">
                        @error('sell_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Buying Price -->
                    <div>
                        <label for="buying_price" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Buying Price ({{ $currency_sign }})
                        </label>
                        <input type="number" name="buying_price" id="buying_price" value="{{ old('buying_price', $variant->buying_price) }}" step="0.01" min="0"
                            class="w-full px-3.5 py-2.5 border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition">
                        @error('buying_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Discount Price -->
                    <div>
                        <label for="discount_price" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Discount Price ({{ $currency_sign }})
                        </label>
                        <input type="number" name="discount_price" id="discount_price" value="{{ old('discount_price', $variant->discount_price) }}" step="0.01" min="0"
                            class="w-full px-3.5 py-2.5 border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition">
                        @error('discount_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Weight -->
                    <div>
                        <label for="weight" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Weight (kg)
                        </label>
                        <input type="number" name="weight" id="weight" value="{{ old('weight', $variant->weight) }}" step="0.01" min="0"
                            class="w-full px-3.5 py-2.5 border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition">
                        @error('weight') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Status -->
                    <div class="md:col-span-3">
                        <label for="status" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                            class="w-full px-3.5 py-2.5 border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm transition">
                            <option value="active" {{ old('status', $variant->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $variant->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                        <button type="button" @click="addAttribute()" class="text-xs font-semibold text-amber-600 hover:text-amber-800 flex items-center bg-amber-50 px-2.5 py-1 border border-amber-100 transition">
                            + Add Attribute
                        </button>
                    </div>

                    <template x-for="(item, index) in attributesList" :key="index">
                        <div class="flex items-center gap-3 mb-3 bg-gray-50 p-2.5 border border-gray-200">
                            <div class="flex-1">
                                <select :name="'variant_attributes[' + index + '][attribute_id]'"
                                    x-model="item.attribute_id"
                                    @change="item.attribute_value_id = ''"
                                    class="w-full border-gray-300 text-xs focus:ring-amber-500 focus:border-amber-500">
                                    <option value="">Select Attribute</option>
                                    @foreach($attributes as $attr)
                                        <option value="{{ $attr->id }}"
                                                :disabled="isAttributeSelected('{{ $attr->id }}', index)">
                                            {{ $attr->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1">
                                <select :name="'variant_attributes[' + index + '][attribute_value_id]'"
                                    x-model="item.attribute_value_id"
                                    class="w-full border-gray-300 text-xs focus:ring-amber-500 focus:border-amber-500"
                                    :disabled="!item.attribute_id">
                                    <option value="">Select Value</option>
                                    <template x-for="val in getValuesForAttribute(item.attribute_id)" :key="val.id">
                                        <option :value="val.id" x-text="val.value" :selected="val.id == item.attribute_value_id"></option>
                                    </template>
                                </select>
                            </div>
                            <button type="button" @click="removeAttribute(index)" x-show="attributesList.length > 1" class="p-1.5 text-red-500 hover:bg-red-50 transition">
                                ✕
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Image Upload Section -->
                <div class="border-t border-gray-100 pt-5">
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">
                        Variant Image <span class="text-gray-400 font-normal lowercase">(Optional - upload to change image)</span>
                    </label>

                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-3">
                        @if($variant->images && $variant->images->count() > 0)
                            <div class="flex items-center space-x-3 bg-gray-50 p-2.5 border border-gray-200">
                                <span class="text-[11px] font-semibold text-gray-500">Current Image:</span>
                                <div class="w-14 h-14 overflow-hidden border border-gray-200 bg-white">
                                    <img src="{{ $variant->images->first()->getImageUrl() }}" class="w-full h-full object-cover">
                                </div>
                            </div>
                        @endif

                        <!-- Instant New Image Preview -->
                        <div x-show="imagePreview" x-cloak class="flex items-center space-x-3 bg-amber-50 p-2.5 border border-amber-200">
                            <span class="text-[11px] font-semibold text-amber-700">New Image Preview:</span>
                            <div class="w-14 h-14 overflow-hidden border border-amber-300 bg-white">
                                <img :src="imagePreview" class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>

                    <input type="file" name="image" accept="image/*"
                        @change="handleImagePreview($event)"
                        class="w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-none file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 transition cursor-pointer">
                </div>

                <!-- Form Action Buttons -->
                <div class="mt-8 flex justify-end space-x-3 border-t border-gray-100 pt-5">
                    <a href="{{ route('admin.variants.show', $variant->product_id) }}"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-6 transition text-xs">
                        Cancel
                    </a>
                    <button type="submit"
                        class="bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2.5 px-8 transition shadow-lg shadow-amber-500/20 text-xs">
                        Update Variant
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function variantEditManager() {
        return {
            attributesList: @json($variant->variantAttributes->count() > 0 ? $variant->variantAttributes->map(fn($attr) => ['attribute_id' => (string)$attr->attribute_id, 'attribute_value_id' => (string)$attr->attribute_value_id]) : [['attribute_id' => '', 'attribute_value_id' => '']]),
            availableAttributes: @json($attributes),
            imagePreview: null,
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
