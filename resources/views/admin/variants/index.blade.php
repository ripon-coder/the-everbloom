@extends('admin.layouts.app')

@section('title', 'Variant Products')

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
    <div class="p-6">
        <!-- Header & Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Variant Products</h1>
                <p class="text-sm text-gray-500 mt-1">Listing all products configured with variant options.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <!-- Quick Search Bar -->
                <form action="{{ route('admin.variants.index') }}" method="GET" class="relative flex-1 sm:w-64">
                    @foreach(request()->except('search', 'page') as $key => $val)
                        @if($val !== null && $val !== '')
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endif
                    @endforeach
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Quick search variant..."
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </form>

                <!-- Filter Drawer Toggle Button -->
                @php
                    $activeFiltersCount = count(array_filter(request()->only(['search', 'category_id', 'brand_id', 'status', 'is_featured', 'sort_by']), function($v, $k) {
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

                <!-- Create Product Button -->
                <a href="{{ route('admin.products.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center shadow-sm text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Product
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
                            <a href="{{ route('admin.variants.index', request()->except('search')) }}" class="hover:text-blue-900 font-bold ml-1 text-sm">&times;</a>
                        </span>
                    @endif

                    @if(request('category_id') && $cat = $categories->firstWhere('id', request('category_id')))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 font-medium">
                            Category: {{ $cat->name }}
                            <a href="{{ route('admin.variants.index', request()->except('category_id')) }}" class="hover:text-indigo-900 font-bold ml-1 text-sm">&times;</a>
                        </span>
                    @endif

                    @if(request('brand_id') && $brd = $brands->firstWhere('id', request('brand_id')))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-200 font-medium">
                            Brand: {{ $brd->name }}
                            <a href="{{ route('admin.variants.index', request()->except('brand_id')) }}" class="hover:text-purple-900 font-bold ml-1 text-sm">&times;</a>
                        </span>
                    @endif

                    @if(request('status'))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-50 text-green-700 border border-green-200 font-medium">
                            Status: {{ ucfirst(request('status')) }}
                            <a href="{{ route('admin.variants.index', request()->except('status')) }}" class="hover:text-green-900 font-bold ml-1 text-sm">&times;</a>
                        </span>
                    @endif

                    @if(request('is_featured') !== null && request('is_featured') !== '')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-yellow-50 text-yellow-800 border border-yellow-200 font-medium">
                            Featured: {{ request('is_featured') == '1' ? 'Yes' : 'No' }}
                            <a href="{{ route('admin.variants.index', request()->except('is_featured')) }}" class="hover:text-yellow-900 font-bold ml-1 text-sm">&times;</a>
                        </span>
                    @endif

                    @if(request('sort_by') && request('sort_by') !== 'latest')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-gray-700 border border-gray-300 font-medium">
                            Sort: {{ str_replace('_', ' ', ucfirst(request('sort_by'))) }}
                            <a href="{{ route('admin.variants.index', request()->except('sort_by')) }}" class="hover:text-gray-900 font-bold ml-1 text-sm">&times;</a>
                        </span>
                    @endif
                </div>

                <div class="flex items-center space-x-3 text-xs">
                    <button type="button" onclick="openFilterDrawer()" class="text-blue-600 hover:text-blue-800 font-semibold flex items-center">
                        Edit Filters
                    </button>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('admin.variants.index') }}" class="text-red-600 hover:text-red-800 font-semibold underline">
                        Clear All
                    </a>
                </div>
            </div>
        @endif

        <!-- Variant Products Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Brand</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Variants</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @php
                                            $imgUrl = $product->firstImage?->getImageUrl() ?? $product->anyImage?->getImageUrl();
                                        @endphp
                                        @if($imgUrl)
                                            <img class="h-12 w-12 rounded-lg object-cover mr-3 border border-gray-100" 
                                                 src="{{ $imgUrl }}" alt="{{ $product->name }}">
                                        @else
                                            <div class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('admin.variants.show', $product->id) }}" class="text-sm font-semibold text-blue-600 hover:underline">
                                                {{ $product->name }}
                                            </a>
                                            <span class="block text-xs text-gray-400 font-mono">Slug: {{ $product->slug }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $product->category->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $product->brand->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                        {{ $product->variants->count() }} Variants
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end items-center">
                                        <!-- View button -->
                                        <a href="{{ route('admin.variants.show', $product->id) }}"
                                           class="inline-flex items-center px-3 py-1.5 border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg text-xs font-medium transition duration-150"
                                           title="View Product Variants">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                        @if(request()->hasAny(['search', 'category_id', 'brand_id', 'status', 'is_featured', 'sort_by']))
                                            <p class="text-lg font-medium text-gray-900">No variant products match your search or filters</p>
                                            <p class="text-sm text-gray-500">Try adjusting your filters or clearing search criteria.</p>
                                            <a href="{{ route('admin.variants.index') }}"
                                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center border border-gray-300 text-sm">
                                                Clear All Filters
                                            </a>
                                        @else
                                            <p class="text-lg font-medium text-gray-900">No variant products found</p>
                                            <p class="text-sm text-gray-500">No products configured with variant options exist yet.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <!-- Right-Side Filter Drawer Backdrop & Drawer Container -->
    <div id="filterDrawerBackdrop" 
        class="fixed inset-0 bg-gray-900/40 opacity-0 pointer-events-none transition-opacity duration-300 ease-in-out z-[140]"
        onclick="closeFilterDrawer()"></div>

    <aside id="filterDrawer" 
        class="fixed top-0 right-0 z-[150] h-full w-full max-w-md bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col"
        aria-label="Filter Variant Products Drawer">
        
        <!-- Drawer Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/80">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Filter Variant Products</h2>
                    <p class="text-xs text-gray-500">Refine variant product listing criteria</p>
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
        <form action="{{ route('admin.variants.index') }}" method="GET" id="drawerFilterForm" class="flex-1 flex flex-col overflow-hidden">
            <div class="flex-1 overflow-y-auto p-6 space-y-5">
                <!-- Search Input -->
                <div>
                    <label for="drawer_search" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Search Keyword</label>
                    <div class="relative">
                        <input type="text" name="search" id="drawer_search" value="{{ request('search') }}"
                            placeholder="Name, slug, SKU, or Product ID..."
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
                <a href="{{ route('admin.variants.index') }}" 
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
            }
        });
    </script>
    @endsection
@endsection
