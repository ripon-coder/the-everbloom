@extends('admin.layouts.app')

@section('title', 'Variant Products Management')

@section('content')
    @php
        $activeFiltersCount = count(array_filter(request()->only(['search', 'category_id', 'brand_id', 'status', 'is_featured', 'sort_by']), function($v, $k) {
            if ($k === 'sort_by') return $v && $v !== 'latest';
            return $v !== null && $v !== '';
        }, ARRAY_FILTER_USE_BOTH));
    @endphp

    <div class="space-y-6" x-data="{
        copiedText: null,
        copyToClipboard(text, label) {
            navigator.clipboard.writeText(text);
            this.copiedText = label;
            setTimeout(() => this.copiedText = null, 2000);
        }
    }">
        <!-- Single Unified Variant Products Card (Sharp / Non-rounded) -->
        <div class="bg-white border border-gray-200 shadow-sm">
            
            <!-- Card Top Header -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-1.5">
                            <span class="text-gray-700 font-semibold">Catalog</span>
                            <span class="text-gray-300">/</span>
                            <span>Variant Products</span>
                        </nav>

                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Variant Products</h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                {{ $products->total() }} Total {{ Str::plural('Product', $products->total()) }}
                            </span>
                        </div>
                    </div>

                    <!-- Header Action Buttons -->
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($activeFiltersCount > 0)
                            <a href="{{ route('admin.variants.index') }}"
                               class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear Filters
                            </a>
                        @endif

                        <a href="{{ route('admin.products.create') }}"
                           class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create Product
                        </a>
                    </div>
                </div>
            </div>

            <!-- Integrated Search & Filters Bar -->
            <div class="p-5 sm:p-6 bg-gray-50/50 border-b border-gray-200">
                <form action="{{ route('admin.variants.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-12 gap-3">
                    
                    <!-- Search Input -->
                    <div class="lg:col-span-4">
                        <label for="search" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Search Products</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Name, slug, SKU, ID..."
                                   class="w-full pl-9 pr-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="lg:col-span-2">
                        <label for="category_id" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Category</label>
                        <select name="category_id" id="category_id"
                                class="w-full py-2 px-3 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Brand Filter -->
                    <div class="lg:col-span-2">
                        <label for="brand_id" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Brand</label>
                        <select name="brand_id" id="brand_id"
                                class="w-full py-2 px-3 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="lg:col-span-2">
                        <label for="status" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Status</label>
                        <select name="status" id="status"
                                class="w-full py-2 px-3 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Submit & Reset Buttons -->
                    <div class="lg:col-span-2 flex items-end gap-2">
                        <button type="submit"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-3 text-xs transition duration-150 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filter
                        </button>

                        @if ($activeFiltersCount > 0)
                            <a href="{{ route('admin.variants.index') }}"
                               class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-2.5 text-xs transition flex items-center justify-center"
                               title="Reset Filters">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Applied Filter Badges Strip (if any) -->
                @if ($activeFiltersCount > 0)
                    <div class="flex flex-wrap items-center gap-1.5 pt-3 mt-3 border-t border-gray-200/70 text-xs">
                        <span class="text-[11px] font-bold text-gray-500 mr-1 uppercase">Active:</span>

                        @if(request('search'))
                            <span class="inline-flex items-center px-2 py-0.5 bg-blue-50 text-blue-800 border border-blue-200 text-[11px]">
                                Search: "{{ request('search') }}"
                                <a href="{{ route('admin.variants.index', request()->except('search')) }}" class="ml-1 text-blue-600 hover:text-blue-900 font-bold">&times;</a>
                            </span>
                        @endif

                        @if(request('category_id') && $cat = $categories->firstWhere('id', request('category_id')))
                            <span class="inline-flex items-center px-2 py-0.5 bg-indigo-50 text-indigo-800 border border-indigo-200 text-[11px]">
                                Category: {{ $cat->name }}
                                <a href="{{ route('admin.variants.index', request()->except('category_id')) }}" class="ml-1 text-indigo-600 hover:text-indigo-900 font-bold">&times;</a>
                            </span>
                        @endif

                        @if(request('brand_id') && $brd = $brands->firstWhere('id', request('brand_id')))
                            <span class="inline-flex items-center px-2 py-0.5 bg-purple-50 text-purple-800 border border-purple-200 text-[11px]">
                                Brand: {{ $brd->name }}
                                <a href="{{ route('admin.variants.index', request()->except('brand_id')) }}" class="ml-1 text-purple-600 hover:text-purple-900 font-bold">&times;</a>
                            </span>
                        @endif

                        @if(request('status'))
                            <span class="inline-flex items-center px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px]">
                                Status: {{ ucfirst(request('status')) }}
                                <a href="{{ route('admin.variants.index', request()->except('status')) }}" class="ml-1 text-emerald-600 hover:text-emerald-900 font-bold">&times;</a>
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Variant Products Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                        <tr>
                            <th class="px-5 py-3.5">Product</th>
                            <th class="px-5 py-3.5">Category / Brand</th>
                            <th class="px-5 py-3.5">Variants Configured</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5">Created Date</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($products as $product)
                            @php
                                $imgUrl = $product->firstImage?->getImageUrl() ?? $product->anyImage?->getImageUrl();
                                $variantsCount = $product->variants ? $product->variants->count() : 0;
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition duration-150">
                                
                                <!-- Product Image & Name -->
                                <td class="px-5 py-4">
                                    <div class="flex items-start space-x-3 min-w-[220px]">
                                        @if ($imgUrl)
                                            <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="w-11 h-11 object-cover border border-gray-200 flex-shrink-0">
                                        @else
                                            <div class="w-11 h-11 bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 flex-shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif

                                        <div class="space-y-0.5 min-w-0">
                                            <a href="{{ route('admin.variants.show', $product->id) }}" 
                                               class="font-semibold text-gray-900 hover:text-blue-600 transition block truncate"
                                               title="{{ $product->name }}">
                                                {{ $product->name }}
                                            </a>
                                            <div class="flex items-center space-x-1.5 text-[11px] text-gray-500">
                                                <span class="font-mono bg-gray-100 px-1 py-0.2 border border-gray-200 truncate max-w-[130px]">
                                                    {{ $product->slug }}
                                                </span>
                                                <button type="button" @click="copyToClipboard('{{ $product->slug }}', '{{ $product->id }}')" 
                                                        class="text-gray-400 hover:text-gray-600 transition" title="Copy Slug">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                </button>
                                                <span>&bull; #{{ $product->id }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category & Brand -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <p class="font-semibold text-gray-900">{{ $product->category?->name ?: 'Uncategorized' }}</p>
                                        <p class="text-[11px] text-gray-500">{{ $product->brand?->name ?: 'No Brand' }}</p>
                                    </div>
                                </td>

                                <!-- Variants Count & Manage link -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                            {{ $variantsCount }} {{ Str::plural('Variant', $variantsCount) }}
                                        </span>
                                        <a href="{{ route('admin.variants.show', $product->id) }}"
                                           class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 underline transition">
                                            Manage Variants &rarr;
                                        </a>
                                    </div>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if ($product->status === 'active')
                                        <span class="inline-flex px-2.5 py-0.5 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex px-2.5 py-0.5 text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <!-- Created Date -->
                                <td class="px-5 py-4 whitespace-nowrap text-gray-500 text-[11px]">
                                    {{ $product->created_at ? $product->created_at->format('M d, Y') : 'N/A' }}
                                </td>

                                <!-- Action Buttons -->
                                <td class="px-5 py-4 whitespace-nowrap text-right font-medium">
                                    <a href="{{ route('admin.variants.show', $product->id) }}"
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition"
                                       title="Manage Variants">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Manage
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div class="w-12 h-12 bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                        </div>
                                        <p class="text-base font-semibold text-gray-900">No variant products found</p>
                                        <p class="text-xs text-gray-500">
                                            @if ($activeFiltersCount > 0)
                                                No products match your search or filter criteria.
                                            @else
                                                Create products with variant options to manage them here.
                                            @endif
                                        </p>
                                        @if ($activeFiltersCount > 0)
                                            <a href="{{ route('admin.variants.index') }}"
                                               class="text-xs font-semibold text-blue-600 hover:text-blue-700 underline">
                                                Clear all search filters
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Card Pagination -->
            @if ($products->hasPages())
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    {{ $products->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection
