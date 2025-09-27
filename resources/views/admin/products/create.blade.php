@extends('admin.layouts.app')

@section('title', 'Create Product')

@section('content')
    <div class="p-4 dark:bg-gray-900">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Create Product</h1>
            <a href="{{ route('admin.products.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Back
            </a>
        </div>

        <!-- Error Alert -->
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm"
            class="space-y-6">
            @csrf

            <!-- Basic Product Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Slug <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug') }}" readonly
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
                        <label for="category_id"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category <span
                                class="text-red-500">*</span></label>
                        <select id="category_id" name="category_id" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('category_id') border-red-500 @enderror">
                            <option value="">🏠 None (Main Category)</option>
                            @php
                                // Function to build category tree options with simple parent-child indicators
                                function buildTreeOptions($categories, $parentId = null, $level = 0, $currentId = null)
                                {
                                    $options = '';
                                    foreach ($categories as $category) {
                                        if ($category->parent_id == $parentId) {
                                            // Skip current category in edit mode (circular reference prevention)
                                            if ($currentId && $category->id == $currentId) {
                                                continue;
                                            }

                                            $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                                            $hasChildren = $category->children->count() > 0;

                                            // Simple parent-child indicator
                                            $type = $hasChildren ? 'Parent' : 'Child';
                                            $prefix = $level > 0 ? '├── ' : '';

                                            $options .=
                                                '<option value="' .
                                                $category->id .
                                                '" ' .
                                                (old('category_id') == $category->id ? 'selected' : '') .
                                                '>';
                                            $options .= $indent . $prefix . $category->name . ' (' . $type . ')';
                                            $options .= '</option>';

                                            // Recursively add children
                                            $options .= buildTreeOptions(
                                                $categories,
                                                $category->id,
                                                $level + 1,
                                                $currentId,
                                            );
                                        }
                                    }
                                    return $options;
                                }

                                echo buildTreeOptions($categories);
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
                        <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01"
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
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Product Images -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Product Images</h2>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Upload Images
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
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">You can select multiple images. First image
                        will be set as
                        default.</p>
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
                <div id="emptyVariantsState"
                    class="text-center py-12 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-800">
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
                <div id="variantsContainer" class="space-y-6 hidden"></div>
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
                    Create Product
                </button>
            </div>
        </form>
    </div>

    <script>
        window.oldVariantsData = @json(old('variants'));
        window.laravelErrors = @json($errors->getMessages());
    </script>
    @include('components.products-javascript')
@endsection
