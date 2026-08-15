@extends('admin.layouts.app')

@section('title', 'Create Brand')

@section('content')
    <div class="space-y-6" x-data="{
        name: '{{ old('name', '') }}',
        slug: '{{ old('slug', '') }}',
        isManualSlug: false,
        generateSlug() {
            if (!this.isManualSlug) {
                this.slug = this.name.toLowerCase()
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
                            <a href="{{ route('admin.brands.index') }}" class="hover:text-gray-900 transition">Brands</a>
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-700 font-semibold">New Brand</span>
                        </nav>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Create New Brand</h1>
                    </div>

                    <!-- Header Action -->
                    <a href="{{ route('admin.brands.index') }}"
                       class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-sm transition">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Brands
                    </a>
                </div>
            </div>

            <!-- Form Body -->
            <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="p-5 sm:p-6 space-y-6">
                    
                    <!-- General Details Grid -->
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 pb-2 mb-4 border-b border-gray-200">
                            Brand Information
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Brand Name -->
                            <div>
                                <label for="name" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Brand Name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" required
                                       x-model="name" @input="generateSlug()"
                                       placeholder="e.g., Nike, Zara, Apple"
                                       class="w-full px-3 py-2 text-xs border @error('name') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                @error('name')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Brand Slug -->
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
                                <input type="text" name="slug" id="slug" required
                                       x-model="slug" @input="isManualSlug = true"
                                       placeholder="e.g., brand-name"
                                       class="w-full px-3 py-2 text-xs font-mono border @error('slug') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                @error('slug')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Status <span class="text-rose-500">*</span>
                                </label>
                                <select name="status" id="status" required
                                        class="w-full py-2 px-3 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600 cursor-pointer">
                                    <option value="{{ \App\Constants\BrandStatus::ACTIVE }}" {{ old('status') == \App\Constants\BrandStatus::ACTIVE ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="{{ \App\Constants\BrandStatus::INACTIVE }}" {{ old('status') == \App\Constants\BrandStatus::INACTIVE ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>
                            </div>

                            <!-- Logo Upload -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Brand Logo
                                </label>
                                <div class="p-3 border border-dashed border-gray-300 bg-gray-50/50 flex items-center gap-3">
                                    <img id="logo-preview" src="{{ asset('images/default-logo.png') }}" alt="Brand Logo Preview"
                                         class="w-12 h-12 object-contain p-1 border border-gray-300 bg-white flex-shrink-0">
                                    
                                    <div class="space-y-1 text-xs">
                                        <input type="file" name="logo" id="logo" accept="image/*" onchange="previewLogo(this)"
                                               class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2.5 file:border-0 file:text-xs file:font-semibold file:bg-gray-800 file:text-white hover:file:bg-black cursor-pointer">
                                        <p class="text-[10px] text-gray-500">Formats: JPG, PNG, SVG, WEBP. Max size: 2MB.</p>
                                    </div>
                                </div>
                                @error('logo')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="pt-4 border-t border-gray-200">
                        <label for="description" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3"
                                  placeholder="Brief overview or description about the brand..."
                                  class="w-full px-3 py-2 text-xs border @error('description') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Form Footer Actions -->
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.brands.index') }}"
                       class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                        Create Brand
                    </button>
                </div>
            </form>

        </div>
    </div>

    @push('scripts')
    <script>
        function previewLogo(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logo-preview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
@endsection
