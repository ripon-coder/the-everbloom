@extends('admin.layouts.app')

@section('title', 'Edit Brand')

@section('content')
<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg max-w-4xl mx-auto">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Brand</h2>
        <a href="{{ route('admin.brands.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
            Back to Brands
        </a>
    </div>
    <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        @method('PUT')
        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Brand Name</label>
                <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('name') border-red-500 @enderror" value="{{ old('name', $brand->name) }}" placeholder="Enter brand name" required>
                @error('name')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Slug</label>
                <input type="text" name="slug" id="slug" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('slug') border-red-500 @enderror" value="{{ old('slug', $brand->slug) }}" placeholder="brand-name" required>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">URL-friendly version of the name</p>
                @error('slug')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                <select name="status" id="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('status') border-red-500 @enderror" required>
                    <option value="{{ \App\Constants\BrandStatus::ACTIVE }}" {{ old('status', $brand->status) == \App\Constants\BrandStatus::ACTIVE ? 'selected' : '' }}>Active</option>
                    <option value="{{ \App\Constants\BrandStatus::INACTIVE }}" {{ old('status', $brand->status) == \App\Constants\BrandStatus::INACTIVE ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="logo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Brand Logo</label>
                <input type="file" name="logo" id="logo" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 @error('logo') border-red-500 @enderror" accept="image/*">
                @if(isset($brand->options['logo']) && $brand->options['logo'])
                    <div class="mt-2 flex items-center space-x-3">
                        <img src="{{ asset('storage/' . $brand->options['logo']) }}" alt="{{ $brand->name }}" class="w-12 h-12 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Current logo</span>
                    </div>
                @endif
                @error('logo')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave empty to keep current logo. Allowed types: jpg, jpeg, png, gif. Max size: 2MB.</p>
            </div>
        </div>
        
        <div class="mb-6">
            <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
            <textarea name="description" id="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('description') border-red-500 @enderror" placeholder="Enter brand description...">{{ old('description', $brand->description ?? '') }}</textarea>
            @error('description')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Optional: Provide a brief description about the brand.</p>
        </div>


        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
            <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Brand Information</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600 dark:text-gray-400">Created:</span>
                    <span class="text-gray-900 dark:text-white ml-2">{{ $brand->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <div>
                    <span class="text-gray-600 dark:text-gray-400">Last Updated:</span>
                    <span class="text-gray-900 dark:text-white ml-2">{{ $brand->updated_at->format('Y-m-d H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                Update Brand
            </button>
            <div class="flex space-x-3">
                <a href="{{ route('admin.brands.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    // Store original slug value
    slugInput.setAttribute('data-original', slugInput.value);
    
    // Auto-generate slug from name (only if slug hasn't been manually changed)
    nameInput.addEventListener('input', function() {
        if (slugInput.value === slugInput.getAttribute('data-original')) {
            const slug = this.value
                .toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove special characters
                .replace(/\s+/g, '-') // Replace spaces with hyphens
                .replace(/-+/g, '-') // Replace multiple hyphens with single hyphen
                .trim();
            
            slugInput.value = slug;
        }
    });
    
    // Store original slug value when user manually edits it
    slugInput.addEventListener('input', function() {
        this.setAttribute('data-original', this.value);
    });
});
</script>
@endsection
