@extends('admin.layouts.app')

@section('title', 'Attribute Value Details')

@section('content')
<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg max-w-4xl mx-auto">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Attribute Value Details</h2>
        <div class="flex space-x-2">
            <a href="{{ route('admin.attribute-values.edit', $attributeValue->id) }}" class="text-yellow-600 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                Edit
            </a>
            <a href="{{ route('admin.attribute-values.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                Back to Attribute Values
            </a>
        </div>
    </div>

    <div class="p-6">
        <!-- Attribute Value Information -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">General Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ID</label>
                    <p class="text-gray-900 dark:text-white">{{ $attributeValue->id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                        Active
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Created At</label>
                    <p class="text-gray-900 dark:text-white">{{ $attributeValue->created_at->format('F d, Y H:i:s') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Updated</label>
                    <p class="text-gray-900 dark:text-white">{{ $attributeValue->updated_at->format('F d, Y H:i:s') }}</p>
                </div>
            </div>
        </div>

        <!-- Attribute Information -->
        @if($attributeValue->attribute)
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Attribute Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Attribute Name</label>
                    <p class="text-gray-900 dark:text-white">{{ $attributeValue->attribute->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Attribute Type</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                        {{ $attributeValue->attribute->type_name }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Required</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $attributeValue->attribute->is_required ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300' }}">
                        {{ $attributeValue->attribute->is_required ? 'Yes' : 'No' }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Active</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $attributeValue->attribute->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                        {{ $attributeValue->attribute->is_active ? 'Yes' : 'No' }}
                    </span>
                </div>
                @if($attributeValue->attribute->description)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <p class="text-gray-900 dark:text-white">{{ $attributeValue->attribute->description }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Product Information -->
        @if($attributeValue->product)
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Name</label>
                    <p class="text-gray-900 dark:text-white">{{ $attributeValue->product->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product SKU</label>
                    <p class="text-gray-900 dark:text-white">{{ $attributeValue->product->sku ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price</label>
                    <p class="text-gray-900 dark:text-white">${{ number_format($attributeValue->product->price, 2) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $attributeValue->product->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                        {{ ucfirst($attributeValue->product->status) }}
                    </span>
                </div>
            </div>
        </div>
        @endif

        <!-- Value Information -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Value Information</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Display Value</label>
                    <div class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg p-3">
                        @if($attributeValue->values && is_array($attributeValue->values))
                            <div class="flex flex-wrap gap-2">
                                @foreach($attributeValue->values as $value)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                        {{ $value }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-900 dark:text-white">{{ $attributeValue->value ?: '<span class="text-gray-400">No value set</span>' }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Raw Value</label>
                        <p class="text-gray-900 dark:text-white font-mono text-sm">{{ $attributeValue->value ?: 'NULL' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Raw Values (JSON)</label>
                        <p class="text-gray-900 dark:text-white font-mono text-sm">{{ $attributeValue->values ? json_encode($attributeValue->values) : 'NULL' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.attribute-values.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                Back to List
            </a>
            <a href="{{ route('admin.attribute-values.edit', $attributeValue->id) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                Edit Attribute Value
            </a>
        </div>
    </div>
</div>
@endsection
