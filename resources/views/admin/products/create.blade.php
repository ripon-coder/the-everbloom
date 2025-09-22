@extends('admin.layouts.app')

@section('title', 'Create Product')

@section('content')
<div class="p-4">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Create Product</h1>
        <a href="{{ route('admin.products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back
        </a>
    </div>

    <!-- Error Alert -->
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm" class="space-y-6">
        @csrf
        
        <!-- Basic Product Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Product Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                        Slug <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug') }}" readonly
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
                    <select id="brand_id" name="brand_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('brand_id') border-red-500 @enderror">
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
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
                    <select id="category_id" name="category_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('category_id') border-red-500 @enderror">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                    <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                <textarea id="description" name="description" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Product Images -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Product Images</h2>
            
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Upload Images
                </label>
                <div class="flex items-center justify-center w-full">
                    <label for="productImages" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition duration-200">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <p class="mt-2 text-sm text-gray-500">You can select multiple images. First image will be set as default.</p>
                <div id="imagePreview" class="mt-4 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4"></div>
            </div>
        </div>

        <!-- Product Variants -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-1">Product Variants</h2>
                    <p class="text-sm text-gray-600">Create different versions of your product with unique attributes</p>
                </div>
                <button type="button" id="addVariantBtn"
                        class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-xl transition duration-200 flex items-center shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="font-medium">Add New Variant</span>
                </button>
            </div>

            <!-- Empty State -->
            <div id="emptyVariantsState" class="text-center py-12 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No variants yet</h3>
                <p class="text-gray-600 mb-4 max-w-md mx-auto">Start creating variants for your product by clicking the "Add New Variant" button above.</p>
                <div class="flex justify-center space-x-4 text-sm text-gray-500">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>
                        </svg>
                        Add multiple attributes
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2h-8a2 2 0 01-2-2v-4z" clip-rule="evenodd"></path>
                        </svg>
                        Set unique pricing
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 01-.723 1.745 3.066 3.066 0 00-2.812 2.812 3.066 3.066 0 01-3.976 0 3.066 3.066 0 01-1.745-.723 3.066 3.066 0 00-2.812-2.812 3.066 3.066 0 01-3.976 0 3.066 3.066 0 01-1.745.723 3.066 3.066 0 00-2.812-2.812 3.066 3.066 0 010-3.976 3.066 3.066 0 01.723-1.745 3.066 3.066 0 002.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Manage inventory
                    </div>
                </div>
            </div>

            <!-- Variants Container -->
            <div id="variantsContainer" class="space-y-6 hidden"></div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Create Product
            </button>
        </div>
    </form>
</div>

<!-- JS -->
<script>
// Global variable for variant counting
let variantCount = 0;
let nextVariantNumber = 1; // Track the next sequential variant number

document.addEventListener('DOMContentLoaded', function() {

    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const productImages = document.getElementById('productImages');
    const imagePreview = document.getElementById('imagePreview');
    const addVariantBtn = document.getElementById('addVariantBtn');
    const variantsContainer = document.getElementById('variantsContainer');

    // Auto-generate slug
    nameInput?.addEventListener('input', () => {
        slugInput.value = nameInput.value.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
    });

    // Image preview
    productImages?.addEventListener('change', function(e) {
        imagePreview.innerHTML = '';
        Array.from(e.target.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(ev) {
                const div = document.createElement('div');
                div.className = 'image-preview';
                div.innerHTML = `
                    <img src="${ev.target.result}" alt="Preview">
                    <button type="button" class="remove-image">
                        &times;
                    </button>
                `;
                div.querySelector('.remove-image').addEventListener('click', () => div.remove());
                imagePreview.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    });

    // Remove the problematic addOptionBtn code as it's not needed for variant functionality

    // Add variant button (manual)
    addVariantBtn?.addEventListener('click', function() {
        addManualVariantToForm();
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
            
            if(attrId){
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
                        
                        if(values.length > 0){
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
    });

});


// Preview variant image (single image)
function previewVariantImage(input, variantIndex) {
    const previewContainer = document.getElementById(`variant-image-preview-${variantIndex}`);
    previewContainer.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative inline-block';
            div.innerHTML = `
                <img src="${e.target.result}" alt="Variant Preview" class="w-32 h-32 object-cover rounded-lg shadow-md border-2 border-gray-200">
                <button type="button" class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm shadow-lg transition duration-200" onclick="removeVariantImage(this)" title="Remove image">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            previewContainer.appendChild(div);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Remove variant image preview
function removeVariantImage(button) {
    const previewContainer = button.parentElement.parentElement;
    const inputId = previewContainer.id.replace('variant-image-preview-', 'variant-image-');
    const input = document.getElementById(inputId);
    
    // Clear the file input
    input.value = '';
    
    // Remove the preview
    button.parentElement.remove();
}


// Add manual variant to form
function addManualVariantToForm() {
    variantCount++;
    const displayNumber = nextVariantNumber++;
    
    // Hide empty state and show variants container
    document.getElementById('emptyVariantsState').classList.add('hidden');
    document.getElementById('variantsContainer').classList.remove('hidden');
    
    const variantHtml = `
    <div class="bg-gradient-to-br from-white to-gray-50 border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden" data-variant="${variantCount}">
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
                <button type="button" class="bg-red-100 hover:bg-red-200 text-red-700 p-2 rounded-lg transition duration-200 group" onclick="removeVariant(this)" title="Remove variant">
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
                    <div class="attribute-item bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                            <div class="md:col-span-5">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg class="w-4 h-4 inline mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Attribute
                                </label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 attribute-select" name="variants[${variantCount}][attributes][0][attribute_id]" required>
                                    <option value="">Select an attribute</option>
                                    @foreach($attributes as $attribute)
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
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 attribute-value-select" name="variants[${variantCount}][attributes][0][attribute_value_id]" required>
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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            SKU
                        </label>
                        <input type="text" name="variants[${variantCount}][sku]" placeholder="e.g., TSHIRT-BLUE-LARGE" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Price ($)
                        </label>
                        <input type="number" name="variants[${variantCount}][price]" step="0.01" value="0" placeholder="0.00" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Stock
                        </label>
                        <input type="number" name="variants[${variantCount}][stock]" value="10" placeholder="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Status
                        </label>
                        <select name="variants[${variantCount}][status]" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Variant Image Section -->
            <div>
                <div class="flex items-center mb-4">
                    <svg class="w-5 h-5 text-pink-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h4 class="text-md font-semibold text-gray-900">Variant Image</h4>
                </div>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center bg-gray-50 hover:bg-gray-100 transition duration-200">
                    <input type="file" name="variants[${variantCount}][image]" accept="image/*" class="hidden" id="variant-image-${variantCount}" onchange="previewVariantImage(this, ${variantCount})">
                    <label for="variant-image-${variantCount}" class="cursor-pointer">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="text-gray-600 mb-1">Click to upload variant image</p>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB (single image)</p>
                    </label>
                </div>
                <div id="variant-image-preview-${variantCount}" class="mt-4 flex justify-center"></div>
            </div>
        </div>
    </div>`;
    
    variantsContainer.insertAdjacentHTML('beforeend', variantHtml);
}

// Remove variant function
function removeVariant(button) {
    const variant = button.closest('[data-variant]');
    variant.remove();
    
    // Check if there are any variants left
    const variantsContainer = document.getElementById('variantsContainer');
    if (variantsContainer.children.length === 0) {
        // Show empty state and hide variants container
        document.getElementById('emptyVariantsState').classList.remove('hidden');
        document.getElementById('variantsContainer').classList.add('hidden');
    }
}

// Add attribute to variant
function addAttributeToVariant(variantId) {
    const attributesContainer = document.querySelector(`.attributes-container[data-variant="${variantId}"]`);
    const attributeCount = attributesContainer.querySelectorAll('.attribute-item').length;
    
    const attributeHtml = `
    <div class="attribute-item bg-gray-50 p-4 rounded-lg border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <svg class="w-4 h-4 inline mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Attribute
                </label>
                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 attribute-select" name="variants[${variantId}][attributes][${attributeCount}][attribute_id]" required>
                    <option value="">Select an attribute</option>
                    @foreach($attributes as $attribute)
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
                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 attribute-value-select" name="variants[${variantId}][attributes][${attributeCount}][attribute_value_id]" required>
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

// Load manual attribute values
function loadManualAttributeValues(variantIndex) {
    const select = document.querySelector(`select[name="variants[${variantIndex}][attributes][0][attribute_id]"]`);
    const valueSelect = document.querySelector(`select[name="variants[${variantIndex}][attributes][0][attribute_value_id]"]`);
    const attrId = select.value;
    
    valueSelect.innerHTML = '';
    valueSelect.disabled = true;
    
    if(attrId){
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
                
                if(values.length > 0){
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
        valueSelect.innerHTML = '<option value="">Select Attribute First</option>';
    }
}
</script>
@endsection
