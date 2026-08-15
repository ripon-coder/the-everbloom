@extends('admin.layouts.app')

@section('title', 'Create Attribute')

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
                            <a href="{{ route('admin.attributes.index') }}" class="hover:text-gray-900 transition">Attributes</a>
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-700 font-semibold">New Attribute</span>
                        </nav>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Create New Attribute</h1>
                    </div>

                    <!-- Header Action -->
                    <a href="{{ route('admin.attributes.index') }}"
                       class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-sm transition">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Attributes
                    </a>
                </div>
            </div>

            <!-- Form Body -->
            <form action="{{ route('admin.attributes.store') }}" method="POST">
                @csrf

                <div class="p-5 sm:p-6 space-y-6">
                    
                    <!-- General Details Grid -->
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 pb-2 mb-4 border-b border-gray-200">
                            Attribute Configuration
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Attribute Name -->
                            <div>
                                <label for="name" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Attribute Name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" required
                                       value="{{ old('name') }}"
                                       placeholder="e.g., Size, Color, Material, Volume"
                                       class="w-full px-3 py-2 text-xs border @error('name') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                @error('name')
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
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Type & Description Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-4 border-t border-gray-200">
                        <!-- Is Image Attribute Checkbox -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Attribute Type
                            </label>
                            
                            <input type="hidden" name="is_image" value="0">
                            <label class="p-3 border border-gray-200 bg-gray-50/50 flex items-start space-x-3 cursor-pointer">
                                <input type="checkbox" name="is_image" id="is_image" value="1"
                                       {{ old('is_image') ? 'checked' : '' }}
                                       class="mt-0.5 w-4 h-4 text-blue-600 border-gray-300 focus:ring-0 cursor-pointer">
                                <div>
                                    <span class="text-xs font-semibold text-gray-900 block">🎨 Represents Visual / Image Swatch</span>
                                    <span class="text-[11px] text-gray-500 block mt-0.5">Check if this attribute requires image thumbnails or color swatches instead of pure text.</span>
                                </div>
                            </label>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="3"
                                      placeholder="Brief note or guidance for this attribute..."
                                      class="w-full px-3 py-2 text-xs border @error('description') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                </div>

                <!-- Form Footer Actions -->
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.attributes.index') }}"
                       class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                        Create Attribute
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection
