@extends('admin.layouts.app')

@section('title', 'Edit Hero Slider')

@section('content')
    <div class="space-y-6">
        <!-- Single Unified Card (Sharp / Non-rounded) -->
        <div class="bg-white border border-gray-200 shadow-sm">
            
            <!-- Card Header -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-1.5">
                            <a href="{{ route('admin.sliders.index') }}" class="hover:text-gray-900 transition">Hero Sliders</a>
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-700 font-semibold truncate max-w-[200px]">{{ $slider->title ?: 'Slider #' . $slider->id }}</span>
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-500">Edit</span>
                        </nav>
                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Hero Slider</h1>
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                #{{ $slider->id }}
                            </span>
                        </div>
                    </div>

                    <!-- Header Action -->
                    <a href="{{ route('admin.sliders.index') }}"
                       class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-sm transition">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Sliders
                    </a>
                </div>
            </div>

            <!-- Form Body -->
            <form action="{{ route('admin.sliders.update', $slider->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="p-5 sm:p-6 space-y-6">
                    
                    <!-- Section 1: Banner Image -->
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 pb-2 mb-4 border-b border-gray-200">
                            Slider Image & Banner Asset
                        </h2>

                        <div class="p-4 border border-dashed border-gray-300 bg-gray-50/50 flex flex-col md:flex-row items-start gap-5">
                            <div class="w-full md:w-64 h-28 bg-white border border-gray-300 flex items-center justify-center overflow-hidden flex-shrink-0">
                                <img id="image-preview" src="{{ $slider->getImageUrl() ?: asset('images/default-logo.png') }}" alt="Slider Banner Preview"
                                     class="w-full h-full object-cover">
                            </div>

                            <div class="space-y-2 text-xs flex-1">
                                <label for="image" class="block font-semibold text-gray-800">
                                    Upload Replacement Banner (Recommended: 1920x800 px)
                                </label>
                                <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(this)"
                                       class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:border-0 file:text-xs file:font-semibold file:bg-gray-800 file:text-white hover:file:bg-black cursor-pointer">
                                <p class="text-[11px] text-gray-500">Leave empty to keep the existing banner. Allowed formats: JPG, PNG, WEBP. Max: 2MB.</p>
                                @error('image')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Headline & Content -->
                    <div class="pt-4 border-t border-gray-200">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 pb-2 mb-4 border-b border-gray-200">
                            Headlines & Call to Action
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Main Title -->
                            <div>
                                <label for="title" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Main Headline Title
                                </label>
                                <input type="text" name="title" id="title"
                                       value="{{ old('title', $slider->title) }}" placeholder="e.g., Summer Collection 2026"
                                       class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            </div>

                            <!-- Sort Order -->
                            <div>
                                <label for="sort_order" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Display Order Sequence
                                </label>
                                <input type="number" name="sort_order" id="sort_order"
                                       value="{{ old('sort_order', $slider->sort_order) }}" min="0"
                                       class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            </div>

                            <!-- Button Text -->
                            <div>
                                <label for="btn_text" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Call-To-Action (CTA) Button Text
                                </label>
                                <input type="text" name="btn_text" id="btn_text"
                                       value="{{ old('btn_text', $slider->btn_text) }}" placeholder="e.g., Shop Now, Discover More"
                                       class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            </div>

                            <!-- Button Link -->
                            <div>
                                <label for="btn_link" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    CTA Button Target URL
                                </label>
                                <input type="text" name="btn_link" id="btn_link"
                                       value="{{ old('btn_link', $slider->btn_link) }}" placeholder="e.g., /shop, /categories/summer"
                                       class="w-full px-3 py-2 text-xs font-mono border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            </div>
                        </div>

                        <!-- Subtitle / Description -->
                        <div class="mt-5">
                            <label for="subtitle" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Subtitle / Secondary Description
                            </label>
                            <textarea name="subtitle" id="subtitle" rows="3"
                                      placeholder="Catchy secondary line or promotion description..."
                                      class="w-full px-3 py-2 text-xs border border-gray-300 bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">{{ old('subtitle', $slider->subtitle) }}</textarea>
                        </div>

                        <!-- Status Checkbox -->
                        <div class="mt-5 pt-4 border-t border-gray-100">
                            <label class="inline-flex items-center space-x-2.5 cursor-pointer">
                                <input type="checkbox" name="status" id="status" value="1"
                                       class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-0 cursor-pointer"
                                       {{ old('status', $slider->status) ? 'checked' : '' }}>
                                <span class="text-xs font-bold text-gray-900">Active (Visible on homepage carousel)</span>
                            </label>
                        </div>
                    </div>

                </div>

                <!-- Form Footer with Meta & Actions (No Delete Button) -->
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="text-gray-500 space-x-3">
                        <span>Created: {{ $slider->created_at ? $slider->created_at->format('M d, Y · H:i') : 'N/A' }}</span>
                        <span>&bull;</span>
                        <span>Updated: {{ $slider->updated_at ? $slider->updated_at->format('M d, Y · H:i') : 'N/A' }}</span>
                    </div>

                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.sliders.index') }}"
                           class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 transition">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                            Update Slider
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    @push('scripts')
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('image-preview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
@endsection
