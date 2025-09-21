@extends('admin.layouts.app')

@section('title', 'Create Category')

@section('content')
<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg max-w-4xl mx-auto">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add New Category</h2>
        <a href="{{ route('admin.categories.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
            Back to Categories
        </a>
    </div>
    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category Name</label>
                <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('name') border-red-500 @enderror" value="{{ old('name') }}" placeholder="Enter category name" required>
                @error('name')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Slug</label>
                <input type="text" name="slug" id="slug" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('slug') border-red-500 @enderror" value="{{ old('slug') }}" placeholder="category-name" required>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">URL-friendly version of the name (auto-generated if left empty)</p>
                @error('slug')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="parent_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Parent Category</label>
                <select name="parent_id" id="parent_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('parent_id') border-red-500 @enderror">
                    <option value="">🏠 None (Main Category)</option>
                    @php
                        // Function to build category tree options with simple parent-child indicators
                        function buildTreeOptions($categories, $parentId = null, $level = 0, $currentId = null) {
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
                                    
                                    $options .= '<option value="' . $category->id . '" ' . (old('parent_id') == $category->id ? 'selected' : '') . '>';
                                    $options .= $indent . $prefix . $category->name . ' (' . $type . ')';
                                    $options .= '</option>';
                                    
                                    // Recursively add children
                                    $options .= buildTreeOptions($categories, $category->id, $level + 1, $currentId);
                                }
                            }
                            return $options;
                        }
                        
                        // Get all categories and build tree
                        $allCategories = \App\Models\Category::all();
                        echo buildTreeOptions($allCategories);
                    @endphp
                </select>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Optional: Select a parent category. Categories show as (Parent) or (Child)</p>
                @error('parent_id')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                <select name="status" id="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('status') border-red-500 @enderror" required>
                    <option value="{{ \App\Constants\CategoryStatus::ACTIVE }}" {{ old('status') == \App\Constants\CategoryStatus::ACTIVE ? 'selected' : '' }}>Active</option>
                    <option value="{{ \App\Constants\CategoryStatus::INACTIVE }}" {{ old('status') == \App\Constants\CategoryStatus::INACTIVE ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="image" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Category Image</label>
                <input type="file" name="image" id="image" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 @error('image') border-red-500 @enderror" accept="image/*" onchange="previewImage(this)">
                <div class="mt-2 flex items-center space-x-3">
                    <img id="image-preview" src="{{ asset('images/default-logo.png') }}" alt="Category Image Preview" class="w-12 h-12 rounded-full object-cover border-2 border-gray-200 dark:border-gray-600">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Default image (will be replaced when you upload an image)</span>
                </div>
                @error('image')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Allowed types: jpg, jpeg, png, gif. Max size: 2MB.</p>
            </div>
        </div>
        
        <div class="mb-6">
            <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
            <textarea name="description" id="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('description') border-red-500 @enderror" placeholder="Enter category description...">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Optional: Provide a brief description about the category.</p>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.categories.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                Cancel
            </a>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                Create Category
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    // Auto-generate slug from name
    nameInput.addEventListener('input', function() {
        if (!slugInput.value || slugInput.value === slugInput.getAttribute('data-original')) {
            const slug = this.value
                .toLowerCase()
                .replace(/[^\w\s-]/g, '') // Remove special characters
                .replace(/\s+/g, '-') // Replace spaces with hyphens
                .replace(/-+/g, '-') // Replace multiple hyphens with single hyphen
                .trim();
            
            slugInput.value = slug;
            slugInput.setAttribute('data-original', slug);
        }
    });
    
    // Store original slug value when user manually edits it
    slugInput.addEventListener('input', function() {
        this.setAttribute('data-original', this.value);
    });
});


// Function to preview image
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('image-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
