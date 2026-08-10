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

        <!-- Form -->
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm"
            class="space-y-6">
            @csrf

            <!-- Basic Product Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4">
                        <h3 class="text-sm font-semibold text-red-700 mb-2">
                            âš ï¸ Validation Error
                        </h3>
                        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

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
                        <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('slug') border-red-500 @enderror">
                        <div id="slugFeedback" class="mt-1 text-xs font-semibold hidden"></div>
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
                        <select name="category_id" id="category_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('parent_id') border-red-500 @enderror">
                            <option value="">Select Category</option>
                            {!! \App\Helpers\CategoryHelper::BuildTree($allCategories, $parentId = null, $level = 0, $currentId = 0, $selectedParentId = old('category_id')) !!}
                        </select>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose the main category for this product
                        </p>
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span>
                                {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Selling Price ({{ $currency_sign }}) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01"
                            min="0" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('price') border-red-500 @enderror">
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="simple_stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Stock Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="simple_stock" name="simple_stock" value="{{ old('simple_stock', 10) }}" min="0" step="1"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label for="simple_buying_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Buying Price ({{ $currency_sign }})
                        </label>
                        <input type="number" id="simple_buying_price" name="simple_buying_price" value="{{ old('simple_buying_price', 0) }}" step="0.01" min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label for="simple_sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            SKU <span class="text-xs text-gray-500">(Auto if blank)</span>
                        </label>
                        <input type="text" id="simple_sku" name="simple_sku" value="{{ old('simple_sku') }}" placeholder="Auto-generated"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div>
                        <label for="is_free_delivery" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Is Free Delivery? <span class="text-red-500">*</span>
                        </label>
                        <select id="is_free_delivery" name="is_free_delivery" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('is_free_delivery') border-red-500 @enderror">
                            <option value="0" {{ old('is_free_delivery') == '0' ? 'selected' : '' }}>False</option>
                            <option value="1" {{ old('is_free_delivery') == '1' ? 'selected' : '' }}>True</option>
                        </select>
                        @error('is_free_delivery')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="is_featured" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Is Featured? <span class="text-red-500">*</span>
                        </label>
                        <select id="is_featured" name="is_featured" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('is_featured') border-red-500 @enderror">
                            <option value="0" {{ old('is_featured') == '0' ? 'selected' : '' }}>False</option>
                            <option value="1" {{ old('is_featured') == '1' ? 'selected' : '' }}>True</option>
                        </select>
                        @error('is_featured')
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
                    <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Short Description
                    </label>
                    <textarea id="short_description" name="short_description" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('description') border-red-500 @enderror">{{ old('short_description') }}</textarea>
                    @error('short_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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

                <!-- SEO Information -->
                <div class="mt-6">
                    <label for="meta_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Meta Title
                    </label>
                    <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title') }}"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('meta_title') border-red-500 @enderror">
                    @error('meta_title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mt-6">
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Meta Description
                    </label>
                    <textarea id="meta_description" name="meta_description" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white @error('meta_description') border-red-500 @enderror">{{ old('meta_description') }}</textarea>
                    @error('meta_description')
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
