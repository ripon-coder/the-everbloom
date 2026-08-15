@extends('admin.layouts.app')

@section('title', 'Product Reviews')

@section('content')
    @php
        $currentSearch = request('search', '');
        $currentRating = request('rating', '');
        $currentStatus = request('status', '');
        $hasFilters = filled($currentSearch) || filled($currentRating) || filled($currentStatus);
    @endphp

    <div class="space-y-6">
        <!-- Single Unified Reviews Card (Sharp / Non-rounded) -->
        <div class="bg-white border border-gray-200 shadow-sm">
            
            <!-- Card Top Header -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-1.5">
                            <span class="text-gray-700 font-semibold">Feedback</span>
                            <span class="text-gray-300">/</span>
                            <span>Product Reviews</span>
                        </nav>

                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Product Reviews</h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                {{ $reviews->total() }} Total {{ Str::plural('Review', $reviews->total()) }}
                            </span>
                        </div>
                    </div>

                    <!-- Header Action Buttons -->
                    @if ($hasFilters)
                        <div>
                            <a href="{{ route('admin.reviews.index') }}"
                               class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear Filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Search & Filters Bar -->
            <div class="p-5 sm:p-6 bg-gray-50/50 border-b border-gray-200">
                <form action="{{ route('admin.reviews.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                    
                    <!-- Search Input -->
                    <div class="lg:col-span-5">
                        <label for="search" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Search Reviews</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ $currentSearch }}"
                                   placeholder="Search Feedback, Customer, Product, ID..."
                                   class="w-full pl-9 pr-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        </div>
                    </div>

                    <!-- Rating Filter -->
                    <div class="lg:col-span-3">
                        <label for="rating" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Rating</label>
                        <select name="rating" id="rating"
                                class="w-full py-2 px-3 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">All Ratings</option>
                            <option value="5" {{ $currentRating == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ 5 Stars</option>
                            <option value="4" {{ $currentRating == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ 4 Stars</option>
                            <option value="3" {{ $currentRating == '3' ? 'selected' : '' }}>⭐⭐⭐ 3 Stars</option>
                            <option value="2" {{ $currentRating == '2' ? 'selected' : '' }}>⭐⭐ 2 Stars</option>
                            <option value="1" {{ $currentRating == '1' ? 'selected' : '' }}>⭐ 1 Star</option>
                        </select>
                    </div>

                    <!-- Approval Status Filter -->
                    <div class="lg:col-span-2">
                        <label for="status" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Status</label>
                        <select name="status" id="status"
                                class="w-full py-2 px-3 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">All Statuses</option>
                            <option value="approved" {{ $currentStatus == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="pending" {{ $currentStatus == 'pending' ? 'selected' : '' }}>Pending</option>
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
                            <a href="{{ route('admin.reviews.index') }}"
                               class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-2.5 text-xs transition flex items-center justify-center"
                               title="Reset Search">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Reviews Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                        <tr>
                            <th class="px-5 py-3.5">Product</th>
                            <th class="px-5 py-3.5">Reviewer</th>
                            <th class="px-5 py-3.5">Rating</th>
                            <th class="px-5 py-3.5">Review Feedback</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5">Created Date</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($reviews as $review)
                            <tr class="hover:bg-gray-50/80 transition duration-150">
                                
                                <!-- Product -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="space-y-0.5">
                                        @if($review->product)
                                            <a href="{{ route('admin.products.show', $review->product->id) }}" 
                                               class="font-semibold text-gray-900 hover:text-blue-600 transition block truncate max-w-[200px]"
                                               title="{{ $review->product->name }}">
                                                {{ $review->product->name }}
                                            </a>
                                            <span class="text-[10px] text-gray-400 font-mono">#{{ $review->product->id }}</span>
                                        @else
                                            <span class="text-gray-400">Product removed</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Reviewer User -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="space-y-0.5">
                                        <p class="font-semibold text-gray-900">{{ $review->user?->name ?: 'Anonymous' }}</p>
                                        <p class="text-[11px] text-gray-500">{{ $review->user?->email ?: '—' }}</p>
                                    </div>
                                </td>

                                <!-- Rating Stars -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-1">
                                        <div class="flex text-amber-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="font-bold text-gray-700 text-xs ml-1">{{ $review->rating }}/5</span>
                                    </div>
                                </td>

                                <!-- Feedback Text -->
                                <td class="px-5 py-4">
                                    <div class="text-xs text-gray-700 max-w-sm" title="{{ $review->review }}">
                                        {{ $review->review ?: 'No comment written.' }}
                                    </div>
                                </td>

                                <!-- Approval Status Badge -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if ($review->is_approved)
                                        <span class="inline-flex px-2.5 py-0.5 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Approved
                                        </span>
                                    @else
                                        <span class="inline-flex px-2.5 py-0.5 text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                            Pending Approval
                                        </span>
                                    @endif
                                </td>

                                <!-- Created Date -->
                                <td class="px-5 py-4 whitespace-nowrap text-gray-500 text-[11px]">
                                    {{ $review->created_at ? $review->created_at->format('M d, Y · H:i') : 'N/A' }}
                                </td>

                                <!-- Actions (Toggle Approval, no delete button) -->
                                <td class="px-5 py-4 whitespace-nowrap text-right font-medium">
                                    <form action="{{ route('admin.reviews.toggle-approval', $review) }}" method="POST" class="inline">
                                        @csrf
                                        @if($review->is_approved)
                                            <button type="submit" 
                                                    class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition"
                                                    title="Unapprove review">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Unapprove
                                            </button>
                                        @else
                                            <button type="submit" 
                                                    class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition"
                                                    title="Approve review">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Approve
                                            </button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div class="w-12 h-12 bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-base font-semibold text-gray-900">No reviews found</p>
                                        <p class="text-xs text-gray-500">
                                            @if ($hasFilters)
                                                No customer feedback matches your search criteria.
                                            @else
                                                Product reviews submitted by customers will be displayed here.
                                            @endif
                                        </p>
                                        @if ($hasFilters)
                                            <a href="{{ route('admin.reviews.index') }}"
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
            @if ($reviews->hasPages())
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    {{ $reviews->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection
