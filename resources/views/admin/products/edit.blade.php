@extends('admin.layouts.app')

@section('title', 'Edit Product')

@section('content')
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <li>{{$error}}</li>
        @endforeach
    @endif
    <div class="p-4 dark:bg-gray-900">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Product</h1>
            <a href="{{ route('admin.products.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Back
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm"
            class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Product Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Slug <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug', $product->slug) }}" readonly
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white @error('slug') border-red-500 @enderror">
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="brand_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Brand <span class="text-red-500">*</span>
                        </label>
                        <select id="brand_id" name="brand_id" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('brand_id') border-red-500 @enderror">
                            <option value="">Select Brand</option>
                            @foreach ($brands as $brand)
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
                        <label for="category_id"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category <span
                                class="text-red-500">*</span></label>
                        <select id="category_id" name="category_id" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('category_id') border-red-500 @enderror">
                            <option value="">🏠 None (Main Category)</option>
                            @php
                                // Function to build category tree options with simple parent-child indicators
                                function buildTreeOptions($categories, $parentId = null, $level = 0, $currentId = null, $selectedId = null)
                                {
                                    $options = '';
                                    foreach ($categories as $category) {
                                        if ($category->parent_id == $parentId) {
                                            // Skip current category in edit mode (circular reference prevention)
                                            if ($currentId && $category->id == $currentId) {
                                                continue;
                                            }

                                            $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                                            $hasChildren = $category->children_count > 0;

                                            // Simple parent-child indicator
                                            $type = $hasChildren ? 'Parent' : 'Child';
                                            $prefix = $level > 0 ? '├── ' : '';

                                            $options .=
                                                '<option value="' .
                                                $category->id .
                                                '" ' .
                                                (($selectedId && $selectedId == $category->id) ? 'selected' : '') .
                                                '>';
                                            $options .= $indent . $prefix . $category->name . ' (' . $type . ')';
                                            $options .= '</option>';

                                            // Recursively add children
                                            $options .= buildTreeOptions(
                                                $categories,
                                                $category->id,
                                                $level + 1,
                                                $currentId,
                                                $selectedId
                                            );
                                        }
                                    }
                                    return $options;
                                }

                                echo buildTreeOptions($categories, null, 0, null, old('category_id', $product->category_id));
                            @endphp
                        </select>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose the main category for this product
                        </p>
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span>
                                {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Price (৳) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01"
                            min="0" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('price') border-red-500 @enderror">
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('status') border-red-500 @enderror">
                            <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Current Product Images -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Product Images</h2>
                
                @if($product->images_count > 0)
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($product->images as $image)
                            <div class="relative group">
                                <img src="{{ $image->getImageUrl() }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-24 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
                                @if($image->is_default)
                                    <span class="absolute top-2 right-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Default
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No images uploaded for this product.</p>
                    </div>
                @endif
            </div>

            <!-- Product Images -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Replace Product Images</h2>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Upload New Images
                    </label>
                    <div class="flex items-center justify-center w-full">
                        <label for="productImages"
                            class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-200">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                    </path>
                                </svg>
                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-semibold">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF, WEBP (MAX. 2MB each)</p>
                            </div>
                            <input id="productImages" name="images[]" type="file" class="hidden" multiple
                                accept="image/*" />
                        </label>
                    </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Note: Uploading new images will replace all existing images. First image will be set as default.</p>
                    <div id="imagePreview" class="mt-4 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4"></div>
                </div>
            </div>

            <!-- Product Variants -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Product Variants</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Create different versions of your product with
                            unique attributes
                        </p>
                    </div>
                    <button type="button" id="addVariantBtn"
                        class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-xl transition duration-200 flex items-center shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        <span class="font-medium">Add New Variant</span>
                    </button>
                </div>

                <!-- Empty State -->
                <div id="emptyVariantsState" class="{{ $product->variants_count > 0 ? 'hidden' : '' }} text-center py-12 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-800">
                    <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500 mb-4" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No variants yet</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4 max-w-md mx-auto">Start creating variants for your
                        product by clicking the
                        "Add New Variant" button above.</p>
                    <div class="flex justify-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Add multiple attributes
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2h-8a2 2 0 01-2-2v-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Set unique pricing
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 01-.723 1.745 3.066 3.066 0 00-2.812 2.812 3.066 3.066 0 01-3.976 0 3.066 3.066 0 01-1.745-.723 3.066 3.066 0 00-2.812-2.812 3.066 3.066 0 010-3.976 3.066 3.066 0 01.723-1.745 3.066 3.066 0 002.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Manage inventory
                        </div>
                    </div>
                </div>

                <!-- Variants Container -->
                <div id="variantsContainer" class="{{ $product->variants_count == 0 ? 'hidden' : '' }} space-y-6">
                    @foreach($product->variants as $index => $variant)
                        <div class="variant-item bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden" data-variant="{{ $index }}">
                            <!-- Variant Header -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900 dark:to-indigo-900 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center">
                                        <div class="bg-blue-500 text-white rounded-lg w-8 h-8 flex items-center justify-center font-bold text-sm mr-3">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Product Variant</h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Configure your variant options</p>
                                        </div>
                                    </div>
                                    <button type="button" class="bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:hover:bg-red-800 text-red-700 dark:text-red-300 p-2 rounded-lg transition duration-200 group remove-variant" title="Remove variant">
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
                                            <h4 class="text-md font-semibold text-gray-900 dark:text-white">Variant Attributes</h4>
                                        </div>
                                        <button type="button" class="bg-purple-100 hover:bg-purple-200 dark:bg-purple-900 dark:hover:bg-purple-800 text-purple-700 dark:text-purple-300 font-medium py-2 px-4 rounded-lg text-sm transition duration-200 flex items-center add-attribute" data-variant="{{ $index }}">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Add Attribute
                                        </button>
                                    </div>
                                    <div class="attributes-container space-y-4" data-variant="{{ $index }}">
                                        @foreach($variant->variantAttributes as $attrIndex => $variantAttribute)
                                            <div class="attribute-item bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600" data-attribute-index="{{ $attrIndex }}">
                                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                                    <div class="md:col-span-5">
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            <svg class="w-4 h-4 inline mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                            </svg>
                                                            Attribute
                                                        </label>
                                                        <select class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white attribute-select" name="variants[{{ $index }}][attributes][{{ $attrIndex }}][attribute_id]" required>
                                                            <option value="">Select an attribute</option>
                                                            @foreach($attributes as $attribute)
                                                                <option value="{{ $attribute->id }}" {{ $variantAttribute->attribute_id == $attribute->id ? 'selected' : '' }}>
                                                                    {{ $attribute->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="md:col-span-5">
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            <svg class="w-4 h-4 inline mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                            </svg>
                                                            Value
                                                        </label>
                                                        <select class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white attribute-value-select" name="variants[{{ $index }}][attributes][{{ $attrIndex }}][attribute_value_id]" required>
                                                            <option value="">Select a value</option>
                                                            @if($variantAttribute->attributeValue)
                                                                <option value="{{ $variantAttribute->attributeValue->id }}" selected>
                                                                    {{ $variantAttribute->attributeValue->value }}
                                                                </option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="md:col-span-2">
                                                        <button type="button" class="w-full bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:hover:bg-red-800 text-red-700 dark:text-red-300 font-medium py-3 px-4 rounded-lg transition duration-200 remove-attribute flex items-center justify-center" title="Remove attribute">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <!-- Variant Details Section -->
                                <div class="mb-8">
                                    <div class="flex items-center mb-4">
                                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        <h4 class="text-md font-semibold text-gray-900 dark:text-white">Variant Details</h4>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                <svg class="w-4 h-4 inline mr-1 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                                SKU <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="variants[{{ $index }}][sku]" value="{{ $variant->sku }}" placeholder="e.g., TSHIRT-BLUE-LARGE" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                <svg class="w-4 h-4 inline mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                                Stock <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number" name="variants[{{ $index }}][stock]" value="{{ $variant->stock }}" placeholder="0" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                <svg class="w-4 h-4 inline mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                                                </svg>
                                                Weight (kg) <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number" name="variants[{{ $index }}][weight]" value="{{ $variant->weight ?? '' }}" step="0.01" min="0" placeholder="0.00" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                                        </div>
                                    </div>
                                    
                                    <!-- Variant Pricing Section -->
                                    <div class="mt-6 py-2 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center mb-4 px-1">
                                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <h4 class="text-md font-semibold text-gray-900 dark:text-white">Variant Pricing</h4>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 px-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    <svg class="w-4 h-4 inline mr-1 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Buying Price (৳)
                                                </label>
                                                <div class="relative">
                                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">৳</span>
                                                    <input type="number" name="variants[{{ $index }}][buying_price]" step="0.01" min="0" value="{{ $variant->buying_price ?? 0 }}" placeholder="0.00" class="w-full pl-8 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white variant-buying-price">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    <svg class="w-4 h-4 inline mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Sell Price (৳)
                                                </label>
                                                <div class="relative">
                                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">৳</span>
                                                    <input type="number" name="variants[{{ $index }}][sell_price]" step="0.01" min="0" value="{{ $variant->sell_price ?? 0 }}" placeholder="0.00" class="w-full pl-8 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white variant-sell-price" required>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    <svg class="w-4 h-4 inline mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                                    </svg>
                                                    Discount Price (৳)
                                                </label>
                                                <div class="relative">
                                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">৳</span>
                                                    <input type="number" name="variants[{{ $index }}][discount_price]" step="0.01" min="0" value="{{ $variant->discount_price ?? 0 }}" placeholder="0.00" class="w-full pl-8 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white variant-discount-price">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                <svg class="w-4 h-4 inline mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                Status
                                            </label>
                                            <select name="variants[{{ $index }}][status]" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                <option value="active" {{ $variant->status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $variant->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                        <h4 class="text-md font-semibold text-gray-900 dark:text-white">Variant Images</h4>
                                    </div>
                                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-200">
                <input type="file" name="variants[{{ $index }}][images][]" accept="image/*" class="hidden" id="variant-image-{{ $index }}" onchange="previewVariantImage(this, {{ $index }})">
                                        <label for="variant-image-{{ $index }}" class="cursor-pointer">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-3" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <p class="text-gray-600 dark:text-gray-300 mb-1">Click to upload variant image</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF up to 2MB (single image only)</p>
                                        </label>
                                    </div>
                                    <div id="variant-image-preview-{{ $index }}" class="mt-4 flex justify-center flex-wrap gap-2">
                                        <!-- Current variant images with delete functionality -->
                                        @if($variant->images->count() > 0)
                                            @foreach($variant->images as $variantImage)
                                                <div class="relative group variant-existing-image" id="variant-image-container-{{ $variantImage->id }}">
                                                    <img src="{{ $variantImage->getImageUrl() }}" 
                                                         alt="Variant image" 
                                                         class="w-32 h-32 object-cover rounded-lg shadow-md border-2 border-gray-200">
                                                    <button type="button" 
                                                            class="absolute top-2 left-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 shadow-lg delete-variant-image" 
                                                            title="Delete image"
                                                            data-image-id="{{ $variantImage->id }}"
                                                            data-variant-index="{{ $index }}">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                    <div class="text-xs text-gray-500 mt-1 truncate max-w-32 text-center">{{ basename($variantImage->image) }}</div>
                                                    <!-- Hidden input to mark image for deletion -->
                                                    <input type="hidden" name="variants[{{ $index }}][delete_images][]" value="" id="delete-image-{{ $variantImage->id }}">
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.products.index') }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                    Cancel
                </a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Product
                </button>
            </div>
        </form>
    </div>
    @include('components.products-edit-js')
@endsection
