@extends('admin.layouts.app')

@section('title', 'Attribute Values Management')

@section('content')
    @php
        $currentSearch = request('search', '');
        $currentAttr = request('attribute_id', '');
        $currentStatus = request('status', '');
        $hasFilters = filled($currentSearch) || filled($currentAttr) || filled($currentStatus);
    @endphp

    <div class="space-y-6">
        <!-- Single Unified Attribute Values Card (Sharp / Non-rounded) -->
        <div class="bg-white border border-gray-200 shadow-sm">
            
            <!-- Card Top Header -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-1.5">
                            <span class="text-gray-700 font-semibold">Catalog</span>
                            <span class="text-gray-300">/</span>
                            <a href="{{ route('admin.attributes.index') }}" class="hover:text-gray-900 transition">Attributes</a>
                            <span class="text-gray-300">/</span>
                            <span>Attribute Values</span>
                        </nav>

                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Attribute Values</h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                {{ $attributeValues->total() }} Total {{ Str::plural('Value', $attributeValues->total()) }}
                            </span>
                        </div>
                    </div>

                    <!-- Header Action Buttons -->
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($hasFilters)
                            <a href="{{ route('admin.attribute-values.index') }}"
                               class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear Filters
                            </a>
                        @endif

                        <a href="{{ route('admin.attribute-values.create') }}"
                           class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add New Value
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search & Filters Bar -->
            <div class="p-5 sm:p-6 bg-gray-50/50 border-b border-gray-200">
                <form action="{{ route('admin.attribute-values.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                    
                    <!-- Search Input -->
                    <div class="lg:col-span-5">
                        <label for="search" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Search Values</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ $currentSearch }}"
                                   placeholder="Search Value, ID..."
                                   class="w-full pl-9 pr-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        </div>
                    </div>

                    <!-- Attribute Filter -->
                    <div class="lg:col-span-4">
                        <label for="attribute_id" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Parent Attribute</label>
                        <select name="attribute_id" id="attribute_id"
                                class="w-full py-2 px-3 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">All Attributes</option>
                            @foreach($attributes as $attr)
                                <option value="{{ $attr->id }}" {{ $currentAttr == $attr->id ? 'selected' : '' }}>
                                    {{ $attr->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="lg:col-span-2">
                        <label for="status" class="block text-[11px] font-semibold text-gray-600 uppercase tracking-wider mb-1">Status</label>
                        <select name="status" id="status"
                                class="w-full py-2 px-3 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">All</option>
                            <option value="active" {{ $currentStatus == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $currentStatus == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Submit & Reset Buttons -->
                    <div class="lg:col-span-1 flex items-end">
                        <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-3 text-xs transition duration-150 flex items-center justify-center">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Attribute Values Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                        <tr>
                            <th class="px-5 py-3.5">Parent Attribute</th>
                            <th class="px-5 py-3.5">Option Value</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5">Created Date</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($attributeValues as $attributeValue)
                            <tr class="hover:bg-gray-50/80 transition duration-150">
                                
                                <!-- Parent Attribute -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if($attributeValue->attribute)
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                                {{ $attributeValue->attribute->name }}
                                            </span>
                                            @if($attributeValue->attribute->is_image)
                                                <span class="text-[10px] text-amber-700 bg-amber-50 px-1.5 py-0.2 border border-amber-200">🎨 Swatch</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">Unassigned</span>
                                    @endif
                                </td>

                                <!-- Option Value -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="space-y-0.5">
                                        <span class="font-bold text-gray-900 text-xs">
                                            {{ $attributeValue->value }}
                                        </span>
                                        <p class="text-[10px] text-gray-400 font-mono">#{{ $attributeValue->id }}</p>
                                    </div>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if ($attributeValue->status == 'active')
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
                                    {{ $attributeValue->created_at ? $attributeValue->created_at->format('M d, Y · H:i') : 'N/A' }}
                                </td>

                                <!-- Action Buttons -->
                                <td class="px-5 py-4 whitespace-nowrap text-right font-medium">
                                    <a href="{{ route('admin.attribute-values.edit', $attributeValue->id) }}"
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition"
                                       title="Edit Value">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div class="w-12 h-12 bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-base font-semibold text-gray-900">No attribute values found</p>
                                        <p class="text-xs text-gray-500">
                                            @if ($hasFilters)
                                                No values match your search or filter criteria.
                                            @else
                                                Get started by adding attribute options.
                                            @endif
                                        </p>
                                        @if ($hasFilters)
                                            <a href="{{ route('admin.attribute-values.index') }}"
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
            @if ($attributeValues->hasPages())
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    {{ $attributeValues->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection
