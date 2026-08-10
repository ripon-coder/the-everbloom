@extends('admin.layouts.app')

@section('title', 'Product Details')

@section('content')
    <div class="p-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Product Details</h1>
            <div class="flex space-x-3">
                @if ($product->product_type === 'variant')
                    <a href="{{ route('admin.variants.show', $product) }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Variant
                    </a>
                @endif
                <a href="{{ route('admin.products.edit', $product) }}"
                    class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('admin.products.index') }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back
                </a>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Product Information -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Product Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Product Name</label>
                            <p class="text-base text-gray-900">{{ $product->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Slug</label>
                            <p class="text-base text-gray-900">{{ $product->slug }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Brand</label>
                            <p class="text-base text-gray-900">{{ $product->brand?->name ?: 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Category</label>
                            <p class="text-base text-gray-900">{{ $product->category?->name ?: 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Price</label>
                            <p class="text-base text-gray-900 font-semibold">{{ $currency_sign }}{{ number_format($product->price, 2) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                            <span
                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($product->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Is Free Delivery?</label>
                        <p class="text-base text-gray-900">
                            {{ $product->is_free_delivery ? 'Yes' : 'No' }}
                        </p>
                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Created At</label>
                            <p class="text-base text-gray-900">{{ $product->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Updated At</label>
                            <p class="text-base text-gray-900">{{ $product->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Product Variants -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Product Variants</h2>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $product->variants->count() }} variants
                        </span>
                    </div>

                    @if ($product->variants->count() > 0)
                        <div class="space-y-4">
                            @foreach ($product->variants as $variant)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h3 class="text-base font-medium text-gray-900">SKU: {{ $variant->sku }}</h3>
                                            @php
                                                $sellPrice = $variant->sell_price ?? $product->sell_price;
                                                $discountPrice = $variant->discount_price ?? $product->discount_price;
                                                $discountPercent =
                                                    $sellPrice > 0
                                                        ? round((($sellPrice - $discountPrice) / $sellPrice) * 100, 2)
                                                        : 0;
                                            @endphp

                                            <div class="flex flex-wrap items-center gap-4 mt-1 text-sm text-gray-500">
                                                <span>Buying Price:
                                                    {{ $currency_sign }}{{ number_format($variant->buying_price ?? $product->buying_price, 2) }}</span>
                                                <span>Sell Price: {{ $currency_sign }}{{ number_format($sellPrice, 2) }}</span>
                                                <span>Discount Price: {{ $currency_sign }}{{ number_format($discountPrice, 2) }}</span>
                                                <span class="text-green-700 font-medium">({{ $discountPercent }}%
                                                    Off)</span>
                                                <span>Stock: {{ $variant->stock }}</span>
                                                <span>Weight: {{ number_format($variant->weight, 2) }} kg</span>
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
        {{ $variant->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst($variant->status) }}
                                                </span>
                                            </div>


                                        </div>
                                    </div>

                                    <!-- Variant Attributes -->
                                    @if ($variant->variantAttributes->count() > 0)
                                        <div class="mb-3">
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">Attributes:</h4>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($variant->variantAttributes as $variantAttribute)
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $variantAttribute->attribute?->name }}:
                                                        {{ $variantAttribute->attributeValue?->value }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Variant Images -->
                                    @if ($variant->images->count() > 0)
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">Images:</h4>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($variant->images as $variantImage)
                                                    <div class="relative">
                                                        <img src="{{ $variantImage->getImageUrl() }}" alt="Variant image"
                                                            class="w-16 h-16 object-cover rounded-lg">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                </path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No variants found for this product.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Product Images -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Product Images</h2>

                    @if ($product->images->count() > 0)
                        <div class="space-y-3">
                            @foreach ($product->images as $image)
                                <div class="relative group">
                                    <img src="{{ $image->getImageUrl() }}" alt="{{ $product->name }}"
                                        class="w-full h-48 object-cover ">
                                    @if ($image->is_default)
                                        <span
                                            class="absolute top-2 right-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Default
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No images uploaded for this product.</p>
                        </div>
                    @endif
                </div>

                <!-- Quick Stats -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Stats</h2>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Variants</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $product->variants->count() }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Active Variants</span>
                            <span
                                class="text-lg font-semibold text-green-600">{{ $product->variants->where('status', 'active')->count() }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Stock</span>
                            <span
                                class="text-lg font-semibold text-gray-900">{{ $product->variants->sum('stock') }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Images</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $product->images->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>

                    <div class="space-y-3">
                        <a href="{{ route('admin.products.edit', $product) }}"
                            class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Edit Product
                        </a>

                        @if ($product->trashed())
                            <form action="{{ route('admin.products.restore', $product->id) }}" method="POST"
                                class="block w-full">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                                    onclick="return confirm('Are you sure you want to restore this product?')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                    Restore Product
                                </button>
                            </form>

                            <form action="{{ route('admin.products.force-delete', $product->id) }}" method="POST"
                                class="block w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                                    onclick="return confirm('Are you sure you want to permanently delete this product? This action cannot be undone.')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                    Delete Permanently
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                class="block w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                                    onclick="return confirm('Are you sure you want to delete this product?')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                    Delete Product
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
