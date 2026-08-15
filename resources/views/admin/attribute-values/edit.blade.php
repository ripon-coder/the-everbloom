@extends('admin.layouts.app')

@section('title', 'Edit Attribute Value')

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
                            <a href="{{ route('admin.attribute-values.index') }}" class="hover:text-gray-900 transition">Attribute Values</a>
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-700 font-semibold truncate max-w-[200px]">{{ $attributeValue->value }}</span>
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-500">Edit</span>
                        </nav>
                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Edit Attribute Value</h1>
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                #{{ $attributeValue->id }}
                            </span>
                        </div>
                    </div>

                    <!-- Header Action -->
                    <a href="{{ route('admin.attribute-values.index') }}"
                       class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-sm transition">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Values
                    </a>
                </div>
            </div>

            <!-- Form Body -->
            <form action="{{ route('admin.attribute-values.update', $attributeValue->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-5 sm:p-6 space-y-6">
                    
                    <!-- General Details Grid -->
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700 pb-2 mb-4 border-b border-gray-200">
                            Value Details
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <!-- Parent Attribute Selector -->
                            <div>
                                <label for="attribute_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Parent Attribute <span class="text-rose-500">*</span>
                                </label>
                                <select name="attribute_id" id="attribute_id" required
                                        class="w-full py-2 px-3 text-xs border @error('attribute_id') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600 cursor-pointer">
                                    <option value="">Select an Attribute</option>
                                    @foreach ($attributes as $attribute)
                                        <option value="{{ $attribute->id }}" {{ old('attribute_id', $attributeValue->attribute_id) == $attribute->id ? 'selected' : '' }}>
                                            {{ $attribute->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('attribute_id')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Option Value -->
                            <div>
                                <label for="value" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Value <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="value" id="value" required
                                       value="{{ old('value', $attributeValue->value) }}"
                                       placeholder="e.g., Red, Blue, Small, XL, 250ml"
                                       class="w-full px-3 py-2 text-xs border @error('value') border-rose-500 @else border-gray-300 @enderror bg-white focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                                @error('value')
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
                                    <option value="active" {{ old('status', $attributeValue->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $attributeValue->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Form Footer with Meta & Actions (No Delete Button) -->
                <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="text-gray-500 space-x-3">
                        <span>Created: {{ $attributeValue->created_at ? $attributeValue->created_at->format('M d, Y · H:i') : 'N/A' }}</span>
                        <span>&bull;</span>
                        <span>Updated: {{ $attributeValue->updated_at ? $attributeValue->updated_at->format('M d, Y · H:i') : 'N/A' }}</span>
                    </div>

                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.attribute-values.index') }}"
                           class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 transition">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                            Update Value
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection
