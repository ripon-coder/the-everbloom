@extends('admin.layouts.app')

@section('title', 'Add Menu')

@section('content')
<div class="p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold text-gray-800">Add New Menu</h1>
        <a href="{{ route('admin.menus.index') }}" class="text-sm text-gray-600 hover:underline">Back to List</a>
    </div>

    <form action="{{ route('admin.menus.store') }}" method="POST" class="bg-white border border-gray-200 rounded shadow-sm p-6">
        @csrf
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Menu Name</label>
                <input type="text" name="name" id="name" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ old('name') }}" required>
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                <input type="text" name="slug" id="slug" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ old('slug') }}">
                @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">URL (Internal or External)</label>
                <input type="text" name="url" id="url" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ old('url') }}" placeholder="e.g., /shop or https://google.com">
                @error('url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                    <input type="number" name="order" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ old('order', 0) }}">
                    @error('order') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="status" id="status" class="mr-2" checked>
                    <label for="status" class="text-sm font-medium text-gray-700">Status (Active/Inactive)</label>
                </div>
            </div>
        </div>

        <div class="mt-8 border-t pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded transition">Save Menu</button>
        </div>
    </form>
</div>

@section('scripts')
<script>
    document.getElementById('name').addEventListener('keyup', function() {
        document.getElementById('slug').value = this.value.toLowerCase().replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
    });
</script>
@endsection
@endsection
