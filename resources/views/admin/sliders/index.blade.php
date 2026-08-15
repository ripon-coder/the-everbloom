@extends('admin.layouts.app')

@section('title', 'Hero Sliders Management')

@section('content')
    @php
        $currentSearch = request('search', '');
        $currentStatus = request('status', '');
        $hasFilters = filled($currentSearch) || filled($currentStatus);
    @endphp

    <div class="space-y-6">
        <!-- Single Unified Sliders Card (Sharp / Non-rounded) -->
        <div class="bg-white border border-gray-200 shadow-sm">
            
            <!-- Card Top Header -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-1.5">
                            <span class="text-gray-700 font-semibold">Storefront</span>
                            <span class="text-gray-300">/</span>
                            <span>Hero Sliders</span>
                        </nav>

                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Hero Sliders</h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $sliders->total() }} Total {{ Str::plural('Slider', $sliders->total()) }}
                            </span>
                        </div>
                    </div>

                    <!-- Header Action Buttons -->
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($hasFilters)
                            <a href="{{ route('admin.sliders.index') }}"
                               class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear Filters
                            </a>
                        @endif

                        <a href="{{ route('admin.sliders.create') }}"
                           class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add New Slider
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search & Filters Bar -->
            <div class="p-5 sm:p-6 bg-gray-50/50 border-b border-gray-200">
                <form action="{{ route('admin.sliders.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                    
                    <!-- Search Input -->
                    <div class="lg:col-span-7">
                        <label for="search" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Search Sliders</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ $currentSearch }}"
                                   placeholder="Search Title, Subtitle, Button Text, ID..."
                                   class="w-full pl-9 pr-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="lg:col-span-3">
                        <label for="status" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Status</label>
                        <select name="status" id="status"
                                class="w-full py-2 px-3 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">All Sliders</option>
                            <option value="1" {{ $currentStatus === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $currentStatus === '0' ? 'selected' : '' }}>Inactive</option>
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
                            <a href="{{ route('admin.sliders.index') }}"
                               class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-2.5 text-xs transition flex items-center justify-center"
                               title="Reset Search">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Sliders Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                        <tr>
                            <th class="px-5 py-3.5">Order</th>
                            <th class="px-5 py-3.5">Banner Image</th>
                            <th class="px-5 py-3.5">Headline & Subtitle</th>
                            <th class="px-5 py-3.5">Call to Action</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($sliders as $slider)
                            <tr class="hover:bg-gray-50/80 transition duration-150">
                                
                                <!-- Sort Order -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-100 border border-gray-300 text-gray-700 font-mono font-bold text-xs">
                                        {{ $slider->sort_order }}
                                    </span>
                                </td>

                                <!-- Banner Image -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="w-32 h-14 bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center">
                                        @if($slider->getImageUrl())
                                            <img src="{{ $slider->getImageUrl() }}" alt="{{ $slider->title }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-[10px] text-gray-400">No Image</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Title & Subtitle -->
                                <td class="px-5 py-4">
                                    <div class="space-y-0.5 max-w-sm">
                                        <a href="{{ route('admin.sliders.edit', $slider->id) }}" 
                                           class="font-bold text-gray-900 hover:text-blue-600 transition block text-xs truncate">
                                            {{ $slider->title ?: '(Untitled Banner)' }}
                                        </a>
                                        @if($slider->subtitle)
                                            <p class="text-[11px] text-gray-500 line-clamp-1">{{ $slider->subtitle }}</p>
                                        @endif
                                        <span class="text-[10px] text-gray-400 font-mono">#{{ $slider->id }}</span>
                                    </div>
                                </td>

                                <!-- CTA Button & Link -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if($slider->btn_text || $slider->btn_link)
                                        <div class="space-y-1">
                                            @if($slider->btn_text)
                                                <span class="inline-flex px-2 py-0.5 text-[11px] font-semibold bg-gray-800 text-white border border-gray-900">
                                                    {{ $slider->btn_text }}
                                                </span>
                                            @endif
                                            @if($slider->btn_link)
                                                <p class="text-[10px] text-gray-500 font-mono truncate max-w-[140px]">{{ $slider->btn_link }}</p>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-[11px]">No CTA button</span>
                                    @endif
                                </td>

                                <!-- Status Badge -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <form action="{{ route('admin.sliders.toggle-status', $slider->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex px-2.5 py-0.5 text-xs font-semibold border transition {{ $slider->status ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200' }}"
                                                title="Click to toggle banner visibility">
                                            {{ $slider->status ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>

                                <!-- Action Buttons (Edit button only, no delete button) -->
                                <td class="px-5 py-4 whitespace-nowrap text-right font-medium">
                                    <a href="{{ route('admin.sliders.edit', $slider->id) }}"
                                       class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition"
                                       title="Edit Slider">
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
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-base font-semibold text-gray-900">No hero sliders found</p>
                                        <p class="text-xs text-gray-500">
                                            @if ($hasFilters)
                                                No banners match your search criteria.
                                            @else
                                                Add promotional sliders to display on your homepage banner carousel.
                                            @endif
                                        </p>
                                        @if ($hasFilters)
                                            <a href="{{ route('admin.sliders.index') }}"
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
            @if ($sliders->hasPages())
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    {{ $sliders->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection
