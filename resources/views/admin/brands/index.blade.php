@extends('admin.layouts.app')

@section('title', 'Manage Brands')

@section('content')
<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Brand List</h2>
        <a href="{{ route('admin.brands.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            Add New Brand
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">ID</th>
                    <th scope="col" class="px-6 py-3">Logo</th>
                    <th scope="col" class="px-6 py-3">Name</th>
                    <th scope="col" class="px-6 py-3">Slug</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3">Created At</th>
                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($brands as $brand)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $brand->id }}
                    </th>
                    <td class="px-6 py-4">
                        @if(isset($brand->options['logo']) && $brand->options['logo'])
                            <img src="{{ asset('storage/' . $brand->options['logo']) }}" alt="{{ $brand->name }}" class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr($brand->name, 0, 1)) }}
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                        {{ $brand->name }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $brand->slug }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($brand->status == \App\Constants\BrandStatus::ACTIVE)
                            <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Active</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        {{ $brand->created_at->format('Y-m-d H:i') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('admin.brands.edit', $brand->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                            <button onclick="showDeleteModal('brand', '{{ route('admin.brands.destroy', $brand->id) }}', '{{ $brand->name }}')" class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center justify-center space-y-3">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <p class="text-lg font-medium">No brands found</p>
                            <p class="text-sm">Get started by creating your first brand.</p>
                            <a href="{{ route('admin.brands.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                Create Brand
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($brands->count() > 0)
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-center items-center">
        {{ $brands->links() }}
    </div>
    @endif
</div>
@endsection
