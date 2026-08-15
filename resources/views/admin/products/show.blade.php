@extends('admin.layouts.app')

@section('title', 'Product Details - ' . $product->name)

@section('content')
    @php
        $currency = $currency_sign ?? 'Tk.';
        $isVariantProduct = $product->product_type === 'variant';
        $variantsCount = $product->variants->count();
        $singleVar = $product->singleProduct;

        // Calculate price and stock display
        if ($isVariantProduct && $variantsCount > 0) {
            $totalStock = $product->variants->sum('stock');
            $activeVariantsCount = $product->variants->where('status', 'active')->count();
            $minPrice = $product->variants->min('sell_price') ?? $product->price;
            $maxPrice = $product->variants->max('sell_price') ?? $product->price;
            $priceDisplay = $minPrice == $maxPrice 
                ? $currency . number_format($minPrice, 2)
                : $currency . number_format($minPrice, 2) . ' - ' . $currency . number_format($maxPrice, 2);
        } else {
            $totalStock = $singleVar?->stock ?? $product->stock ?? 0;
            $activeVariantsCount = 1;
            $sellPrice = $singleVar?->sell_price ?? $product->price ?? 0;
            $discountPrice = $singleVar?->discount_price;
            $priceDisplay = $currency . number_format($sellPrice, 2);
        }

        // Collect all unique images
        $galleryImages = collect();
        foreach ($product->images as $img) {
            $galleryImages->push([
                'url' => $img->getImageUrl(),
                'is_default' => (bool)$img->is_default,
                'title' => 'Product Image'
            ]);
        }
        foreach ($product->variants as $variant) {
            foreach ($variant->images as $vImg) {
                $galleryImages->push([
                    'url' => $vImg->getImageUrl(),
                    'is_default' => false,
                    'title' => 'Variant ' . ($variant->sku ? '(' . $variant->sku . ')' : '')
                ]);
            }
        }
        $galleryImages = $galleryImages->unique('url')->values();
        $defaultImage = $galleryImages->firstWhere('is_default', true)['url'] 
            ?? $galleryImages->first()['url'] 
            ?? asset('images/default-logo.png');
    @endphp

    <div class="max-w-7xl mx-auto" x-data="{
        activeTab: '{{ $isVariantProduct ? 'variants' : 'overview' }}',
        activeImage: '{{ $defaultImage }}',
        lightboxOpen: false,
        copiedText: null,
        copyToClipboard(text, label) {
            navigator.clipboard.writeText(text);
            this.copiedText = label;
            setTimeout(() => this.copiedText = null, 2000);
        }
    }">
        <!-- Single Unified Card (Sharp / Non-rounded) -->
        <div class="bg-white border border-gray-200 shadow-sm">
            
            <!-- Card Header: Title, Badges & Actions -->
            <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-50/80 via-white to-gray-50/50 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="space-y-2">
                        <!-- Breadcrumb -->
                        <nav class="flex items-center space-x-2 text-xs text-gray-500">
                            <a href="{{ route('admin.products.index') }}" class="hover:text-blue-600 font-medium transition flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Products
                            </a>
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-700 font-semibold truncate max-w-xs">{{ $product->name }}</span>
                        </nav>

                        <!-- Title & Status Indicators -->
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $product->name }}</h1>

                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold {{ $product->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                <span class="w-1.5 h-1.5 {{ $product->status === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }} mr-1.5 animate-pulse"></span>
                                {{ ucfirst($product->status) }}
                            </span>

                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                                {{ ucfirst($product->product_type ?? 'Single') }} Product
                            </span>

                            @if ($product->is_featured)
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                    ⭐ Featured
                                </span>
                            @endif

                            @if ($product->is_free_delivery)
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium bg-cyan-50 text-cyan-700 border border-cyan-200">
                                    🚚 Free Delivery
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Header Action Buttons -->
                    <div class="flex flex-wrap items-center gap-2">
                        @if (\Illuminate\Support\Facades\Route::has('product.show') && $product->slug)
                            <a href="{{ route('product.show', $product->slug) }}" target="_blank"
                               class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-sm transition">
                                <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                View in Store
                            </a>
                        @endif

                        @if ($isVariantProduct)
                            <a href="{{ route('admin.variants.show', $product) }}"
                               class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Manage Variants
                            </a>
                        @endif

                        <a href="{{ route('admin.products.edit', $product) }}"
                           class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-white bg-amber-500 hover:bg-amber-600 shadow-sm transition">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </a>

                        <a href="{{ route('admin.products.index') }}"
                           class="inline-flex items-center px-3.5 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-sm transition">
                            <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back
                        </a>
                    </div>
                </div>
            </div>

            <!-- Top Showcase Section: Gallery + Key Metrics & Specs -->
            <div class="p-5 sm:p-6 border-b border-gray-200">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- Left: Compact Interactive Image Gallery -->
                    <div class="lg:col-span-4 space-y-3">
                        <div class="relative bg-gray-50 border border-gray-200 h-64 sm:h-72 flex items-center justify-center overflow-hidden group cursor-zoom-in"
                             @click="lightboxOpen = true">
                            <img :src="activeImage" alt="{{ $product->name }}" 
                                 class="max-h-60 sm:max-h-64 w-auto object-contain transition-transform duration-300 group-hover:scale-105">
                            
                            <div class="absolute bottom-2 right-2 bg-black/60 text-white text-[11px] font-medium px-2 py-1 opacity-0 group-hover:opacity-100 transition flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                </svg>
                                Zoom
                            </div>
                        </div>

                        <!-- Gallery Thumbnails -->
                        @if ($galleryImages->count() > 1)
                            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-thin">
                                @foreach ($galleryImages as $img)
                                    <button type="button" 
                                            @click="activeImage = '{{ $img['url'] }}'"
                                            :class="activeImage === '{{ $img['url'] }}' ? 'border-blue-600 opacity-100' : 'border-gray-200 hover:border-gray-400 opacity-60 hover:opacity-100'"
                                            class="w-14 h-14 border-2 bg-white p-0.5 flex-shrink-0 transition cursor-pointer">
                                        <img src="{{ $img['url'] }}" alt="Thumb" class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Right: Key Summary & Metrics Grid -->
                    <div class="lg:col-span-8 space-y-5">
                        
                        <!-- Top Mini Stat Badges -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-gray-50/80 p-3.5 border border-gray-200">
                            <div>
                                <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Total Stock</p>
                                <p class="text-base sm:text-lg font-bold text-gray-900 mt-0.5">
                                    {{ number_format($totalStock) }} <span class="text-xs font-medium text-gray-900">units</span>
                                </p>
                            </div>

                            <div class="border-l border-gray-200 pl-3">
                                <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Variants</p>
                                <p class="text-base sm:text-lg font-bold text-gray-900 mt-0.5">
                                    {{ $variantsCount }} <span class="text-xs font-normal text-emerald-600">({{ $activeVariantsCount }} Active)</span>
                                </p>
                            </div>

                            <div class="border-l border-gray-200 pl-3">
                                <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wider">Media Assets</p>
                                <p class="text-base sm:text-lg font-bold text-gray-900 mt-0.5">
                                    {{ $galleryImages->count() }} <span class="text-xs font-normal text-gray-500">photos</span>
                                </p>
                            </div>
                        </div>

                        <!-- Specifications Key-Value Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs">
                            <div class="space-y-1">
                                <span class="text-gray-500 font-medium">Product Slug</span>
                                <div class="flex items-center space-x-1.5">
                                    <span class="font-mono text-gray-800 bg-gray-100 px-2 py-0.5 border border-gray-200 truncate max-w-[180px]">
                                        {{ $product->slug }}
                                    </span>
                                    <button type="button" @click="copyToClipboard('{{ $product->slug }}', 'slug')" 
                                            class="text-blue-600 hover:text-blue-700 font-medium text-[11px]">
                                        <span x-show="copiedText === 'slug'" class="text-emerald-600 font-bold">Copied!</span>
                                        <span x-show="copiedText !== 'slug'">Copy</span>
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <span class="text-gray-500 font-medium">Category</span>
                                <p class="font-semibold text-gray-900">
                                    {{ $product->category?->name ?: 'Uncategorized' }}
                                </p>
                            </div>

                            <div class="space-y-1">
                                <span class="text-gray-500 font-medium">Brand</span>
                                <p class="font-semibold text-gray-900">
                                    {{ $product->brand?->name ?: 'No Brand' }}
                                </p>
                            </div>

                            <div class="space-y-1">
                                <span class="text-gray-500 font-medium">Free Delivery</span>
                                <p class="font-semibold {{ $product->is_free_delivery ? 'text-emerald-600' : 'text-gray-700' }}">
                                    {{ $product->is_free_delivery ? '🚚 Free Delivery Eligible' : 'Standard Shipping' }}
                                </p>
                            </div>

                            <div class="space-y-1">
                                <span class="text-gray-500 font-medium">Created Date</span>
                                <p class="text-gray-700">{{ $product->created_at ? $product->created_at->format('M d, Y · H:i') : 'N/A' }}</p>
                            </div>

                            <div class="space-y-1">
                                <span class="text-gray-500 font-medium">Last Modified</span>
                                <p class="text-gray-700">{{ $product->updated_at ? $product->updated_at->format('M d, Y · H:i') : 'N/A' }}</p>
                            </div>
                        </div>

                        @if ($product->short_description)
                            <div class="bg-gray-50/70 p-3 border border-gray-200 text-xs text-gray-600 leading-relaxed">
                                <span class="font-semibold text-gray-800 mr-1">Summary:</span>
                                {{ $product->short_description }}
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            <!-- Tab Navigation (Inside the same card) -->
            <div class="px-5 sm:px-6 bg-gray-50/60 border-b border-gray-200">
                <nav class="flex space-x-6 text-sm font-medium">
                    @if ($isVariantProduct)
                        <button type="button" @click="activeTab = 'variants'"
                                :class="activeTab === 'variants' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="py-3.5 px-1 border-b-2 font-semibold transition flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Variants & Inventory ({{ $variantsCount }})
                        </button>
                    @else
                        <button type="button" @click="activeTab = 'overview'"
                                :class="activeTab === 'overview' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="py-3.5 px-1 border-b-2 font-semibold transition flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Pricing & Inventory Overview
                        </button>
                    @endif

                    <button type="button" @click="activeTab = 'description'"
                            :class="activeTab === 'description' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-3.5 px-1 border-b-2 font-semibold transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                        </svg>
                        Full Description
                    </button>

                    <button type="button" @click="activeTab = 'seo'"
                            :class="activeTab === 'seo' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="py-3.5 px-1 border-b-2 font-semibold transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        SEO & Search Preview
                    </button>
                </nav>
            </div>

            <!-- Tab Content Panels (Inside the same card) -->
            <div class="p-5 sm:p-6">
                
                <!-- TAB 1 (Single Product): Overview & Specifications -->
                @if (!$isVariantProduct)
                    <div x-show="activeTab === 'overview'" x-cloak class="space-y-6">
                        <div class="bg-gray-50/80 p-5 border border-gray-200">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Single Product Pricing & Specifications
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 text-xs">
                                <div class="space-y-1 bg-white p-3.5 border border-gray-200">
                                    <span class="text-gray-500 font-medium block">SKU Code</span>
                                    <div class="flex items-center justify-between">
                                        <span class="font-mono font-bold text-gray-900 text-sm">{{ $singleVar?->sku ?? 'N/A' }}</span>
                                        @if($singleVar?->sku)
                                            <button type="button" @click="copyToClipboard('{{ $singleVar->sku }}', 'singleSku')" class="text-blue-600 text-[11px] hover:underline">
                                                <span x-show="copiedText === 'singleSku'" class="text-emerald-600 font-bold">Copied!</span>
                                                <span x-show="copiedText !== 'singleSku'">Copy</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <div class="space-y-1 bg-white p-3.5 border border-gray-200">
                                    <span class="text-gray-500 font-medium block">Regular Selling Price</span>
                                    <span class="font-bold text-gray-900 text-sm">{{ $currency }}{{ number_format($singleVar?->sell_price ?? $product->price ?? 0, 2) }}</span>
                                </div>

                                <div class="space-y-1 bg-white p-3.5 border border-gray-200">
                                    <span class="text-gray-500 font-medium block">Discount Price</span>
                                    @if($singleVar?->discount_price && $singleVar->discount_price < ($singleVar->sell_price ?? $product->price))
                                        @php
                                            $sSell = $singleVar->sell_price ?? $product->price;
                                            $sDisc = $singleVar->discount_price;
                                            $sOff = round((($sSell - $sDisc) / $sSell) * 100, 1);
                                        @endphp
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-emerald-600 text-sm">{{ $currency }}{{ number_format($sDisc, 2) }}</span>
                                            <span class="px-1.5 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                -{{ $sOff }}% OFF
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 font-normal italic">No discount active</span>
                                    @endif
                                </div>

                                <div class="space-y-1 bg-white p-3.5 border border-gray-200">
                                    <span class="text-gray-500 font-medium block">Buying Price</span>
                                    <span class="font-bold text-gray-800 text-sm">{{ $currency }}{{ number_format($singleVar?->buying_price ?? 0, 2) }}</span>
                                </div>

                                <div class="space-y-1 bg-white p-3.5 border border-gray-200">
                                    <span class="text-gray-500 font-medium block">Available Stock</span>
                                    @php $sStock = $singleVar?->stock ?? $product->stock ?? 0; @endphp
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-gray-900 text-sm">{{ number_format($sStock) }} units</span>
                                        <span class="px-2 py-0.5 text-[10px] font-bold {{ $sStock > 10 ? 'bg-emerald-100 text-emerald-800' : ($sStock > 0 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                            {{ $sStock > 10 ? 'In Stock' : ($sStock > 0 ? 'Low Stock' : 'Out of Stock') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="space-y-1 bg-white p-3.5 border border-gray-200">
                                    <span class="text-gray-500 font-medium block">Weight</span>
                                    <span class="font-bold text-gray-900 text-sm">{{ number_format($singleVar?->weight ?? 0, 2) }} kg</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- TAB 1 (Variant Product): Variants & Inventory Table -->
                @if ($isVariantProduct)
                    <div x-show="activeTab === 'variants'" x-cloak>
                        @if ($product->variants->count() > 0)
                            <div class="overflow-x-auto border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                                    <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                                        <tr>
                                            <th class="px-4 py-3">Variant Info</th>
                                            <th class="px-4 py-3">Attributes</th>
                                            <th class="px-4 py-3">Buying Price</th>
                                            <th class="px-4 py-3">Selling / Discount</th>
                                            <th class="px-4 py-3">Stock Level</th>
                                            <th class="px-4 py-3">Weight</th>
                                            <th class="px-4 py-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach ($product->variants as $variant)
                                            @php
                                                $vSellPrice = $variant->sell_price ?? $product->price;
                                                $vDiscountPrice = $variant->discount_price;
                                                $vDiscountPercent = ($vDiscountPrice && $vSellPrice > 0 && $vDiscountPrice < $vSellPrice)
                                                    ? round((($vSellPrice - $vDiscountPrice) / $vSellPrice) * 100, 1)
                                                    : 0;
                                                $vImage = $variant->images->first()?->getImageUrl();
                                            @endphp
                                            <tr class="hover:bg-gray-50/80 transition">
                                                <!-- SKU & Image -->
                                                <td class="px-4 py-3.5 whitespace-nowrap">
                                                    <div class="flex items-center space-x-3">
                                                        @if ($vImage)
                                                            <img src="{{ $vImage }}" alt="Variant" class="w-9 h-9 object-cover border border-gray-200 flex-shrink-0">
                                                        @else
                                                            <div class="w-9 h-9 bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 flex-shrink-0">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                </svg>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div class="flex items-center space-x-1.5">
                                                                <span class="font-mono font-semibold text-gray-900">{{ $variant->sku }}</span>
                                                                <button type="button" @click="copyToClipboard('{{ $variant->sku }}', '{{ $variant->sku }}')" class="text-gray-400 hover:text-gray-600">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                            <span class="text-[11px] text-gray-400">ID #{{ $variant->id }}</span>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Attributes -->
                                                <td class="px-4 py-3.5">
                                                    @if ($variant->variantAttributes && $variant->variantAttributes->count() > 0)
                                                        <div class="flex flex-wrap gap-1.5 max-w-[220px]">
                                                            @foreach ($variant->variantAttributes as $vAttr)
                                                                <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                                    <span class="text-gray-500 font-normal mr-1">{{ $vAttr->attribute?->name }}:</span>
                                                                    <strong class="text-gray-900">{{ $vAttr->attributeValue?->value }}</strong>
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400 italic">Default</span>
                                                    @endif
                                                </td>

                                                <!-- Buying Price -->
                                                <td class="px-4 py-3.5 whitespace-nowrap text-gray-600 font-medium">
                                                    {{ $currency }}{{ number_format($variant->buying_price ?? 0, 2) }}
                                                </td>

                                                <!-- Selling / Discount -->
                                                <td class="px-4 py-3.5 whitespace-nowrap">
                                                    <div>
                                                        <span class="font-bold text-gray-900">{{ $currency }}{{ number_format($vSellPrice, 2) }}</span>
                                                        @if ($vDiscountPrice && $vDiscountPrice < $vSellPrice)
                                                            <div class="flex items-center space-x-1.5 mt-0.5">
                                                                <span class="text-emerald-700 font-semibold">{{ $currency }}{{ number_format($vDiscountPrice, 2) }}</span>
                                                                <span class="px-1.5 py-0.2 text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                                    -{{ $vDiscountPercent }}%
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>

                                                <!-- Stock Level -->
                                                <td class="px-4 py-3.5 whitespace-nowrap">
                                                    @if ($variant->stock <= 0)
                                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold bg-rose-100 text-rose-800">
                                                            Out of Stock (0)
                                                        </span>
                                                    @elseif ($variant->stock < 10)
                                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold bg-amber-100 text-amber-800">
                                                            Low Stock ({{ $variant->stock }})
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                            {{ $variant->stock }} in stock
                                                        </span>
                                                    @endif
                                                </td>

                                                <!-- Weight -->
                                                <td class="px-4 py-3.5 whitespace-nowrap text-gray-500">
                                                    {{ number_format($variant->weight ?? 0, 2) }} kg
                                                </td>

                                                <!-- Status -->
                                                <td class="px-4 py-3.5 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium {{ $variant->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ ucfirst($variant->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 text-sm text-gray-500">
                                No variants found for this product.
                            </div>
                        @endif
                    </div>
                @endif

                <!-- TAB 2: Description & Content -->
                <div x-show="activeTab === 'description'" x-cloak class="space-y-4">
                    @if ($product->description)
                        <div class="prose prose-sm max-w-none text-gray-700 bg-gray-50/50 p-5 border border-gray-200 leading-relaxed">
                            {!! $product->description !!}
                        </div>
                    @else
                        <div class="text-center py-8 text-sm text-gray-400 italic">
                            No detailed description provided for this product.
                        </div>
                    @endif
                </div>

                <!-- TAB 3: SEO Preview -->
                <div x-show="activeTab === 'seo'" x-cloak class="space-y-4">
                    <div class="bg-gray-50/90 p-4 border border-gray-200 space-y-1.5 max-w-2xl text-xs">
                        <div class="flex items-center space-x-1.5 text-gray-500 font-mono text-[11px] truncate">
                            <span>{{ url('/') }}</span>
                            <span>&rsaquo;</span>
                            <span class="text-gray-700 font-medium">product</span>
                            <span>&rsaquo;</span>
                            <span class="text-blue-700 truncate">{{ $product->slug }}</span>
                        </div>
                        <h4 class="text-sm font-medium text-blue-800 hover:underline cursor-pointer line-clamp-1">
                            {{ $product->meta_title ?: $product->name . ' - ' . config('app.name') }}
                        </h4>
                        <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed">
                            {{ $product->meta_description ?: ($product->short_description ?: 'Explore our collection of ' . $product->name) }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs pt-2">
                        <div>
                            <span class="text-gray-500 font-medium">Custom Meta Title:</span>
                            <p class="text-gray-800 font-medium mt-0.5">{{ $product->meta_title ?: 'Not specified (Auto-generated)' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 font-medium">Custom Meta Description:</span>
                            <p class="text-gray-800 mt-0.5">{{ $product->meta_description ?: 'Not specified' }}</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Card Footer: Danger Zone / Delete Button -->
            <div class="px-5 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <span class="text-xs text-gray-500">Product ID: #{{ $product->id }}</span>

                <div>
                    @if ($product->trashed())
                        <div class="flex items-center space-x-2">
                            <form action="{{ route('admin.products.restore', $product->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition"
                                    onclick="return confirm('Are you sure you want to restore this product?')">
                                    Restore Product
                                </button>
                            </form>

                            <form action="{{ route('admin.products.force-delete', $product->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-rose-600 hover:bg-rose-700 shadow-sm transition"
                                    onclick="return confirm('Permanently delete this product?')">
                                    Delete Permanently
                                </button>
                            </form>
                        </div>
                    @else
                        <button type="button"
                            onclick="showDeleteModal('product', '{{ route('admin.products.destroy', $product) }}', '{{ addslashes($product->name) }}')"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                            <svg class="w-3.5 h-3.5 mr-1.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete Product
                        </button>
                    @endif
                </div>
            </div>

        </div>

        <!-- Lightbox Zoom Modal -->
        <div x-show="lightboxOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm"
             @keydown.escape.window="lightboxOpen = false">
            <div class="relative max-w-4xl max-h-[90vh] bg-white p-2 shadow-2xl overflow-hidden" @click.away="lightboxOpen = false">
                <button type="button" @click="lightboxOpen = false" 
                        class="absolute top-4 right-4 z-10 bg-black/60 hover:bg-black text-white p-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <img :src="activeImage" alt="Zoomed view" class="max-h-[85vh] max-w-full object-contain mx-auto">
            </div>
        </div>
    </div>
@endsection
