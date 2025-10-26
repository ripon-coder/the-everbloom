@extends('admin.layouts.app')

@section('title', 'Edit District')

@section('content')
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg max-w-4xl mx-auto">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Edit District</h2>
            <a href="{{ route('admin.district.index') }}"
                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                Back to District
            </a>
        </div>
        <form action="{{ route('admin.district.update', $district) }}" method="POST" enctype="multipart/form-data"
            class="p-6">
            @method('PUT')
            @csrf
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">District
                        Name</label>
                    <input type="text" name="name" id="name"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('name') border-red-500 @enderror"
                        value="{{ old('name',$district->name) }}" placeholder="Enter district name" required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span>
                            {{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="delivery_charge"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Delivery Charge ({{ $currency_sign }})</label>
                    <input type="text" name="delivery_charge" id="delivery_charge"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('name') border-red-500 @enderror"
                        value="{{ old('delivery_charge',$district->delivery_charge) }}" placeholder="120" required>
                    @error('delivery_charge')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span>
                            {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="information"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Information</label>
                <textarea name="information" id="information" rows="4"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('description') border-red-500 @enderror"
                    placeholder="Enter district message...">{{ old('information',$district->information) }}</textarea>
                @error('information')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500"><span class="font-medium">Oops!</span>
                        {{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Optional: If you want to this district any
                    information show.</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">District Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Created:</span>
                        <span
                            class="text-gray-900 dark:text-white ml-2">{{ $district->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Last Updated:</span>
                        <span
                            class="text-gray-900 dark:text-white ml-2">{{ $district->updated_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    Update District
                </button>
                <div class="flex space-x-3">
                    <button type="button"
                        onclick="event.preventDefault(); showDeleteModal('brand', '{{ route('admin.district.destroy', $district->id) }}', '{{ $district->name }}')"
                        class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800">
                        Delete District
                    </button>
                    <a href="{{ route('admin.district.index') }}"
                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection
