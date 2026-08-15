@extends('admin.layouts.app')

@section('title', 'Edit Menu')

@section('content')
    <div class="space-y-6" x-data="{
        name: '{{ old('name', $menu->name) }}',
        slug: '{{ old('slug', $menu->slug) }}',
        generateSlug() {
            this.slug = this.name.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
        }
    }">
        <!-- Single Unified Card (Sharp / Non-rounded) -->
        <div class="bg-white border border-gray-200 shadow-sm">
            
            <!-- Card Header -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-1.5">
                            <a href="{{ route('admin.menus.index') }}" class="hover:text-gray-900 transition">Navigation</a>
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-700 font-semibold truncate max-w-[200px]">{{ $menu->name }}</span>
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-500">Edit</span>
                        </nav>
                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Navigation Menu</h1>
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                #{{ $menu->id }}
                            </span>
                        </div>
                    </div>

                    <!-- Header Action -->
                    <a href="{{ route('admin.menus.index') }}"
                       class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-sm transition">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Menus
                    </a>
                </div>
            </div>

            <!-- Form Body -->
            <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-5 sm:p-6 space-y-6">
                    
                    <!-- Details Section -->
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 pb-2 mb-4 border-b border-gray-200">
                            Menu Item Configuration
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Menu Name -->
                            <div>
                                <label for="name" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Menu Name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" required
                                       x-model="name"
                                       placeholder="Menu name"
                                       class="w-full px-3 py-2 text-xs border @error('name') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                @error('name')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Menu Slug -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="slug" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Slug <span class="text-rose-500">*</span>
                                    </label>
                                    <button type="button" @click="generateSlug()" 
                                            class="text-[11px] text-blue-600 hover:text-blue-800 underline">
                                        Regenerate from Name
                                    </button>
                                </div>
                                <input type="text" name="slug" id="slug"
                                       x-model="slug"
                                       placeholder="menu-slug"
                                       class="w-full px-3 py-2 text-xs font-mono border @error('slug') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                @error('slug')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Target Link / URL -->
                            <div>
                                <label for="url" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Target Link / URL
                                </label>
                                <input type="text" name="url" id="url"
                                       value="{{ old('url', $menu->url) }}" placeholder="e.g., /shop, /categories/electronics, or https://..."
                                       class="w-full px-3 py-2 text-xs font-mono border @error('url') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                <p class="mt-1 text-[10px] text-gray-500">Leave empty to use the slug as route.</p>
                            </div>

                            <!-- Display Order -->
                            <div>
                                <label for="order" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Display Order Sequence
                                </label>
                                <input type="number" name="order" id="order"
                                       value="{{ old('order', $menu->order) }}" min="0"
                                       class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            </div>
                        </div>

                        <!-- Status Checkbox -->
                        <div class="mt-5 pt-4 border-t border-gray-100">
                            <label class="inline-flex items-center space-x-2.5 cursor-pointer">
                                <input type="checkbox" name="status" id="status" value="1"
                                       class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-0 cursor-pointer"
                                       {{ old('status', $menu->status) ? 'checked' : '' }}>
                                <span class="text-xs font-bold text-gray-900">Active (Visible in storefront menus)</span>
                            </label>
                        </div>
                    </div>

                </div>

                <!-- Form Footer with Meta & Actions (No Delete Button) -->
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="text-gray-500 space-x-3">
                        <span>Created: {{ $menu->created_at ? $menu->created_at->format('M d, Y · H:i') : 'N/A' }}</span>
                        <span>&bull;</span>
                        <span>Updated: {{ $menu->updated_at ? $menu->updated_at->format('M d, Y · H:i') : 'N/A' }}</span>
                    </div>

                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.menus.index') }}"
                           class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 transition">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                            Update Menu
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection
