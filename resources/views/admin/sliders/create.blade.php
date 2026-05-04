@extends('admin.layouts.app')

@section('title', 'Create Hero Slider')

@section('content')
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg max-w-4xl mx-auto">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add New Hero Slider</h2>
            <a href="{{ route('admin.sliders.index') }}"
                class="text-gray-500 bg-white hover:bg-gray-100 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5">
                Back to Sliders
            </a>
        </div>
        <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <div class="mb-6">
                <label for="image" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Slider Image (Recommended: 1920x800)</label>
                <input type="file" name="image" id="image"
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none @error('image') border-red-500 @enderror"
                    accept="image/*" onchange="previewImage(this)" required>
                <div class="mt-4">
                    <img id="image-preview" src="{{ asset('images/default-logo.png') }}" alt="Slider Preview"
                        class="w-full max-h-64 object-cover rounded-lg border-2 border-dashed border-gray-300">
                </div>
                @error('image')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Main Title</label>
                    <input type="text" name="title" id="title"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        value="{{ old('title') }}" placeholder="Enter main title">
                    @error('title')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="sort_order" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sort Order</label>
                    <input type="number" name="sort_order" id="sort_order"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        value="{{ old('sort_order', 0) }}">
                </div>
                <div>
                    <label for="btn_text" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Button Text</label>
                    <input type="text" name="btn_text" id="btn_text"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        value="{{ old('btn_text') }}" placeholder="e.g. Shop Now">
                </div>
                <div>
                    <label for="btn_link" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Button Link</label>
                    <input type="text" name="btn_link" id="btn_link"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        value="{{ old('btn_link') }}" placeholder="e.g. /shop">
                </div>
            </div>

            <div class="mb-6">
                <label for="subtitle" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subtitle / Description</label>
                <textarea name="subtitle" id="subtitle" rows="3"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Enter slider subtitle or description...">{{ old('subtitle') }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <input id="status-active" type="radio" name="status" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" checked>
                        <label for="status-active" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Active</label>
                    </div>
                    <div class="flex items-center">
                        <input id="status-inactive" type="radio" name="status" value="0" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="status-inactive" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Inactive</label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.sliders.index') }}"
                    class="text-gray-500 bg-white hover:bg-gray-100 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5">
                    Cancel
                </a>
                <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Create Slider
                </button>
            </div>
        </form>
    </div>

    <script>
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
