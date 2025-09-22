@extends('admin.layouts.app')

@section('title', 'Edit Attribute Value')

@section('content')
<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg max-w-4xl mx-auto">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Attribute Value</h2>
        <a href="{{ route('admin.attribute-values.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
            Back to Attribute Values
        </a>
    </div>
    <form action="{{ route('admin.attribute-values.update', $attributeValue->id) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        <div class="grid gap-6 mb-6">
            <div>
                <label for="attribute_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Attribute</label>
                <select name="attribute_id" id="attribute_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('attribute_id') border-red-500 @enderror" required>
                    <option value="">Select an attribute</option>
                    @foreach ($attributes as $attribute)
                        <option value="{{ $attribute->id }}" {{ old('attribute_id', $attributeValue->attribute_id) == $attribute->id ? 'selected' : '' }}>
                            {{ $attribute->name }}
                        </option>
                    @endforeach
                </select>
                @error('attribute_id')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-6">
            <label for="value" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Value</label>
            <input type="text" name="value" id="value" value="{{ old('value', $attributeValue->value) }}" placeholder="Enter value (e.g., Red, Blue, S, M)" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('value') border-red-500 @enderror">
            @error('value')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
            <select name="status" id="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('status') border-red-500 @enderror" required>
                <option value="active" {{ old('status', $attributeValue->status) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $attributeValue->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span> {{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-between items-center">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                Update Attribute Value
            </button>
            <div class="flex space-x-3">
                <button type="button" onclick="event.preventDefault(); showDeleteModal('attribute value', '{{ route('admin.attribute-values.destroy', $attributeValue->id) }}', 'ID #{{ $attributeValue->id }}')" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800">
                    Delete Attribute Value
                </button>
                <a href="{{ route('admin.attribute-values.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
