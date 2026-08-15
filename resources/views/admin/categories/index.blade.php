@extends('admin.layouts.app')

@section('title', 'Categories Management')

@section('content')
    @php
        $currentSearch = request('search', '');
        $currentStatus = request('status', '');
        $currentFeatured = request('is_featured', '');
        $hasFilters = filled($currentSearch) || filled($currentStatus) || filled($currentFeatured);
    @endphp

    <div class="space-y-6" x-data="{
        copiedText: null,
        copyToClipboard(text, label) {
            navigator.clipboard.writeText(text);
            this.copiedText = label;
            setTimeout(() => this.copiedText = null, 2000);
        }
    }">
        <!-- Single Unified Categories Card (Sharp / Non-rounded) -->
        <div class="bg-white border border-gray-200 shadow-sm">
            
            <!-- Card Top Header -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-1.5">
                            <span class="text-gray-700 font-semibold">Catalog</span>
                            <span class="text-gray-300">/</span>
                            <span>Categories</span>
                        </nav>

                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Categories Management</h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $categories->total() }} Total {{ Str::plural('Category', $categories->total()) }}
                            </span>
                        </div>
                    </div>

                    <!-- Header Action Buttons -->
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($hasFilters)
                            <a href="{{ route('admin.categories.index') }}"
                               class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear Filters
                            </a>
                        @endif

                        <a href="{{ route('admin.categories.create') }}"
                           class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add New Category
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search & Filters Bar -->
            <div class="p-5 sm:p-6 bg-gray-50/50 border-b border-gray-200">
                <form action="{{ route('admin.categories.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                    
                    <!-- Search Input -->
                    <div class="lg:col-span-5">
                        <label for="search" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Search Categories</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ $currentSearch }}"
                                   placeholder="Search Name, Slug, ID..."
                                   class="w-full pl-9 pr-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="lg:col-span-3">
                        <label for="status" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Status</label>
                        <select name="status" id="status"
                                class="w-full py-2 px-3 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">All Statuses</option>
                            <option value="{{ \App\Constants\CategoryStatus::ACTIVE }}" {{ $currentStatus == \App\Constants\CategoryStatus::ACTIVE ? 'selected' : '' }}>Active</option>
                            <option value="{{ \App\Constants\CategoryStatus::INACTIVE }}" {{ $currentStatus == \App\Constants\CategoryStatus::INACTIVE ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Featured Filter -->
                    <div class="lg:col-span-2">
                        <label for="is_featured" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Featured</label>
                        <select name="is_featured" id="is_featured"
                                class="w-full py-2 px-3 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">All</option>
                            <option value="1" {{ $currentFeatured === '1' ? 'selected' : '' }}>Featured ⭐</option>
                            <option value="0" {{ $currentFeatured === '0' ? 'selected' : '' }}>Non-Featured</option>
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

                        @if ($hasFilters)
                            <a href="{{ route('admin.categories.index') }}"
                               class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-2.5 text-xs transition flex items-center justify-center"
                               title="Reset Search">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Categories Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                        <tr>
                            <th class="px-5 py-3.5">Category</th>
                            <th class="px-5 py-3.5">Hierarchy & Parent</th>
                            <th class="px-5 py-3.5 text-center">Featured</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5">Created Date</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($categories as $category)
                            @php
                                $imageUrl = $category->getImageUrl('category_image');
                                $subcategoriesCount = $category->children ? $category->children->count() : 0;
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition duration-150">
                                
                                <!-- Category Image & Name -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center space-x-3 min-w-[220px]">
                                        @if ($imageUrl)
                                            <img src="{{ $imageUrl }}" alt="{{ $category->name }}" class="w-10 h-10 object-cover border border-gray-200 flex-shrink-0">
                                        @else
                                            <div class="w-10 h-10 bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 flex-shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif

                                        <div class="space-y-0.5 min-w-0">
                                            <span class="font-semibold text-gray-900 block truncate" title="{{ $category->name }}">
                                                {{ $category->name }}
                                            </span>
                                            <div class="flex items-center space-x-1.5 text-[11px] text-gray-500">
                                                <span class="font-mono bg-gray-100 px-1 py-0.2 border border-gray-200 truncate max-w-[130px]">
                                                    {{ $category->slug }}
                                                </span>
                                                <button type="button" @click="copyToClipboard('{{ $category->slug }}', '{{ $category->id }}')" 
                                                        class="text-gray-400 hover:text-gray-600 transition" title="Copy Slug">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                </button>
                                                <span>&bull; #{{ $category->id }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Hierarchy & Parent -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        @if ($category->parent)
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                                Parent: {{ $category->parent->name }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                Root Category
                                            </span>
                                        @endif

                                        @if ($subcategoriesCount > 0)
                                            <p class="text-[11px] text-gray-500">
                                                {{ $subcategoriesCount }} Sub-{{ Str::plural('category', $subcategoriesCount) }}
                                            </p>
                                        @endif
                                    </div>
                                </td>

                                <!-- Featured Star Toggle -->
                                <td class="px-5 py-4 whitespace-nowrap text-center">
                                    <button type="button" onclick="toggleFeatured({{ $category->id }}, this)" 
                                            class="p-1 hover:bg-gray-100 transition focus:outline-none"
                                            title="{{ $category->is_featured ? 'Click to Unfeature' : 'Click to Feature' }}">
                                        <span class="featured-icon {{ $category->is_featured ? 'text-amber-400' : 'text-gray-300 hover:text-gray-400' }}">
                                            <svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </span>
                                    </button>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if ($category->status == \App\Constants\CategoryStatus::ACTIVE)
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
                                    {{ $category->created_at ? $category->created_at->format('M d, Y · H:i') : 'N/A' }}
                                </td>

                                <!-- Action Buttons -->
                                <td class="px-5 py-4 whitespace-nowrap text-right font-medium">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}"
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition"
                                       title="Edit Category">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
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
                                        <p class="text-base font-semibold text-gray-900">No categories found</p>
                                        <p class="text-xs text-gray-500">
                                            @if ($hasFilters)
                                                No categories match your search criteria.
                                            @else
                                                Get started by creating your first category.
                                            @endif
                                        </p>
                                        @if ($hasFilters)
                                            <a href="{{ route('admin.categories.index') }}"
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
            @if ($categories->hasPages())
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    {{ $categories->links() }}
                </div>
            @endif

        </div>
    </div>

    @push('scripts')
    <script>
        function toggleFeatured(categoryId, button) {
            const iconContainer = button.querySelector('.featured-icon');
            
            fetch(`{{ url('admin/categories') }}/${categoryId}/toggle-featured`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.is_featured) {
                        iconContainer.classList.remove('text-gray-300', 'hover:text-gray-400');
                        iconContainer.classList.add('text-amber-400');
                        button.title = "Click to Unfeature";
                    } else {
                        iconContainer.classList.remove('text-amber-400');
                        iconContainer.classList.add('text-gray-300', 'hover:text-gray-400');
                        button.title = "Click to Feature";
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Something went wrong!');
                }
            });
        }
    </script>
    @endpush
@endsection
