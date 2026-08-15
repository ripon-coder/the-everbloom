@extends('admin.layouts.app')

@section('title', 'Create Product')

@section('content')
    <div class="p-4 dark:bg-gray-900">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                    <span class="mr-2">✨</span> Create New Product
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Fill in the product details to add a new item to your catalog.</p>
            </div>
            <a href="{{ route('admin.products.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2.5 px-4 transition duration-200 flex items-center shadow-sm text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Products
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm"
            class="space-y-6">
            @csrf

            <!-- Client-side JS Error Container -->


            <!-- Product Type Selection -->
            <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <span class="w-7 h-7 bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 text-xs font-bold flex items-center justify-center mr-2">🏷️</span>
                        Product Type
                    </h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="relative flex items-center p-4 border border-gray-300 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150" id="singleTypeCard">
                        <input type="radio" name="product_type" id="type_single" value="single" class="h-4 w-4 text-blue-600 focus:ring-blue-500" {{ old('product_type', 'single') == 'single' ? 'checked' : '' }}>
                        <div class="ml-3">
                            <span class="block text-sm font-semibold text-gray-900 dark:text-white">Single / Simple Product</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400">Standard product with fixed price, stock quantity, and SKU.</span>
                        </div>
                    </label>
                    <label class="relative flex items-center p-4 border border-gray-300 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150" id="variantTypeCard">
                        <input type="radio" name="product_type" id="type_variant" value="variant" class="h-4 w-4 text-blue-600 focus:ring-blue-500" {{ old('product_type') == 'variant' ? 'checked' : '' }}>
                        <div class="ml-3">
                            <span class="block text-sm font-semibold text-gray-900 dark:text-white">Has Variants (Multiple Options)</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400">Product with multiple sizes, colors, or options (pricing & stock managed in Variants menu).</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 1. Basic Product Information -->
            <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="border-b border-gray-100 dark:border-gray-700 pb-3 mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <span class="w-7 h-7 bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 text-xs font-bold flex items-center justify-center mr-2">1</span>
                        Basic Information
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Premium Cotton T-Shirt"
                            class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('name') border-red-500 @enderror">
                        <p class="text-xs text-red-600 mt-1 hidden" id="nameError"></p>
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Slug / URL Keyword <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug') }}" placeholder="e.g. premium-cotton-t-shirt"
                            class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('slug') border-red-500 @enderror">
                        <div id="slugFeedback" class="mt-1 text-xs font-semibold hidden"></div>
                        @error('slug')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="brand_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Brand <span class="text-red-500">*</span>
                        </label>
                        <select id="brand_id" name="brand_id"
                            class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('brand_id') border-red-500 @enderror">
                            <option value="">Select Brand</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-red-600 mt-1 hidden" id="brandError"></p>
                        @error('brand_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id" id="category_id"
                            class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('category_id') border-red-500 @enderror">
                            <option value="">Select Category</option>
                            {!! \App\Helpers\CategoryHelper::BuildTree($allCategories, $parentId = null, $level = 0, $currentId = 0, $selectedParentId = old('category_id')) !!}
                        </select>
                        <p class="text-xs text-red-600 mt-1 hidden" id="categoryError"></p>
                        @error('category_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- 2. Pricing & Stock -->
            <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="border-b border-gray-100 dark:border-gray-700 pb-3 mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <span class="w-7 h-7 bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-400 text-xs font-bold flex items-center justify-center mr-2">2</span>
                        Pricing & Stock
                    </h2>
                </div>

                <!-- Single Product Price/Stock Section (Hidden when product_type === 'variant') -->
                <div id="simplePricingStockBlock" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Selling Price ({{ $currency_sign }}) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" placeholder="0.00"
                                class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('price') border-red-500 @enderror">
                            <p class="text-xs text-red-600 mt-1 hidden" id="priceError"></p>
                            @error('price')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="simple_discount_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Discount Price ({{ $currency_sign }})
                            </label>
                            <input type="number" id="simple_discount_price" name="simple_discount_price" value="{{ old('simple_discount_price') }}" step="0.01" min="0" placeholder="0.00"
                                class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <div>
                            <label for="simple_stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Stock Quantity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="simple_stock" name="simple_stock" value="{{ old('simple_stock', 10) }}" min="0" step="1" placeholder="10"
                                class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <p class="text-xs text-red-600 mt-1 hidden" id="stockError"></p>
                        </div>

                        <div>
                            <label for="simple_buying_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Buying Price ({{ $currency_sign }})
                            </label>
                            <input type="number" id="simple_buying_price" name="simple_buying_price" value="{{ old('simple_buying_price', 0) }}" step="0.01" min="0" placeholder="0.00"
                                class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <div>
                            <label for="simple_weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Weight (kg)
                            </label>
                            <input type="number" id="simple_weight" name="simple_weight" value="{{ old('simple_weight', 0) }}" step="0.01" min="0" placeholder="0.00"
                                class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <div>
                            <label for="simple_sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                SKU <span class="text-xs text-gray-400">(Auto if empty)</span>
                            </label>
                            <input type="text" id="simple_sku" name="simple_sku" value="{{ old('simple_sku') }}" placeholder="Auto-generated"
                                class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                    </div>

                    <div class="flex items-center pt-2">
                        <input type="hidden" name="is_free_delivery" value="0">
                        <label for="is_free_delivery" class="flex items-center space-x-2.5 cursor-pointer">
                            <input type="checkbox" id="is_free_delivery" name="is_free_delivery" value="1" {{ old('is_free_delivery') ? 'checked' : '' }}
                                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300 cursor-pointer">Free Delivery</span>
                        </label>
                    </div>
                </div>

                <!-- Info message when Has Variants is selected -->
                <div id="variantPricingNotice" class="hidden p-4 bg-blue-50 border border-blue-200 text-blue-700 text-xs">
                    💡 <strong>Variant Product Selected:</strong> Pricing, stock quantity, buying price, and SKUs will be defined individually for each variant in the dedicated Variants management menu after saving basic info.
                </div>

                <div class="mt-6">
                    <div>
                        <label for="is_featured" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Featured Product <span class="text-red-500">*</span>
                        </label>
                        <select id="is_featured" name="is_featured"
                            class="w-full md:w-1/2 px-3.5 py-2.5 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="0" {{ old('is_featured', '0') == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('is_featured') == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 3. Descriptions & Media -->
            <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="border-b border-gray-100 dark:border-gray-700 pb-3 mb-5 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <span class="w-7 h-7 bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 text-xs font-bold flex items-center justify-center mr-2">3</span>
                        Description & Images
                    </h2>
                </div>

                <div class="space-y-6">
                    <div>
                        <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Short Description
                        </label>
                        <textarea id="short_description" name="short_description" rows="3" placeholder="Brief summary of the product..."
                            class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ old('short_description') }}</textarea>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Full Description
                        </label>
                        <textarea id="description" name="description" rows="4"
                            class="w-full px-3.5 py-2.5 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Product Gallery Images
                        </label>
                        <div class="flex items-center justify-center w-full">
                            <label for="productImages"
                                class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 dark:border-gray-600 border-dashed cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-200">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="mb-1 text-sm text-gray-500 dark:text-gray-400">
                                        <span class="font-semibold text-blue-600">Click to upload</span> or drag images here
                                    </p>
                                    <p class="text-xs text-gray-400">PNG, JPG, WEBP (MAX. 2MB each)</p>
                                </div>
                                <input id="productImages" name="images[]" type="file" class="hidden" multiple accept="image/*" />
                            </label>
                        </div>
                        <div id="imagePreview" class="mt-4 grid grid-cols-2 md:grid-cols-6 gap-3"></div>
                    </div>
                </div>
            </div>

            <!-- Submit Action -->
            <div class="flex justify-end items-center space-x-4 pt-4">
                <a href="{{ route('admin.products.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white font-medium py-2.5 px-6 transition duration-200 text-sm">
                    Cancel
                </a>
                <button type="submit" id="submitProductBtn"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-7 shadow-lg hover:shadow-xl transition duration-200 flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save Product
                </button>
            </div>
        </form>
    </div>
    @push('scripts')
        <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const nameInput = document.getElementById('name');
                const slugInput = document.getElementById('slug');
                const slugFeedback = document.getElementById('slugFeedback');
                let isSlugManuallyEdited = false;
                let slugTimer = null;

                function validateAndCheckSlug(slugVal) {
                    if (!slugVal || !slugFeedback) {
                        slugFeedback.className = 'hidden';
                        return;
                    }

                    // Instant Client-Side Format Validation
                    if (/\s/.test(slugVal)) {
                        slugFeedback.className = 'mt-1 text-xs font-semibold text-red-600 block';
                        slugFeedback.innerHTML = '✖ Spaces are not allowed in slugs! Use hyphens (e.g. redmi-note-10)';
                        slugInput.classList.remove('border-green-500');
                        slugInput.classList.add('border-red-500');
                        return;
                    }

                    if (!/^[a-z0-9]+(?:-[a-z0-9]+)*$/i.test(slugVal)) {
                        slugFeedback.className = 'mt-1 text-xs font-semibold text-red-600 block';
                        slugFeedback.innerHTML = '✖ Invalid format! Only letters, numbers, and hyphens are allowed.';
                        slugInput.classList.remove('border-green-500');
                        slugInput.classList.add('border-red-500');
                        return;
                    }

                    slugFeedback.className = 'mt-1 text-xs font-semibold text-gray-500 block';
                    slugFeedback.innerHTML = '⏳ Checking availability...';

                    fetch(`/admin/products/check-slug?slug=${encodeURIComponent(slugVal)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.available) {
                                slugFeedback.className = 'mt-1 text-xs font-semibold text-green-600 block flex items-center';
                                slugFeedback.innerHTML = `✓ ${data.message} (${data.slug})`;
                                slugInput.classList.remove('border-red-500');
                                slugInput.classList.add('border-green-500');
                            } else {
                                slugFeedback.className = 'mt-1 text-xs font-semibold text-red-600 block flex items-center';
                                slugFeedback.innerHTML = `✖ ${data.message}`;
                                slugInput.classList.remove('border-green-500');
                                slugInput.classList.add('border-red-500');
                            }
                        })
                        .catch(() => {
                            slugFeedback.className = 'hidden';
                        });
                }

                if (slugInput) {
                    slugInput.addEventListener('input', function() {
                        isSlugManuallyEdited = true;
                        clearTimeout(slugTimer);
                        slugTimer = setTimeout(() => {
                            validateAndCheckSlug(slugInput.value);
                        }, 200);
                    });
                }

                if (nameInput && slugInput) {
                    const generateSlug = function () {
                        if (!isSlugManuallyEdited) {
                            slugInput.value = nameInput.value
                                .toLowerCase()
                                .trim()
                                .replace(/[^a-z0-9\s-]/g, '')
                                .replace(/\s+/g, '-')
                                .replace(/-+/g, '-');
                            
                            clearTimeout(slugTimer);
                            slugTimer = setTimeout(() => {
                                validateAndCheckSlug(slugInput.value);
                            }, 200);
                        }
                    };

                    nameInput.addEventListener('input', generateSlug);
                    nameInput.addEventListener('keyup', generateSlug);
                    nameInput.addEventListener('change', generateSlug);
                }

            // Product Type Toggle Logic
            const typeSingleRadio = document.getElementById('type_single');
            const typeVariantRadio = document.getElementById('type_variant');
            const simplePricingStockBlock = document.getElementById('simplePricingStockBlock');
            const variantPricingNotice = document.getElementById('variantPricingNotice');

            function toggleProductType() {
                const isVariant = typeVariantRadio && typeVariantRadio.checked;
                const simpleInputs = simplePricingStockBlock ? simplePricingStockBlock.querySelectorAll('input') : [];
                if (isVariant) {
                    if (simplePricingStockBlock) simplePricingStockBlock.classList.add('hidden');
                    if (variantPricingNotice) variantPricingNotice.classList.remove('hidden');
                    simpleInputs.forEach(input => { input.disabled = true; });
                } else {
                    if (simplePricingStockBlock) simplePricingStockBlock.classList.remove('hidden');
                    if (variantPricingNotice) variantPricingNotice.classList.add('hidden');
                    simpleInputs.forEach(input => { input.disabled = false; });
                }
            }

            document.querySelectorAll('input[name="product_type"]').forEach(radio => {
                radio.addEventListener('change', toggleProductType);
                radio.addEventListener('click', toggleProductType);
            });
            toggleProductType(); // Initial execution

            // Product Gallery Images Live Preview & Removal
            const productImagesInput = document.getElementById('productImages');
            const imagePreviewContainer = document.getElementById('imagePreview');
            let selectedGalleryFiles = new DataTransfer();

            function renderGalleryPreviews() {
                if (!imagePreviewContainer) return;
                imagePreviewContainer.innerHTML = '';
                const files = selectedGalleryFiles.files;

                if (files && files.length > 0) {
                    Array.from(files).forEach((file, index) => {
                        if (!file.type.startsWith('image/')) return;

                        const reader = new FileReader();
                        reader.onload = function(ev) {
                            const card = document.createElement('div');
                            card.className = 'relative group border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-1 shadow-sm';
                            card.innerHTML = `
                                <img src="${ev.target.result}" alt="Preview" class="w-full h-24 object-cover">
                                <button type="button" class="remove-gallery-img absolute top-1 right-1 bg-red-600 text-white w-5 h-5 flex items-center justify-center text-xs font-bold shadow hover:bg-red-700 transition" data-index="${index}" title="Remove image">
                                    ✕
                                </button>
                                <div class="p-1.5 flex items-center justify-between bg-gray-50 dark:bg-gray-800 mt-1">
                                    <label class="flex items-center space-x-1.5 cursor-pointer">
                                        <input type="radio" name="thumbnail_index" value="${index}" ${index === 0 ? 'checked' : ''} class="w-3.5 h-3.5 text-blue-600 focus:ring-blue-500">
                                        <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300">Thumbnail</span>
                                    </label>
                                </div>
                            `;
                            imagePreviewContainer.appendChild(card);
                        };
                        reader.readAsDataURL(file);
                    });
                }
            }

            productImagesInput?.addEventListener('change', function(e) {
                if (e.target.files) {
                    Array.from(e.target.files).forEach(file => selectedGalleryFiles.items.add(file));
                    productImagesInput.files = selectedGalleryFiles.files;
                    renderGalleryPreviews();
                }
            });

            imagePreviewContainer?.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-gallery-img');
                if (removeBtn) {
                    const removeIdx = parseInt(removeBtn.getAttribute('data-index'));
                    const updatedDataTransfer = new DataTransfer();
                    Array.from(selectedGalleryFiles.files).forEach((file, idx) => {
                        if (idx !== removeIdx) updatedDataTransfer.items.add(file);
                    });
                    selectedGalleryFiles = updatedDataTransfer;
                    if (productImagesInput) productImagesInput.files = selectedGalleryFiles.files;
                    renderGalleryPreviews();
                }
            });

            // Real-time Field-Level Live JS Validation
            const nameInputEl = document.getElementById('name');
            const brandSelectEl = document.getElementById('brand_id');
            const categorySelectEl = document.getElementById('category_id');
            const priceInputEl = document.getElementById('price');
            const stockInputEl = document.getElementById('simple_stock');

            function showFieldError(inputEl, errorElId, message) {
                const errEl = document.getElementById(errorElId);
                if (!inputEl) return;

                if (message) {
                    inputEl.classList.remove('border-gray-300', 'border-green-500');
                    inputEl.classList.add('border-red-500');
                    if (errEl) {
                        errEl.textContent = message;
                        errEl.classList.remove('hidden');
                    }
                } else {
                    inputEl.classList.remove('border-red-500');
                    inputEl.classList.add('border-gray-300');
                    if (errEl) {
                        errEl.textContent = '';
                        errEl.classList.add('hidden');
                    }
                }
            }

            nameInputEl?.addEventListener('blur', () => {
                showFieldError(nameInputEl, 'nameError', nameInputEl.value.trim() ? '' : 'This field is required');
            });
            brandSelectEl?.addEventListener('change', () => {
                showFieldError(brandSelectEl, 'brandError', brandSelectEl.value ? '' : 'This field is required');
            });
            categorySelectEl?.addEventListener('change', () => {
                showFieldError(categorySelectEl, 'categoryError', categorySelectEl.value ? '' : 'This field is required');
            });
            priceInputEl?.addEventListener('blur', () => {
                if (typeVariantRadio && typeVariantRadio.checked) return;
                const val = parseFloat(priceInputEl.value);
                showFieldError(priceInputEl, 'priceError', (!isNaN(val) && val >= 0) ? '' : 'Selling Price is required (min 0)');
            });
            stockInputEl?.addEventListener('blur', () => {
                if (typeVariantRadio && typeVariantRadio.checked) return;
                const val = parseInt(stockInputEl.value);
                showFieldError(stockInputEl, 'stockError', (!isNaN(val) && val >= 0) ? '' : 'Stock Quantity is required (min 0)');
            });

            // Instant Client-Side Form Validation on Submit
            const productForm = document.getElementById('productForm');
            const jsErrorContainer = document.getElementById('jsErrorContainer');
            const jsErrorList = document.getElementById('jsErrorList');

            productForm?.addEventListener('submit', function (e) {
                const errors = [];
                const isVariant = typeVariantRadio && typeVariantRadio.checked;
                const nameVal = nameInputEl?.value.trim();
                const brandVal = brandSelectEl?.value;
                const categoryVal = categorySelectEl?.value;
                const priceVal = parseFloat(priceInputEl?.value);
                const stockVal = parseInt(stockInputEl?.value);
                const slugVal = document.getElementById('slug')?.value.trim();

                // Field 1: Name
                if (!nameVal) {
                    errors.push('Product Name is required.');
                    showFieldError(nameInputEl, 'nameError', 'This field is required');
                } else {
                    showFieldError(nameInputEl, 'nameError', '');
                }

                // Field 2: Brand
                if (!brandVal) {
                    errors.push('Brand is required.');
                    showFieldError(brandSelectEl, 'brandError', 'This field is required');
                } else {
                    showFieldError(brandSelectEl, 'brandError', '');
                }

                // Field 3: Category
                if (!categoryVal) {
                    errors.push('Category is required.');
                    showFieldError(categorySelectEl, 'categoryError', 'This field is required');
                } else {
                    showFieldError(categorySelectEl, 'categoryError', '');
                }

                // Only validate price and stock if it's a Single product
                if (!isVariant) {
                    if (isNaN(priceVal) || priceVal < 0) {
                        errors.push('Selling Price is required.');
                        showFieldError(priceInputEl, 'priceError', 'This field is required');
                    } else {
                        showFieldError(priceInputEl, 'priceError', '');
                    }

                    if (isNaN(stockVal) || stockVal < 0) {
                        errors.push('Stock Quantity is required.');
                        showFieldError(stockInputEl, 'stockError', 'This field is required');
                    } else {
                        showFieldError(stockInputEl, 'stockError', '');
                    }
                }

                if (slugVal) {
                    if (/\s/.test(slugVal)) {
                        errors.push('Slug cannot contain spaces (use hyphens e.g. redmi-note-10).');
                    } else if (!/^[a-z0-9]+(?:-[a-z0-9]+)*$/i.test(slugVal)) {
                        errors.push('Slug can only contain letters, numbers, and single hyphens.');
                    }
                }

                if (errors.length > 0) {
                    e.preventDefault();
                    return false;
                } else {
                    // Add loading state to button
                    const btn = document.getElementById('submitProductBtn');
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = `
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving...
                        `;
                    }
                }
            });

                if (document.querySelector('#description')) {
                    ClassicEditor
                        .create(document.querySelector('#description'))
                        .catch(error => {
                            console.error(error);
                        });
                }
            });
        </script>
        <style>
            .ck-editor__editable_inline {
                min-height: 200px;
            }
        </style>
    @endpush
    @include('admin.products.js._create')
@endsection
