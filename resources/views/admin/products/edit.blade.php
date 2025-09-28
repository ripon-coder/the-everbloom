@extends('admin.layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="p-4">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Product</h1>
        <a href="{{ route('admin.products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Basic Product Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Product Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $product->name) }}" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                        Slug <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="slug" 
                           name="slug" 
                           value="{{ old('slug', $product->slug) }}" 
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 @error('slug') border-red-500 @enderror">
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Brand <span class="text-red-500">*</span>
                    </label>
                    <select id="brand_id" 
                            name="brand_id" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('brand_id') border-red-500 @enderror">
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('brand_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" 
                            name="category_id" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('category_id') border-red-500 @enderror">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                        Price ($) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="price" 
                           name="price" 
                           value="{{ old('price', $product->price) }}" 
                           step="0.01" 
                           min="0" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" 
                            name="status" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Current Product Images -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Current Product Images</h2>
            
            @if($product->images->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($product->images as $image)
                        <div class="relative group">
                            <img src="{{ asset('storage/' . $image->image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-24 object-cover rounded-lg">
                            @if($image->is_default)
                                <span class="absolute top-2 right-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Default
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No images uploaded for this product.</p>
                </div>
            @endif
        </div>

        <!-- Product Images -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Replace Product Images</h2>
            
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Upload New Images
                </label>
                <div class="flex items-center justify-center w-full">
                    <label for="productImages" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition duration-200">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500">
                                <span class="font-semibold">Click to upload</span> or drag and drop
                            </p>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF, WEBP (MAX. 2MB each)</p>
                        </div>
                        <input id="productImages" name="images[]" type="file" class="hidden" multiple accept="image/*" />
                    </label>
                </div>
                <p class="mt-2 text-sm text-gray-500">Note: Uploading new images will replace all existing images.</p>
                <div id="imagePreview" class="mt-4 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4"></div>
            </div>
        </div>

        <!-- Product Variants -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Product Variants</h2>
                <button type="button" 
                        id="addVariant"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Variant
                </button>
            </div>
            
            <div id="variantsContainer" class="space-y-4">
                @foreach($product->variants as $index => $variant)
                    <div class="variant-item" data-variant="{{ $index }}">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Variant {{ $index + 1 }}</h3>
                            <button type="button" class="text-red-600 hover:text-red-900 transition duration-200 remove-variant">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    SKU <span class="text-red-500">*</span>
                                </label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" name="variants[{{ $index }}][sku]" value="{{ $variant->sku }}" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Price ($)
                                </label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" name="variants[{{ $index }}][price]" value="{{ $variant->price }}" step="0.01" min="0">
                                <p class="mt-1 text-xs text-gray-500">Leave empty to use product price</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Stock <span class="text-red-500">*</span>
                                </label>
                                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" name="variants[{{ $index }}][stock]" value="{{ $variant->stock }}" min="0" required>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Status
                                </label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" name="variants[{{ $index }}][status]">
                                    <option value="active" {{ $variant->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $variant->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Variant Images
                                </label>
                                <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" name="variants[{{ $index }}][images][]" multiple accept="image/*">
                                <p class="mt-1 text-xs text-gray-500">Multiple images allowed</p>
                                
                                <!-- Current variant images -->
                                @if($variant->images->count() > 0)
                                    <div class="mt-2">
                                        <p class="text-xs text-gray-500 mb-2">Current images:</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($variant->images as $variantImage)
                                                <div class="relative">
                                                    <img src="{{ asset('storage/' . $variantImage->image) }}" 
                                                         alt="Variant image" 
                                                         class="w-12 h-12 object-cover rounded-lg">
                                                    @if($variantImage->is_default)
                                                        <span class="absolute -top-1 -right-1 inline-flex items-center px-1 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Default
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-medium text-gray-700">Attributes</label>
                                <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-1 px-3 rounded-md text-sm transition duration-200 add-attribute" data-variant="{{ $index }}">
                                    <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Attribute
                                </button>
                            </div>
                            <div class="attributes-container" data-variant="{{ $index }}">
                                @foreach($variant->variantAttributes as $attrIndex => $variantAttribute)
                                    <div class="attribute-item">
                                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Attribute</label>
                                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 attribute-select" name="variants[{{ $index }}][attributes][{{ $attrIndex }}][attribute_id]" required>
                                                    <option value="">Select Attribute</option>
                                                    @foreach($attributes as $attribute)
                                                        <option value="{{ $attribute->id }}" {{ $variantAttribute->attribute_id == $attribute->id ? 'selected' : '' }}>
                                                            {{ $attribute->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Attribute Value</label>
                                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 attribute-value-select" name="variants[{{ $index }}][attributes][{{ $attrIndex }}][attribute_value_id]" required>
                                                    <option value="">Select Value</option>
                                                    @if($variantAttribute->attribute)
                                                        {{-- @foreach($variantAttribute->attribute->values as $value)
                                                            <option value="{{ $value->id }}" {{ $variantAttribute->attribute_value_id == $value->id ? 'selected' : '' }}>
                                                                {{ $value->value }}
                                                            </option>
                                                        @endforeach --}}
                                                    @endif
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
                                                <button type="button" class="w-full bg-red-100 hover:bg-red-200 text-red-700 font-medium py-2 px-3 rounded-md text-sm transition duration-200 remove-attribute">
                                                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Update Product
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .variant-item {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1.5rem;
    }
    .attribute-item {
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
    }
    .image-preview {
        position: relative;
        display: inline-block;
    }
    .image-preview img {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 0.5rem;
    }
    .image-preview .remove-image {
        position: absolute;
        top: -0.5rem;
        right: -0.5rem;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 50%;
        width: 1.5rem;
        height: 1.5rem;
        font-size: 0.75rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script>
    // Global variables
    let variantCount = {{ $product->variants->count() }};
    let nextVariantNumber = {{ $product->variants->count() > 0 ? $product->variants->count() + 1 : 1 }};

    // Helper function to show error messages
    function showValidationError(message, element) {
        // Create error div
        const errorDiv = document.createElement('div');
        errorDiv.className = 'validation-error-message bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-3 flex items-center';
        errorDiv.innerHTML = `
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            ${message}
        `;

        // Insert the error message after the element
        if (element && element.parentNode) {
            element.parentNode.insertBefore(errorDiv, element.nextSibling);
        } else {
            // Fallback to adding at the top of the form
            const form = document.getElementById('productForm');
            if (form) {
                form.insertBefore(errorDiv, form.firstChild);
            }
        }

        // Remove the error message after 5 seconds
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        const productImages = document.getElementById('productImages');
        const imagePreview = document.getElementById('imagePreview');
        const addVariantBtn = document.getElementById('addVariant');
        const variantsContainer = document.getElementById('variantsContainer');

        // Auto-generate slug from name
        nameInput?.addEventListener('input', () => {
            slugInput.value = nameInput.value.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
        });

        // Handle image preview
        productImages?.addEventListener('change', function(e) {
            imagePreview.innerHTML = '';
            const files = e.target.files;
            
            if (files && files.length > 0) {
                Array.from(files).forEach((file, index) => {
                    // Validate file type
                    if (!file.type.startsWith('image/')) {
                        console.error('Invalid file type:', file.type);
                        return;
                    }
                    
                    // Validate file size (2MB max)
                    if (file.size > 2 * 1024 * 1024) {
                        console.error('File too large:', file.size);
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        const div = document.createElement('div');
                        div.className = 'image-preview relative group';
                        div.innerHTML = `
                            <img src="${ev.target.result}" alt="Preview" class="w-full h-24 object-cover rounded-lg border border-gray-300">
                            <button type="button" class="remove-image absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200" data-index="${index}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            <div class="text-xs text-gray-500 mt-1 truncate">${file.name}</div>
                        `;
                        
                        const removeBtn = div.querySelector('.remove-image');
                        removeBtn.addEventListener('click', function() {
                            div.remove();
                            // Remove the file from the input
                            const dt = new DataTransfer();
                            const inputFiles = Array.from(productImages.files);
                            inputFiles.splice(index, 1);
                            inputFiles.forEach(file => dt.items.add(file));
                            productImages.files = dt.files;
                        });
                        
                        imagePreview.appendChild(div);
                    };
                    reader.onerror = function() {
                        console.error('Error reading file:', file.name);
                    };
                    reader.readAsDataURL(file);
                });
            }
        });

        // Add variant button
        addVariantBtn?.addEventListener('click', function() {
            addManualVariantToForm();
        });

        // Form submission validation
        document.getElementById('productForm')?.addEventListener('submit', function(e) {
            const variantsContainer = document.getElementById('variantsContainer');
            if (!variantsContainer) {
                return; // No variants container found
            }

            const variantElements = variantsContainer.querySelectorAll('[data-variant]');
            let hasValidationErrors = false;

            // Validate all variants for duplicate attributes
            variantElements.forEach(variantElement => {
                if (variantElement) {
                    const isValid = validateVariantAttributes(variantElement);
                    if (!isValid) {
                        hasValidationErrors = true;
                    }
                }
            });

            // Prevent form submission if there are validation errors
            if (hasValidationErrors) {
                e.preventDefault();
                showValidationError('Please fix the duplicate attribute errors before submitting the form.', variantsContainer);
            }
        });

        // Event delegation for add/remove attribute buttons
        document.addEventListener('click', function(e) {
            // Add attribute button
            if (e.target.closest('.add-attribute')) {
                const button = e.target.closest('.add-attribute');
                const variantId = button.dataset.variant;
                addAttributeToVariant(variantId);
            }

            // Remove attribute button
            if (e.target.closest('.remove-attribute')) {
                const button = e.target.closest('.remove-attribute');
                const attributeItem = button.closest('.attribute-item');
                const attributesContainer = button.closest('.attributes-container');

                // Don't remove if it's the last attribute
                if (attributesContainer.querySelectorAll('.attribute-item').length > 1) {
                    attributeItem.remove();
                }
            }
        });

        // Event delegation for attribute selection changes
        document.addEventListener('change', function(e) {
            // Attribute select change
            if (e.target.classList.contains('attribute-select')) {
                const attributeSelect = e.target;
                const attributeItem = attributeSelect.closest('.attribute-item');
                const valueSelect = attributeItem.querySelector('.attribute-value-select');
                const attrId = attributeSelect.value;

                valueSelect.innerHTML = '';
                valueSelect.disabled = true;

                if (attrId) {
                    // Show loading state
                    valueSelect.innerHTML = '<option value="">Loading...</option>';

                    // Fetch attribute values via AJAX
                    fetch(`/admin/attributes/${attrId}/values`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(values => {
                            valueSelect.innerHTML = '';
                            valueSelect.disabled = false;

                            if (values.length > 0) {
                                // Add default option
                                const defaultOpt = document.createElement('option');
                                defaultOpt.value = "";
                                defaultOpt.text = "Select value";
                                defaultOpt.selected = true;
                                defaultOpt.disabled = true;
                                valueSelect.appendChild(defaultOpt);
                                values.forEach(v => {
                                    const opt = document.createElement('option');
                                    opt.value = v.id;
                                    opt.text = v.value;
                                    valueSelect.appendChild(opt);
                                });
                            } else {
                                valueSelect.innerHTML = '<option value="">No values found</option>';
                            }
                        })
                        .catch(error => {
                            console.error('Error loading attribute values:', error);
                            valueSelect.innerHTML = '<option value="">Error loading values</option>';
                        });
                } else {
                    valueSelect.innerHTML = '<option value="">Select Value</option>';
                }
            }

            // Attribute value select change - validate for duplicates
            if (e.target.classList.contains('attribute-value-select')) {
                const valueSelect = e.target;
                const attributeItem = valueSelect.closest('.attribute-item');
                const attributeSelect = attributeItem.querySelector('.attribute-select');
                const variantContainer = attributeItem.closest('[data-variant]');

                // Only validate if both attribute and value are selected
                if (attributeSelect.value && valueSelect.value) {
                    // Get all attribute items in this variant
                    const allAttributeItems = variantContainer.querySelectorAll('.attribute-item');

                    // Check for duplicate attribute values across different attributes
                    let hasDuplicateValue = false;

                    // Collect all selected attribute values in this variant (excluding current one)
                    const usedValues = [];
                    allAttributeItems.forEach(item => {
                        // Skip the current attribute item
                        if (item === attributeItem) {
                            return;
                        }

                        const itemValueSelect = item.querySelector('.attribute-value-select');
                        if (itemValueSelect && itemValueSelect.value) {
                            usedValues.push(itemValueSelect.value);
                        }
                    });

                    // Check if the selected value is already used
                    if (usedValues.includes(valueSelect.value)) {
                        hasDuplicateValue = true;
                    }

                    // If duplicate value found, show error and reset selection
                    if (hasDuplicateValue) {
                        showValidationError(
                            'This attribute value is already used in another attribute within this variant. Please select a different value.',
                            attributeItem
                        );
                        valueSelect.value = '';
                        return;
                    }

                    // Also check for duplicate attribute-value combinations
                    let hasDuplicateCombination = false;
                    allAttributeItems.forEach(item => {
                        // Skip the current attribute item
                        if (item === attributeItem) return;

                        const itemAttributeSelect = item.querySelector('.attribute-select');
                        const itemValueSelect = item.querySelector('.attribute-value-select');

                        if (itemAttributeSelect.value === attributeSelect.value &&
                            itemValueSelect.value === valueSelect.value) {
                            hasDuplicateCombination = true;
                        }
                    });

                    // If duplicate combination found, show error and reset selection
                    if (hasDuplicateCombination) {
                        showValidationError(
                            'This attribute and value combination already exists in this variant.',
                            attributeItem
                        );
                        valueSelect.value = '';
                        return;
                    }
                }
            }
        });
    });

    // Add manual variant to form
    function addManualVariantToForm() {
        variantCount++;
        const displayNumber = nextVariantNumber++;

        const variantHtml = `
    <div class="variant-item bg-gradient-to-br from-white to-gray-50 border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden" data-variant="${variantCount}">
        <!-- Variant Header -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <div class="bg-blue-500 text-white rounded-lg w-8 h-8 flex items-center justify-center font-bold text-sm mr-3">
                        ${displayNumber}
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Product Variant</h3>
                        <p class="text-sm text-gray-600">Configure your variant options</p>
                    </div>
                </div>
                <button type="button" class="bg-red-100 hover:bg-red-200 text-red-700 p-2 rounded-lg transition duration-200 group remove-variant" title="Remove variant">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Variant Body -->
        <div class="p-6">
            <!-- Variant Attributes Section -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <h4 class="text-md font-semibold text-gray-900">Variant Attributes</h4>
                    </div>
                    <button type="button" class="bg-purple-100 hover:bg-purple-200 text-purple-700 font-medium py-2 px-4 rounded-lg text-sm transition duration-200 flex items-center add-attribute" data-variant="${variantCount}">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Attribute
                    </button>
                </div>
                <div class="attributes-container space-y-4" data-variant="${variantCount}">
                    <!-- Initial empty attribute -->
                    <div class="attribute-item bg-gray-50 p-4 rounded-lg border border-gray-200" data-attribute-index="0">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                            <div class="md:col-span-5">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 inline mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Attribute
                                </label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white text-gray-900 attribute-select" name="variants[${variantCount}][attributes][0][attribute_id]" required>
                                    <option value="">Select an attribute</option>
                                    @foreach ($attributes as $attribute)
                                        <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-5">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 inline mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Value
                                </label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white text-gray-900 attribute-value-select" name="variants[${variantCount}][attributes][0][attribute_value_id]" required>
                                    <option value="">Select a value</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <button type="button" class="w-full bg-red-100 hover:bg-red-200 text-red-700 font-medium py-3 px-4 rounded-lg transition duration-200 remove-attribute flex items-center justify-center" title="Remove attribute">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Variant Details Section -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <h4 class="text-md font-semibold text-gray-900">Variant Details</h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="hidden" name="variants[${variantCount}][id]" value="">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            SKU <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="variants[${variantCount}][sku]" placeholder="e.g., TSHIRT-BLUE-LARGE" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white text-gray-900" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Price ($)
                        </label>
                        <input type="number" name="variants[${variantCount}][price]" step="0.01" min="0" placeholder="0.00" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white text-gray-900">
                        <p class="mt-1 text-xs text-gray-500">Leave empty to use product price</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Stock <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="variants[${variantCount}][stock]" value="10" placeholder="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white text-gray-900" required>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Status
                        </label>
                        <select name="variants[${variantCount}][status]" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white text-gray-900">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Variant Images
                        </label>
                        <input type="file" name="variants[${variantCount}][images][]" multiple accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white text-gray-900">
                        <p class="mt-1 text-xs text-gray-500">Multiple images allowed</p>
                    </div>
                </div>
            </div>
        </div>
    </div>`;

        variantsContainer.insertAdjacentHTML('beforeend', variantHtml);
    }

    // Remove variant function
    function removeVariant(button) {
        const variant = button.closest('[data-variant]');
        variant.remove();
    }

    // Add attribute to variant
    function addAttributeToVariant(variantId) {
        const attributesContainer = document.querySelector(`.attributes-container[data-variant="${variantId}"]`);
        if (!attributesContainer) {
            console.error('Attributes container not found for variant:', variantId);
            return;
        }

        const attributeCount = attributesContainer.querySelectorAll('.attribute-item').length;

        const attributeHtml = `
    <div class="attribute-item bg-gray-50 p-4 rounded-lg border border-gray-200" data-attribute-index="${attributeCount}">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="w-4 h-4 inline mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Attribute
                </label>
                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white text-gray-900 attribute-select" name="variants[${variantId}][attributes][${attributeCount}][attribute_id]" required>
                    <option value="">Select an attribute</option>
                    @foreach ($attributes as $attribute)
                        <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="w-4 h-4 inline mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Value
                </label>
                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white text-gray-900 attribute-value-select" name="variants[${variantId}][attributes][${attributeCount}][attribute_value_id]" required>
                    <option value="">Select a value</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <button type="button" class="w-full bg-red-100 hover:bg-red-200 text-red-700 font-medium py-3 px-4 rounded-lg transition duration-200 remove-attribute flex items-center justify-center" title="Remove attribute">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>`;

        attributesContainer.insertAdjacentHTML('beforeend', attributeHtml);
    }

    // Validate variant attributes for duplicates
    function validateVariantAttributes(variantContainer) {
        if (!variantContainer) {
            console.error('validateVariantAttributes called with null variantContainer');
            return true;
        }

        const attributeItems = variantContainer.querySelectorAll('.attribute-item');
        console.log('validateVariantAttributes: Found attribute items:', attributeItems.length);

        if (attributeItems.length === 0) {
            console.warn('validateVariantAttributes: No attribute items found in variant');
            return true; // No attributes to validate
        }

        const attributeCombinations = [];
        let hasDuplicates = false;

        // Clear previous error messages
        attributeItems.forEach(item => {
            const existingError = item.querySelector('.duplicate-error');
            if (existingError) {
                existingError.remove();
            }
            item.classList.remove('border-red-500', 'bg-red-50');
        });

        // Collect all attribute-value combinations
        attributeItems.forEach(item => {
            const attributeSelect = item.querySelector('.attribute-select');
            const valueSelect = item.querySelector('.attribute-value-select');

            if (attributeSelect && valueSelect && attributeSelect.value && valueSelect.value) {
                const combination = `${attributeSelect.value}-${valueSelect.value}`;
                console.log('validateVariantAttributes: Checking combination:', combination);

                if (attributeCombinations.includes(combination)) {
                    console.warn('validateVariantAttributes: Duplicate combination found:', combination);
                    hasDuplicates = true;
                    // Highlight duplicate
                    item.classList.add('border-red-500', 'bg-red-50');

                    // Add error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'duplicate-error mt-2 p-2 bg-red-100 border border-red-400 text-red-700 rounded text-sm';
                    errorDiv.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        This attribute and value combination already exists in this variant.
                    </div>
                `;
                    item.appendChild(errorDiv);
                } else {
                    attributeCombinations.push(combination);
                    console.log('validateVariantAttributes: Added combination:', combination);
                }
            }
        });

        console.log('validateVariantAttributes: Validation result:', !hasDuplicates, 'Combinations:', attributeCombinations);
        return !hasDuplicates;
    }

    // Event delegation for remove variant
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-variant')) {
            const button = e.target.closest('.remove-variant');
            removeVariant(button);
        }
    });
</script>
@endpush
