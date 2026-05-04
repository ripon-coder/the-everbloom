@extends('admin.layouts.app')

@section('title', 'Edit Page')

@section('content')
<div class="p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold text-gray-800">Edit Page</h1>
        <a href="{{ route('admin.pages.index') }}" class="text-sm text-gray-600 hover:underline">Back to List</a>
    </div>

    <form action="{{ route('admin.pages.update', $page->id) }}" method="POST" class="bg-white border border-gray-200 rounded shadow-sm p-6">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Page Type (Optional)</label>
                <select id="quick_select" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-blue-50">
                    <option value="">-- Custom --</option>
                    <option value="about_us" data-title="About Us" {{ $page->slug == 'about_us' ? 'selected' : '' }}>About Us</option>
                    <option value="contact_us" data-title="Contact Us" {{ $page->slug == 'contact_us' ? 'selected' : '' }}>Contact Us</option>
                    <option value="privacy_policy" data-title="Privacy Policy" {{ $page->slug == 'privacy_policy' ? 'selected' : '' }}>Privacy Policy</option>
                    <option value="terms_conditions" data-title="Terms & Conditions" {{ $page->slug == 'terms_conditions' ? 'selected' : '' }}>Terms & Conditions</option>
                    <option value="return_policy" data-title="Return Policy" {{ $page->slug == 'return_policy' ? 'selected' : '' }}>Return Policy</option>
                    <option value="shipping_policy" data-title="Shipping Policy" {{ $page->slug == 'shipping_policy' ? 'selected' : '' }}>Shipping Policy</option>
                    <option value="faq" data-title="FAQs" {{ $page->slug == 'faq' ? 'selected' : '' }}>FAQs</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title" id="title" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ old('title', $page->title) }}" required>
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                <input type="text" name="slug" id="slug" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ old('slug', $page->slug) }}">
                @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                <textarea name="content" id="editor">{{ old('content', $page->content) }}</textarea>
                @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                    <input type="text" name="meta_title" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ old('meta_title', $page->meta_title) }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                    <textarea name="meta_description" rows="2" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('meta_description', $page->meta_description) }}</textarea>
                </div>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" class="mr-2" {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="text-sm font-medium text-gray-700">Published Status</label>
            </div>
        </div>

        <div class="mt-8 border-t pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded transition">Update Page</button>
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
</script>
@endsection
@endsection
