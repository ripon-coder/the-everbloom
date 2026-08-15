@extends('admin.layouts.app')

@section('title', 'Edit Page')

@section('content')
    <div class="space-y-6" x-data="{
        title: '{{ old('title', $page->title) }}',
        slug: '{{ old('slug', $page->slug) }}',
        generateSlug() {
            this.slug = this.title.toLowerCase()
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
                            <a href="{{ route('admin.pages.index') }}" class="hover:text-gray-900 transition">Custom Pages</a>
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-700 font-semibold truncate max-w-[200px]">{{ $page->title }}</span>
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-500">Edit</span>
                        </nav>
                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Custom Page</h1>
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                #{{ $page->id }}
                            </span>
                        </div>
                    </div>

                    <!-- Header Actions -->
                    <div class="flex items-center space-x-2">
                        <a href="{{ url($page->slug) }}" target="_blank"
                           class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-gray-50 border border-gray-200 hover:bg-gray-100 shadow-sm transition">
                            <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            View on Site
                        </a>
                        <a href="{{ route('admin.pages.index') }}"
                           class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-sm transition">
                            <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Pages
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form Body -->
            <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-5 sm:p-6 space-y-6">
                    
                    <!-- Section 1: General Info -->
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 pb-2 mb-4 border-b border-gray-200">
                            Page Configuration
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Page Title -->
                            <div>
                                <label for="title" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Page Title <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="title" id="title" required
                                       x-model="title"
                                       placeholder="Page title"
                                       class="w-full px-3 py-2 text-xs border @error('title') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                @error('title')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Page Slug -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="slug" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        URL Slug <span class="text-rose-500">*</span>
                                    </label>
                                    <button type="button" @click="generateSlug()" 
                                            class="text-[11px] text-blue-600 hover:text-blue-800 underline">
                                        Regenerate from Title
                                    </button>
                                </div>
                                <input type="text" name="slug" id="slug"
                                       x-model="slug"
                                       placeholder="url-slug"
                                       class="w-full px-3 py-2 text-xs font-mono border @error('slug') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                @error('slug')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Page Body / Rich Text Content -->
                    <div class="pt-4 border-t border-gray-200">
                        <label for="editor" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Page Content <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="content" id="editor" rows="12">{{ old('content', $page->content) }}</textarea>
                        @error('content')
                            <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Section 3: SEO Metadata & Status -->
                    <div class="pt-4 border-t border-gray-200">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 pb-2 mb-4 border-b border-gray-200">
                            Search Engine Optimization (SEO) & Visibility
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="meta_title" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    SEO Meta Title
                                </label>
                                <input type="text" name="meta_title" id="meta_title"
                                       value="{{ old('meta_title', $page->meta_title) }}" placeholder="Custom page title for search engines..."
                                       class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            </div>

                            <div>
                                <label for="meta_description" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    SEO Meta Description
                                </label>
                                <textarea name="meta_description" id="meta_description" rows="2"
                                          placeholder="Brief description for search results snippet..."
                                          class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">{{ old('meta_description', $page->meta_description) }}</textarea>
                            </div>
                        </div>

                        <!-- Published Status Checkbox -->
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="inline-flex items-center space-x-2.5 cursor-pointer">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-0 cursor-pointer"
                                       {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                                <span class="text-xs font-bold text-gray-900">Publish (Visible on storefront)</span>
                            </label>
                        </div>
                    </div>

                </div>

                <!-- Form Footer with Meta & Actions (No Delete Button) -->
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="text-gray-500 space-x-3">
                        <span>Created: {{ $page->created_at ? $page->created_at->format('M d, Y · H:i') : 'N/A' }}</span>
                        <span>&bull;</span>
                        <span>Updated: {{ $page->updated_at ? $page->updated_at->format('M d, Y · H:i') : 'N/A' }}</span>
                    </div>

                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.pages.index') }}"
                           class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 transition">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                            Update Page
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('editor')) {
                CKEDITOR.replace('editor', { height: 350 });
            }
        });
    </script>
    @endpush
@endsection
