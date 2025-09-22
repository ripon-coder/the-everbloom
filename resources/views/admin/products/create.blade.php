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
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Product Variants</h2>
                <button type="button" id="addVariantBtn"
                        class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Variant
                </button>
            </div>

            <!-- Quick Variant Add -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-md font-medium text-gray-800 mb-3">Quick Add Variant</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Attribute</label>
                        <select id="quickAttrSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" onchange="loadQuickAttributeValues()">
                            <option value="">Select Attribute</option>
                            @foreach($attributes as $attr)
                                <option value="{{ $attr->id }}">{{ $attr->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                        <select id="quickValueSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" disabled>
                            <option value="">Select Attribute First</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                        <input type="text" id="quickSku" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" placeholder="Auto-generated">
                    </div>
                    <div class="flex items-end">
                        <button type="button" id="addQuickVariant" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm transition duration-200" disabled>
                            Add Variant
                        </button>
                    </div>
                </div>
            </div>
            
            <div id="variantsContainer" class="space-y-4"></div>
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
document.addEventListener('DOMContentLoaded', function() {

    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const productImages = document.getElementById('productImages');
    const imagePreview = document.getElementById('imagePreview');
    const addVariantBtn = document.getElementById('addVariantBtn');
    const quickAttrSelect = document.getElementById('quickAttrSelect');
    const quickValueSelect = document.getElementById('quickValueSelect');
    const quickSku = document.getElementById('quickSku');
    const addQuickVariant = document.getElementById('addQuickVariant');
    const variantsContainer = document.getElementById('variantsContainer');

    let variantCount = 0;
    let selectedAttribute = null;
    let selectedValue = null;

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

    // Add option set
    addOptionBtn?.addEventListener('click', () => {
        optionSetCount++;
        const optionHtml = `
        <div class="option-set grid grid-cols-1 md:grid-cols-3 gap-3 items-end p-3 bg-white border border-gray-200 rounded-md" data-option-set="${optionSetCount}">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Attribute</label>
                <select class="attribute-select w-full px-2 py-1.5 border border-gray-300 rounded-md text-sm" onchange="loadAttributeValuesForOption(this)">
                    <option value="">Select Attribute</option>
                    @foreach($attributes as $attr)
                        <option value="{{ $attr->id }}">{{ $attr->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Values</label>
                <select class="attribute-value-select w-full px-2 py-1.5 border border-gray-300 rounded-md text-sm" multiple>
                    <option value="">Select Attribute First</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Hold Ctrl (or Cmd) to select multiple values.</p>
            </div>
            <div>
                <button type="button" class="remove-option-set w-full bg-red-100 hover:bg-red-200 text-red-700 py-1.5 px-3 rounded-md text-sm transition duration-200" onclick="this.closest('.option-set').remove()">
                    Remove
                </button>
            </div>
        </div>`;
        variantOptionsContainer.insertAdjacentHTML('beforeend', optionHtml);
    });

    // Quick attribute value selection
    quickAttrSelect?.addEventListener('change', function() {
        selectedAttribute = this.options[this.selectedIndex];
        loadQuickAttributeValues();
    });

    // Quick value selection
    quickValueSelect?.addEventListener('change', function() {
        selectedValue = this.options[this.selectedIndex];
        updateQuickVariantButton();
    });

    // Add quick variant
    addQuickVariant?.addEventListener('click', function() {
        addQuickVariantToForm();
    });

    // Add variant button (manual)
    addVariantBtn?.addEventListener('click', function() {
        addManualVariantToForm();
    });

});

// Load attribute values dynamically for option set using AJAX
function loadAttributeValuesForOption(select){
    const valSelect = select.closest('.option-set').querySelector('.attribute-value-select');
    valSelect.innerHTML = '';
    const attrId = select.value;
    
    // Show loading state
    valSelect.innerHTML = '<option value="">Loading...</option>';
    
    if(attrId){
        // Fetch attribute values via AJAX
        fetch(`/admin/attributes/${attrId}/values`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(values => {
                valSelect.innerHTML = '';
                
                if(values.length > 0){
                    values.forEach(v => {
                        const opt = document.createElement('option');
                        opt.value = v.id;
                        opt.text = v.value;
                        valSelect.appendChild(opt);
                    });
                } else {
                    valSelect.innerHTML = '<option value="">No values found</option>';
                }
            })
            .catch(error => {
                console.error('Error loading attribute values:', error);
                valSelect.innerHTML = '<option value="">Error loading values</option>';
            });
    } else {
        valSelect.innerHTML = '<option value="">Select Attribute First</option>';
    }
}

// Preview variant images
function previewVariantImages(input, variantIndex) {
    const previewContainer = document.getElementById(`variant-image-preview-${variantIndex}`);
    previewContainer.innerHTML = '';
    
    if (input.files) {
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" class="w-full h-20 object-cover rounded">
                    <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs" onclick="removeVariantImage(this)">
                        ×
                    </button>
                `;
                previewContainer.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    }
}

// Remove variant image preview
function removeVariantImage(button) {
    button.parentElement.remove();
}

// Load quick attribute values
function loadQuickAttributeValues() {
    const attrId = quickAttrSelect.value;
    quickValueSelect.innerHTML = '';
    quickValueSelect.disabled = true;
    addQuickVariant.disabled = true;
    
    if(attrId){
        // Show loading state
        quickValueSelect.innerHTML = '<option value="">Loading...</option>';
        
        // Fetch attribute values via AJAX
        fetch(`/admin/attributes/${attrId}/values`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(values => {
                quickValueSelect.innerHTML = '';
                quickValueSelect.disabled = false;
                
                if(values.length > 0){
                    values.forEach(v => {
                        const opt = document.createElement('option');
                        opt.value = v.id;
                        opt.text = v.value;
                        quickValueSelect.appendChild(opt);
                    });
                } else {
                    quickValueSelect.innerHTML = '<option value="">No values found</option>';
                }
            })
            .catch(error => {
                console.error('Error loading attribute values:', error);
                quickValueSelect.innerHTML = '<option value="">Error loading values</option>';
            });
    } else {
        quickValueSelect.innerHTML = '<option value="">Select Attribute First</option>';
    }
}

// Update quick variant button state
function updateQuickVariantButton() {
    const attrId = quickAttrSelect.value;
    const valueId = quickValueSelect.value;
    
    if(attrId && valueId){
        // Auto-generate SKU
        const attrName = quickAttrSelect.options[quickAttrSelect.selectedIndex].text;
        const valueName = quickValueSelect.options[quickValueSelect.selectedIndex].text;
        quickSku.value = `${attrName.substring(0,3)}-${valueName.substring(0,3)}`.toUpperCase();
        
        addQuickVariant.disabled = false;
    } else {
        quickSku.value = '';
        addQuickVariant.disabled = true;
    }
}

// Add quick variant to form
function addQuickVariantToForm() {
    const attrId = quickAttrSelect.value;
    const valueId = quickValueSelect.value;
    const sku = quickSku.value || `${quickAttrSelect.options[quickAttrSelect.selectedIndex].text.substring(0,3)}-${quickValueSelect.options[quickValueSelect.selectedIndex].text.substring(0,3)}`.toUpperCase();
    
    if(!attrId || !valueId) return;
    
    variantCount++;
    const attrName = quickAttrSelect.options[quickAttrSelect.selectedIndex].text;
    const valueName = quickValueSelect.options[quickValueSelect.selectedIndex].text;
    const variantTitle = `${attrName}: ${valueName}`;
    
    const variantHtml = `
    <div class="p-3 bg-gray-50 border border-gray-200 rounded-md">
        <div class="flex justify-between items-start mb-3">
            <h4 class="font-medium text-gray-800">${variantTitle}</h4>
            <button type="button" class="text-red-600 hover:text-red-800 text-sm" onclick="this.closest('.p-3').remove()">
                Remove
            </button>
        </div>
        <input type="hidden" name="variants[${variantCount}][attributes][0][attribute_id]" value="${attrId}">
        <input type="hidden" name="variants[${variantCount}][attributes][0][attribute_value_id]" value="${valueId}">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                <input type="text" name="variants[${variantCount}][sku]" value="${sku}" class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                <input type="number" name="variants[${variantCount}][price]" step="0.01" value="{{ old('price',0) }}" class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                <input type="number" name="variants[${variantCount}][stock]" value="10" class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="variants[${variantCount}][status]" class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
        <div class="mt-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Variant Images</label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                <input type="file" name="variants[${variantCount}][images][]" multiple accept="image/*" class="hidden" id="variant-images-${variantCount}" onchange="previewVariantImages(this, ${variantCount})">
                <label for="variant-images-${variantCount}" class="cursor-pointer">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="mt-1 text-sm text-gray-600">Click to upload variant images</p>
                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB each</p>
                </label>
            </div>
            <div id="variant-image-preview-${variantCount}" class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-2"></div>
        </div>
    </div>`;
    
    variantsContainer.insertAdjacentHTML('beforeend', variantHtml);
    
    // Reset quick add form
    quickAttrSelect.value = '';
    quickValueSelect.innerHTML = '<option value="">Select Attribute First</option>';
    quickValueSelect.disabled = true;
    quickSku.value = '';
    addQuickVariant.disabled = true;
}

// Add manual variant to form
function addManualVariantToForm() {
    variantCount++;
    
    const variantHtml = `
    <div class="p-3 bg-gray-50 border border-gray-200 rounded-md">
        <div class="flex justify-between items-start mb-3">
            <h4 class="font-medium text-gray-800">Manual Variant</h4>
            <button type="button" class="text-red-600 hover:text-red-800 text-sm" onclick="this.closest('.p-3').remove()">
                Remove
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Attribute</label>
                <select name="variants[${variantCount}][attributes][0][attribute_id]" class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm" required onchange="loadManualAttributeValues(${variantCount})">
                    <option value="">Select Attribute</option>
                    @foreach($attributes as $attr)
                        <option value="{{ $attr->id }}">{{ $attr->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                <select name="variants[${variantCount}][attributes][0][attribute_value_id]" class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm" required disabled>
                    <option value="">Select Attribute First</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                <input type="text" name="variants[${variantCount}][sku]" class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                <input type="number" name="variants[${variantCount}][price]" step="0.01" value="{{ old('price',0) }}" class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                <input type="number" name="variants[${variantCount}][stock]" value="10" class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="variants[${variantCount}][status]" class="w-full px-2 py-1 border border-gray-300 rounded-md text-sm">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
        <div class="mt-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Variant Images</label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                <input type="file" name="variants[${variantCount}][images][]" multiple accept="image/*" class="hidden" id="variant-images-${variantCount}" onchange="previewVariantImages(this, ${variantCount})">
                <label for="variant-images-${variantCount}" class="cursor-pointer">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="mt-1 text-sm text-gray-600">Click to upload variant images</p>
                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB each</p>
                </label>
            </div>
            <div id="variant-image-preview-${variantCount}" class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-2"></div>
        </div>
    </div>`;
    
    variantsContainer.insertAdjacentHTML('beforeend', variantHtml);
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
