@extends('admin.layouts.app')

@section('title', 'Create Page')

@section('content')
    <div class="space-y-6" x-data="{
        title: '{{ old('title', '') }}',
        slug: '{{ old('slug', '') }}',
        isManualSlug: false,
        onTemplateChange(e) {
            const val = e.target.value;
            const titleMap = {
                'about_us': 'About Us',
                'contact_us': 'Contact Us',
                'privacy_policy': 'Privacy Policy',
                'terms_conditions': 'Terms & Conditions',
                'return_policy': 'Return Policy',
                'shipping_policy': 'Shipping Policy',
                'faq': 'Frequently Asked Questions (FAQs)'
            };
            if (val && titleMap[val]) {
                this.title = titleMap[val];
                this.slug = val.replace(/_/g, '-');
                this.isManualSlug = true;
            }
        },
        generateSlug() {
            if (!this.isManualSlug) {
                this.slug = this.title.toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim();
            }
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
                            <span class="text-gray-700 font-semibold">New Page</span>
                        </nav>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Create Custom Page</h1>
                    </div>

                    <!-- Header Action -->
                    <a href="{{ route('admin.pages.index') }}"
                       class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-sm transition">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Pages
                    </a>
                </div>
            </div>

            <!-- Form Body -->
            <form action="{{ route('admin.pages.store') }}" method="POST">
                @csrf

                <div class="p-5 sm:p-6 space-y-6">
                    
                    <!-- Section 1: Template & General Info -->
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-2 mb-4 border-b border-gray-200 gap-2">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700">
                                Page Configuration
                            </h2>
                            
                            <!-- Template Preset Dropdown -->
                            <div class="flex items-center space-x-2">
                                <span class="text-[11px] text-gray-500 font-medium">Quick Template:</span>
                                <select @change="onTemplateChange($event)"
                                        class="py-1 px-2.5 text-xs border border-gray-300 bg-blue-50/50 text-gray-800 font-medium focus:ring-1 focus:ring-blue-600 cursor-pointer">
                                    <option value="">-- Custom Page --</option>
                                    <option value="about_us">About Us</option>
                                    <option value="contact_us">Contact Us</option>
                                    <option value="privacy_policy">Privacy Policy</option>
                                    <option value="terms_conditions">Terms & Conditions</option>
                                    <option value="return_policy">Return Policy</option>
                                    <option value="shipping_policy">Shipping Policy</option>
                                    <option value="faq">FAQs</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Page Title -->
                            <div>
                                <label for="title" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Page Title <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="title" id="title" required
                                       x-model="title" @input="generateSlug()"
                                       placeholder="e.g., About Us, Privacy Policy"
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
                                    <button type="button" @click="isManualSlug = false; generateSlug()" 
                                            class="text-[11px] text-blue-600 hover:text-blue-800 underline">
                                        Auto Generate
                                    </button>
                                </div>
                                <input type="text" name="slug" id="slug"
                                       x-model="slug" @input="isManualSlug = true"
                                       placeholder="e.g., about-us"
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
                        <textarea name="content" id="editor" rows="12">{{ old('content') }}</textarea>
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
                                       value="{{ old('meta_title') }}" placeholder="Custom page title for search engines..."
                                       class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            </div>

                            <div>
                                <label for="meta_description" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    SEO Meta Description
                                </label>
                                <textarea name="meta_description" id="meta_description" rows="2"
                                          placeholder="Brief description for search results snippet..."
                                          class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">{{ old('meta_description') }}</textarea>
                            </div>
                        </div>

                        <!-- Published Status Checkbox -->
                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <label class="inline-flex items-center space-x-2.5 cursor-pointer">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-0 cursor-pointer"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <span class="text-xs font-bold text-gray-900">Publish Immediately (Visible on storefront)</span>
                            </label>
                        </div>
                    </div>

                </div>

                <!-- Form Footer Actions -->
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.pages.index') }}"
                       class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                        Save Page
                    </button>
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
