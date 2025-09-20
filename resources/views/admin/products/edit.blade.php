@extends('admin.layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg max-w-4xl mx-auto">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Product: {{ $product->name }}</h2>
        <a href="{{ route('admin.products.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
            Back to Products
        </a>
    </div>
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        @method('PUT')
        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Product Name</label>
                <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('name') border-red-500 @enderror" value="{{ old('name', $product->name) }}" required>
                @error('name')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="sku" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">SKU</label>
                <input type="text" name="sku" id="sku" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('sku') border-red-500 @enderror" value="{{ old('sku', $product->sku) }}" required>
                @error('sku')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Price ($)</label>
                <input type="number" name="price" id="price" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('price') border-red-500 @enderror" value="{{ old('price', $product->price) }}" step="0.01" min="0" required>
                @error('price')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="stock_quantity" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stock Quantity</label>
                <input type="number" name="stock_quantity" id="stock_quantity" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('stock_quantity') border-red-500 @enderror" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required>
                @error('stock_quantity')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                <select name="status" id="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('status') border-red-500 @enderror" required>
                    <option value="{{ \App\Constants\ProductStatus::DRAFT }}" {{ old('status', $product->status) == \App\Constants\ProductStatus::DRAFT ? 'selected' : '' }}>Draft</option>
                    <option value="{{ \App\Constants\ProductStatus::ACTIVE }}" {{ old('status', $product->status) == \App\Constants\ProductStatus::ACTIVE ? 'selected' : '' }}>Active</option>
                    <option value="{{ \App\Constants\ProductStatus::INACTIVE }}" {{ old('status', $product->status) == \App\Constants\ProductStatus::INACTIVE ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="image_path" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Product Image</label>
                <input type="file" name="image_path" id="image_path" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 @error('image_path') border-red-500 @enderror" accept="image/*">
                @error('image_path')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" id="file_input_help">Allowed types: jpg, jpeg, png, gif. Max size: 2MB. Leave empty to keep the current image.</p>
                @if($product->image_path)
                    <div class="mt-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Current Image:</p>
                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="mt-2 w-32 h-32 object-cover rounded-lg">
                    </div>
                @endif
            </div>
        </div>
        <div class="mb-6">
            <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
            <textarea name="description" id="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
            @enderror
        </div>
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.products.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                Cancel
            </a>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                Update Product
            </button>
        </div>
    </form>
</div>
@endsection
