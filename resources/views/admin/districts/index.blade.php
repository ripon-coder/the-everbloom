@extends('admin.layouts.app')

@section('title', 'Delivery Districts Management')

@section('content')
    @php
        $currentSearch = request('search', '');
        $hasFilters = filled($currentSearch);
    @endphp

    <div class="space-y-6">
        <!-- Single Unified Districts Card (Sharp / Non-rounded) -->
        <div class="bg-white border border-gray-200 shadow-sm">
            
            <!-- Card Top Header -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-1.5">
                            <span class="text-gray-700 font-semibold">Shipping & Logistics</span>
                            <span class="text-gray-300">/</span>
                            <span>Delivery Districts</span>
                        </nav>

                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Delivery Districts</h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $districts->total() }} Total {{ Str::plural('District', $districts->total()) }}
                            </span>
                        </div>
                    </div>

                    <!-- Header Action Buttons -->
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($hasFilters)
                            <a href="{{ route('admin.district.index') }}"
                               class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear Filters
                            </a>
                        @endif

                        <a href="{{ route('admin.district.create') }}"
                           class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add New District
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="p-5 sm:p-6 bg-gray-50/50 border-b border-gray-200">
                <form action="{{ route('admin.district.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    
                    <!-- Search Input -->
                    <div class="sm:col-span-10">
                        <label for="search" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Search Districts</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ $currentSearch }}"
                                   placeholder="Search District Name, Delivery Charge, Note, ID..."
                                   class="w-full pl-9 pr-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        </div>
                    </div>

                    <!-- Submit & Reset Buttons -->
                    <div class="sm:col-span-2 flex items-end gap-2">
                        <button type="submit"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-3 text-xs transition duration-150 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Search
                        </button>

                        @if ($hasFilters)
                            <a href="{{ route('admin.district.index') }}"
                               class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-2.5 text-xs transition flex items-center justify-center"
                               title="Reset Search">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Districts Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                        <tr>
                            <th class="px-5 py-3.5">ID</th>
                            <th class="px-5 py-3.5">District Name</th>
                            <th class="px-5 py-3.5">Delivery Charge</th>
                            <th class="px-5 py-3.5">Special Delivery Note</th>
                            <th class="px-5 py-3.5">Created Date</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($districts as $district)
                            <tr class="hover:bg-gray-50/80 transition duration-150">
                                
                                <!-- ID -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="font-mono text-gray-500 text-xs">#{{ $district->id }}</span>
                                </td>

                                <!-- District Name -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <a href="{{ route('admin.district.edit', $district->id) }}" 
                                       class="font-bold text-gray-900 hover:text-blue-600 transition block text-xs">
                                        {{ $district->name }}
                                    </a>
                                </td>

                                <!-- Delivery Charge -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono">
                                        {{ $currency_sign ?? '$' }}{{ number_format($district->delivery_charge, 2) }}
                                    </span>
                                </td>

                                <!-- Information Note -->
                                <td class="px-5 py-4">
                                    <span class="text-xs text-gray-600 truncate block max-w-sm" title="{{ $district->information }}">
                                        {{ $district->information ?: '—' }}
                                    </span>
                                </td>

                                <!-- Created Date -->
                                <td class="px-5 py-4 whitespace-nowrap text-gray-500 text-[11px]">
                                    {{ $district->created_at ? $district->created_at->format('M d, Y · H:i') : 'N/A' }}
                                </td>

                                <!-- Actions (Edit button only, no delete button) -->
                                <td class="px-5 py-4 whitespace-nowrap text-right font-medium">
                                    <a href="{{ route('admin.district.edit', $district->id) }}"
                                       class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition"
                                       title="Edit District">
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
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-base font-semibold text-gray-900">No delivery districts found</p>
                                        <p class="text-xs text-gray-500">
                                            @if ($hasFilters)
                                                No districts match your search criteria.
                                            @else
                                                Add delivery zones and their shipping fee rates.
                                            @endif
                                        </p>
                                        @if ($hasFilters)
                                            <a href="{{ route('admin.district.index') }}"
                                               class="text-xs font-semibold text-blue-600 hover:text-blue-700 underline">
                                                Clear search filters
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
            @if ($districts->hasPages())
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    {{ $districts->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection
