@extends('admin.layouts.app')

@section('title', 'Products')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        padding: 6px 8px !important;
        border-color: #d1d5db !important;
        border-radius: 0.5rem !important;
        font-size: 0.875rem !important;
        display: flex !important;
        align-items: center !important;
        background-color: #ffffff !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #1f2937 !important;
        padding-left: 4px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        right: 8px !important;
    }
    .select2-dropdown {
        border-color: #d1d5db !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        z-index: 99999 !important;
        font-size: 0.875rem !important;
        overflow: hidden !important;
    }
    .select2-search__field {
        border-radius: 0.375rem !important;
        border-color: #d1d5db !important;
        padding: 6px 10px !important;
        outline: none !important;
    }
    .select2-search__field:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2) !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #2563eb !important;
    }
</style>

@section('content')
    <div class="p-4">
        <!-- Header & Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Products</h1>
            
            <div class="flex flex-wrap items-center gap-3">
                <!-- Quick Search Bar -->
                <form action="{{ route('admin.products.index') }}" method="GET" class="relative flex-1 sm:w-64">
                    @foreach(request()->except('search', 'page') as $key => $val)
                        @if($val !== null && $val !== '')
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endif
                    @endforeach
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Quick search product..."
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </form>

                <!-- Filter Drawer Toggle Button -->
                @php
                    $activeFiltersCount = count(array_filter(request()->only(['search', 'category_id', 'brand_id', 'status', 'product_type', 'is_featured', 'sort_by']), function($v, $k) {
                        if ($k === 'sort_by') return $v && $v !== 'latest';
                        return $v !== null && $v !== '';
                    }, ARRAY_FILTER_USE_BOTH));
                @endphp
                <button type="button" onclick="openFilterDrawer()"
                    class="relative inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filter
                    @if($activeFiltersCount > 0)
                        <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-blue-600 rounded-full">
                            {{ $activeFiltersCount }}
                        </span>
                    @endif
                </button>

                <!-- Add Product Button -->
                <a href="{{ route('admin.products.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Product
                </a>
            </div>
        </div>

        <!-- Active Filters Bar -->
        @if($activeFiltersCount > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 mb-6 flex flex-wrap items-center justify-between gap-2">
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="font-semibold text-gray-500 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Applied Filters:
                    </span>
                    
                    @if(request('search'))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 font-medium">
                            Keyword: "{{ request('search') }}"
                            <a href="{{ route('admin.products.index', request()->except('search')) }}" class="hover:text-blue-900 font-bold ml-1 text-sm">&times;</a>
                        </span>
                    @endif

                    @if(request('category_id') && $cat = $categories->firstWhere('id', request('category_id')))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 font-medium">
                            Category: {{ $cat->name }}
                            <a href="{{ route('admin.products.index', request()->except('category_id')) }}" class="hover:text-indigo-900 font-bold ml-1 text-sm">&times;</a>
                        </span>
                    @endif

                    @if(request('brand_id') && $brd = $brands->firstWhere('id', request('brand_id')))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-200 font-medium">
                            Brand: {{ $brd->name }}
                            <a href="{{ route('admin.products.index', request()->except('brand_id')) }}" class="hover:text-purple-900 font-bold ml-1 text-sm">&times;</a>
                        </span>
                    @endif

                    @if(request('status'))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-50 text-green-700 border border-green-200 font-medium">
                            Status: {{ ucfirst(request('status')) }}
                            <a href="{{ route('admin.products.index', request()->except('status')) }}" class="hover:text-green-900 font-bold ml-1 text-sm">&times;</a>
                        </span>
                    @endif

                    @if(request('product_type'))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 font-medium">
                            Type: {{ ucfirst(request('product_type')) }}
                            <a href="{{ route('admin.products.index', request()->except('product_type')) }}" class="hover:text-amber-900 font-bold ml-1 text-sm">&times;</a>
                        </span>
                    @endif

                    @if(request('is_featured') !== null && request('is_featured') !== '')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-yellow-50 text-yellow-800 border border-yellow-200 font-medium">
                            Featured: {{ request('is_featured') == '1' ? 'Yes' : 'No' }}
                            <a href="{{ route('admin.products.index', request()->except('is_featured')) }}" class="hover:text-yellow-900 font-bold ml-1 text-sm">&times;</a>
                        </span>
                    @endif

                    @if(request('sort_by') && request('sort_by') !== 'latest')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-gray-700 border border-gray-300 font-medium">
                            Sort: {{ str_replace('_', ' ', ucfirst(request('sort_by'))) }}
                            <a href="{{ route('admin.products.index', request()->except('sort_by')) }}" class="hover:text-gray-900 font-bold ml-1 text-sm">&times;</a>
                        </span>
                    @endif
                </div>

                <div class="flex items-center space-x-3 text-xs">
                    <button type="button" onclick="openFilterDrawer()" class="text-blue-600 hover:text-blue-800 font-semibold flex items-center">
                        Edit Filters
                    </button>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('admin.products.index') }}" class="text-red-600 hover:text-red-800 font-semibold underline">
                        Clear All
                    </a>
                </div>
            </div>
        @endif

        <!-- Products Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Featured</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Variants</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php $thumb = $product->firstImage ?? $product->anyImage; @endphp
                                    @if ($thumb)
                                        <img src="{{ $thumb->getImageUrl() }}" alt="{{ $product->name }}"
                                            class="w-12 h-12 rounded-lg object-cover">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $product->slug }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $product->brand?->name ?: 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $product->category?->name ?: 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $product->is_featured ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $product->is_featured ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $product->variants_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $product->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button type="button" 
                                                data-product="{{ json_encode([
                                                    'id' => $product->id,
                                                    'name' => $product->name,
                                                    'product_type' => $product->product_type ?? 'single',
                                                    'price' => $product->price,
                                                    'status' => $product->status,
                                                    'is_featured' => (int) $product->is_featured,
                                                    'is_free_delivery' => (int) $product->is_free_delivery
                                                ]) }}"
                                                class="quick-edit-btn text-purple-600 hover:text-purple-900 transition duration-150"
                                                title="Quick Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                        </button>
                                         <a href="{{ route('admin.products.show', $product) }}"
                                            class="text-blue-600 hover:text-blue-900 transition duration-150"
                                            title="View">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                            class="text-yellow-600 hover:text-yellow-900 transition duration-150"
                                            title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                        @if ($product->trashed())
                                            <form action="{{ route('admin.products.restore', $product->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="text-green-600 hover:text-green-900 transition duration-150"
                                                    title="Restore">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                            <button
                                                onclick="showDeleteModal('product permanently', '{{ route('admin.products.force-delete', $product->id) }}', '{{ $product->name }}')"
                                                class="text-red-600 hover:text-red-900 transition duration-150"
                                                title="Force Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        @else
                                            <button
                                                onclick="showDeleteModal('product', '{{ route('admin.products.destroy', $product->id) }}', '{{ $product->name }}')"
                                                class="text-red-600 hover:text-red-900 transition duration-150"
                                                title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                            </path>
                                        </svg>
                                        @if(request()->hasAny(['search', 'category_id', 'brand_id', 'status', 'product_type', 'is_featured', 'sort_by']))
                                            <p class="text-lg font-medium text-gray-900">No products match your search or filters</p>
                                            <p class="text-sm text-gray-500">Try adjusting your filters or clearing search criteria.</p>
                                            <a href="{{ route('admin.products.index') }}"
                                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center border border-gray-300">
                                                Clear All Filters
                                            </a>
                                        @else
                                            <p class="text-lg font-medium text-gray-900">No products found</p>
                                            <p class="text-sm text-gray-500">Get started by creating your first product.</p>
                                            <a href="{{ route('admin.products.create') }}"
                                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                Create Product
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->count() > 0)
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        {{ $products->links() }}
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">{{ $products->firstItem() }}</span> to <span
                                    class="font-medium">{{ $products->lastItem() }}</span> of{' '}
                                <span class="font-medium">{{ $products->total() }}</span> results
                            </p>
                        </div>
                        <div>
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Edit Modal -->
    <div id="quickEditModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-900/60 transition-opacity" aria-hidden="true" onclick="closeQuickEditModal()"></div>

        <!-- Modal panel -->
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
            <form id="quickEditForm" onsubmit="submitQuickEdit(event)">
                @csrf
                <input type="hidden" id="quick_edit_id">
                
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-xl font-bold text-gray-900">Quick Edit Product</h3>
                    <button type="button" onclick="closeQuickEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Basic Info -->
                    <div class="space-y-4">
                        <div>
                            <label for="quick_name" class="block text-sm font-bold text-gray-700 mb-1">Product Name</label>
                            <input type="text" id="quick_name" name="name" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg outline-none text-gray-600 bg-gray-100 cursor-not-allowed" readonly>
                        </div>
                    </div>

                    <!-- Toggles Section -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-6 border-t border-gray-100">
                        <div class="flex items-center space-x-3 p-3 bg-gray-50/50 rounded-lg">
                            <input type="checkbox" id="quick_is_featured" name="is_featured" class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                            <label for="quick_is_featured" class="text-sm font-bold text-gray-700 cursor-pointer">Featured Product</label>
                        </div>
                        
                        <div id="quick_free_delivery_wrapper" class="flex items-center space-x-3 p-3 bg-gray-50/50 rounded-lg">
                            <input type="checkbox" id="quick_is_free_delivery" name="is_free_delivery" class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                            <label for="quick_is_free_delivery" class="text-sm font-bold text-gray-700 cursor-pointer">Free Delivery</label>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t border-gray-100">
                    <button type="button" onclick="closeQuickEditModal()" class="w-full sm:w-auto px-5 py-2.5 text-sm font-bold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-all shadow-lg shadow-blue-500/30">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right-Side Filter Drawer Backdrop & Drawer Container -->
    <div id="filterDrawerBackdrop" 
        class="fixed inset-0 bg-gray-900/40 opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out z-[140]"
        onclick="closeFilterDrawer()"></div>

    <aside id="filterDrawer" 
        class="fixed top-0 right-0 z-[150] h-full w-full max-w-md bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col"
        aria-label="Filter Products Drawer">
        
        <!-- Drawer Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/80">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Filter Products</h2>
                    <p class="text-xs text-gray-500">Refine product listing criteria</p>
                </div>
            </div>
            <button type="button" onclick="closeFilterDrawer()" 
                class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition duration-150 focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Drawer Body Form -->
        <form action="{{ route('admin.products.index') }}" method="GET" id="drawerFilterForm" class="flex-1 flex flex-col overflow-hidden">
            <div class="flex-1 overflow-y-auto p-6 space-y-5">
                <!-- Search Input -->
                <div>
                    <label for="drawer_search" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Search Keyword</label>
                    <div class="relative">
                        <input type="text" name="search" id="drawer_search" value="{{ request('search') }}"
                            placeholder="Name, slug, or Product ID..."
                            class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-150">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="drawer_category_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Category</label>
                    <select name="category_id" id="drawer_category_id"
                        class="select2-searchable w-full py-2.5 px-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-150">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Brand Filter -->
                <div>
                    <label for="drawer_brand_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Brand</label>
                    <select name="brand_id" id="drawer_brand_id"
                        class="select2-searchable w-full py-2.5 px-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-150">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="drawer_status" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Status</label>
                    <select name="status" id="drawer_status"
                        class="select2-searchable w-full py-2.5 px-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-150">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Product Type Filter -->
                <div>
                    <label for="drawer_product_type" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Product Type</label>
                    <select name="product_type" id="drawer_product_type"
                        class="select2-searchable w-full py-2.5 px-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-150">
                        <option value="">All Types</option>
                        <option value="single" {{ request('product_type') === 'single' ? 'selected' : '' }}>Single</option>
                        <option value="variant" {{ request('product_type') === 'variant' ? 'selected' : '' }}>Variant</option>
                    </select>
                </div>

                <!-- Featured Filter -->
                <div>
                    <label for="drawer_is_featured" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Featured Product</label>
                    <select name="is_featured" id="drawer_is_featured"
                        class="select2-searchable w-full py-2.5 px-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-150">
                        <option value="">All Products</option>
                        <option value="1" {{ request('is_featured') === '1' ? 'selected' : '' }}>Featured Only</option>
                        <option value="0" {{ request('is_featured') === '0' ? 'selected' : '' }}>Non-Featured</option>
                    </select>
                </div>

                <!-- Sort By -->
                <div>
                    <label for="drawer_sort_by" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Sort Order</label>
                    <select name="sort_by" id="drawer_sort_by"
                        class="select2-searchable w-full py-2.5 px-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-150">
                        <option value="latest" {{ request('sort_by', 'latest') === 'latest' ? 'selected' : '' }}>Latest (Newest First)</option>
                        <option value="oldest" {{ request('sort_by') === 'oldest' ? 'selected' : '' }}>Oldest (First Created)</option>
                        <option value="name_asc" {{ request('sort_by') === 'name_asc' ? 'selected' : '' }}>Name (A to Z)</option>
                        <option value="name_desc" {{ request('sort_by') === 'name_desc' ? 'selected' : '' }}>Name (Z to A)</option>
                    </select>
                </div>
            </div>

            <!-- Drawer Footer Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between gap-3">
                <a href="{{ route('admin.products.index') }}" 
                    class="w-1/2 px-4 py-2.5 text-center text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition duration-150">
                    Reset
                </a>
                <button type="submit" 
                    class="w-1/2 px-4 py-2.5 text-center text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-md shadow-blue-500/20 transition duration-150">
                    Apply Filters
                </button>
            </div>
        </form>
    </aside>

    @section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function initSelect2() {
            if ($.fn.select2) {
                $('.select2-searchable').select2({
                    dropdownParent: $('#filterDrawer'),
                    width: '100%'
                });
            }
        }

        function openFilterDrawer() {
            const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
            if (scrollbarWidth > 0) {
                $('body').css('padding-right', scrollbarWidth + 'px');
            }
            $('body').addClass('overflow-hidden');
            $('#filterDrawerBackdrop').removeClass('opacity-0 pointer-events-none').addClass('opacity-100');
            $('#filterDrawer').removeClass('translate-x-full').addClass('translate-x-0');
            
            initSelect2();
        }

        function closeFilterDrawer() {
            $('#filterDrawerBackdrop').addClass('opacity-0 pointer-events-none').removeClass('opacity-100');
            $('#filterDrawer').addClass('translate-x-full').removeClass('translate-x-0');
            setTimeout(function() {
                $('body').removeClass('overflow-hidden').css('padding-right', '');
            }, 300);
        }

        $(document).on('keydown', function(e) {
            if (e.key === "Escape") {
                closeFilterDrawer();
                closeQuickEditModal();
            }
        });

        $(document).ready(function() {
            $(document).on('click', '.quick-edit-btn', function(e) {
                e.preventDefault();
                const btn = $(this).closest('.quick-edit-btn');
                const rawData = btn.attr('data-product');
                let product = {};
                try {
                    product = typeof rawData === 'string' ? JSON.parse(rawData) : btn.data('product');
                } catch(err) {
                    product = btn.data('product');
                }
                openQuickEditModal(product);
            });
        });

        function openQuickEditModal(product) {
            $('#quick_edit_id').val(product.id);
            $('#quick_name').val(product.name);
            
            const isFeatured = product.is_featured == 1 || product.is_featured === true || product.is_featured === '1';
            const isFreeDelivery = product.is_free_delivery == 1 || product.is_free_delivery === true || product.is_free_delivery === '1';
            
            $('#quick_is_featured').prop('checked', isFeatured);
            $('#quick_is_free_delivery').prop('checked', isFreeDelivery);
            
            // Show Free Delivery ONLY if single product
            const pType = product.product_type || 'single';
            if (pType === 'single') {
                $('#quick_free_delivery_wrapper').removeClass('hidden').addClass('flex');
            } else {
                $('#quick_free_delivery_wrapper').addClass('hidden').removeClass('flex');
            }

            const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
            if (scrollbarWidth > 0) {
                $('body').css('padding-right', scrollbarWidth + 'px');
            }
            $('#quickEditModal').removeClass('hidden').addClass('flex');
            $('body').addClass('overflow-hidden');
        }

        function closeQuickEditModal() {
            $('#quickEditModal').addClass('hidden').removeClass('flex');
            $('body').removeClass('overflow-hidden').css('padding-right', '');
        }

        function submitQuickEdit(event) {
            event.preventDefault();
            const id = $('#quick_edit_id').val();
            const name = $('#quick_name').val();
            const is_featured = $('#quick_is_featured').is(':checked');
            const is_free_delivery = $('#quick_is_free_delivery').is(':checked');

            const url = "{{ route('admin.products.quick-update', ':id') }}".replace(':id', id);
            
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                    is_featured: is_featured ? 1 : 0,
                    is_free_delivery: is_free_delivery ? 1 : 0
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        closeQuickEditModal();
                        location.reload(); 
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    if (errors) {
                        Object.values(errors).forEach(err => toastr.error(err[0]));
                    } else {
                        toastr.error('Something went wrong');
                    }
                }
            });
        }
    </script>
    @endsection
@endsection

