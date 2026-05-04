@extends('admin.layouts.app')

@section('title', 'Add Page')

@section('content')
<div class="p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold text-gray-800">Add New Page</h1>
        <a href="{{ route('admin.pages.index') }}" class="text-sm text-gray-600 hover:underline">Back to List</a>
    </div>

    <form action="{{ route('admin.pages.store') }}" method="POST" class="bg-white border border-gray-200 rounded shadow-sm p-6">
        @csrf
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Page Type (Optional)</label>
                <select id="quick_select" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-blue-50">
                    <option value="">-- Custom --</option>
                    <option value="about_us" data-title="About Us">About Us</option>
                    <option value="contact_us" data-title="Contact Us">Contact Us</option>
                    <option value="privacy_policy" data-title="Privacy Policy">Privacy Policy</option>
                    <option value="terms_conditions" data-title="Terms & Conditions">Terms & Conditions</option>
                    <option value="return_policy" data-title="Return Policy">Return Policy</option>
                    <option value="shipping_policy" data-title="Shipping Policy">Shipping Policy</option>
                    <option value="faq" data-title="FAQs">FAQs</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title" id="title" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ old('title') }}" required>
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                <input type="text" name="slug" id="slug" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ old('slug') }}">
                @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                <textarea name="content" id="editor">{{ old('content') }}</textarea>
                @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                    <input type="text" name="meta_title" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ old('meta_title') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                    <textarea name="meta_description" rows="2" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('meta_description') }}</textarea>
                </div>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" class="mr-2" checked>
                <label for="is_active" class="text-sm font-medium text-gray-700">Published Status</label>
            </div>
        </div>

        <div class="mt-8 border-t pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded transition">Save Page</button>
        </div>
    </form>
</div>

@section('scripts')
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor', { height: 400 });

    document.getElementById('quick_select').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            document.getElementById('title').value = selectedOption.getAttribute('data-title');
            document.getElementById('slug').value = selectedOption.value;
        }
    });

    document.getElementById('title').addEventListener('keyup', function() {
        if (!document.getElementById('quick_select').value) {
            document.getElementById('slug').value = this.value.toLowerCase().replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
        }
    });
</script>
@endsection
@endsection
