@extends('layouts.app')

@section('title', ($product['name'] ?? 'Product') . ' | The Everbloom')
@section('meta_description', $product['short_description'] ?? 'Botanical skincare formulation from The Everbloom.')

@php
    $productCategory = $product['category'] ?? 'Skincare';
    $categoryUrl = !empty($product['category_slug']) ? route('shop.category', $product['category_slug']) : '/shop';

    if (isset($related) && is_array($related)) {
        $relatedProducts = $related;
    } else {
        $allProducts = collect(config('demo.products', []));
        $sameCategory = $allProducts
            ->where('category', $productCategory)
            ->where('id', '!=', $product['id'] ?? 0)
            ->values();

        if ($sameCategory->count() < 4) {
            $needed = 4 - $sameCategory->count();
            $excludeIds = $sameCategory->pluck('id')->push($product['id'] ?? 0)->all();
            $fallbacks = $allProducts->whereNotIn('id', $excludeIds)->take($needed);
            $relatedProducts = $sameCategory->concat($fallbacks)->values()->all();
        } else {
            $relatedProducts = $sameCategory->take(4)->values()->all();
        }
    }

    $images = !empty($product['images']) && is_array($product['images']) ? array_values($product['images']) : ['products/placeholder.jpg'];
    $sizes = !empty($product['sizes']) && is_array($product['sizes']) ? array_values($product['sizes']) : ['Standard'];
    $sku = $product['sku'] ?? ('EB-' . str_pad((string)($product['id'] ?? 1), 3, '0', STR_PAD_LEFT));
    $basePrice = (float) ($product['price'] ?? 0);
    $comparePrice = !empty($product['compare_at_price']) ? (float) $product['compare_at_price'] : null;
    $isOnSale = $comparePrice !== null && $comparePrice > $basePrice;
    $defaultSavePercent = $isOnSale && $comparePrice > 0
        ? (int) round((($comparePrice - $basePrice) / $comparePrice) * 100)
        : 0;

    // Prepare explicit variant options from config/demo/products.php variants or sizes fallback
    $variants = [];
    if (!empty($product['variants']) && is_array($product['variants'])) {
        foreach ($product['variants'] as $v) {
            $vLabel = $v['title'] ?? $v['size'] ?? $v['id'] ?? $v['label'] ?? $sizes[0];
            $variants[] = [
                'id' => $vLabel,
                'label' => $vLabel,
                'attributes' => $v['attributes'] ?? [],
                'image' => $v['image'] ?? null,
                'price' => (float) ($v['price'] ?? $basePrice),
                'compare_at_price' => !empty($v['compare_at_price']) ? (float) $v['compare_at_price'] : null,
                'stock_status' => $v['stock_status'] ?? ($product['stock_status'] ?? 'in_stock'),
            ];
        }
    } else {
        foreach ($sizes as $idx => $sizeLabel) {
            $multiplier = $idx === 0 ? 1 : ($idx === 1 ? 1.55 : 2.0);
            $vPrice = $idx === 0 ? $basePrice : round($basePrice * $multiplier);
            $vCompare = $comparePrice ? ($idx === 0 ? $comparePrice : round($comparePrice * $multiplier)) : null;
            $variants[] = [
                'id' => $sizeLabel,
                'label' => $sizeLabel,
                'price' => $vPrice,
                'compare_at_price' => $vCompare,
                'stock_status' => $product['stock_status'] ?? 'in_stock',
            ];
        }
    }

    $defaultVariant = $variants[0] ?? [
        'id' => $sizes[0],
        'label' => $sizes[0],
        'price' => $basePrice,
        'compare_at_price' => $comparePrice,
        'stock_status' => $product['stock_status'] ?? 'in_stock',
    ];

    $freeShippingThreshold = (float) config('demo.site.shipping.free_shipping_threshold', 75);
    $standardDays = config('demo.site.shipping.standard_days', '3â€“5 business days');
    $returnWindowDays = (int) config('demo.site.returns.window_days', 30);
    $secureCheckoutLabel = config('demo.site.trust_badges.secure_checkout', 'Secure Checkout');

    // Structured Data (Schema.org Product)
    $productSchema = [
        '@context' => 'https://schema.org/',
        '@type' => 'Product',
        'name' => $product['name'] ?? 'Product',
        'description' => $product['short_description'] ?? '',
        'sku' => $sku,
        'brand' => [
            '@type' => 'Brand',
            'name' => 'The Everbloom',
        ],
        'offers' => [
            '@type' => 'Offer',
            'url' => url()->current(),
            'priceCurrency' => $currencyCode,
            'price' => number_format($basePrice, 2, '.', ''),
            'availability' => ($defaultVariant['stock_status'] ?? 'in_stock') !== 'out_of_stock'
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
        ],
        'aggregateRating' => [
            '@type' => 'AggregateRating',
            'ratingValue' => $product['rating'] ?? 5,
            'reviewCount' => $product['review_count'] ?? 0,
        ],
    ];
@endphp

@push('structured-data')
<script type="application/ld+json">
{!! json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('content')
<div 
    x-data="{
        activeImageIndex: 0,
        images: @js($images),
        variants: @js($variants),
        selectedSize: @js($defaultVariant['id']),
        quantity: 1,
        adding: false,
        buyingNow: false,
        activeAccordion: 'description',
        touchStartX: 0,
        touchEndX: 0,
        get currentVariant() {
            if (!Array.isArray(this.variants) || this.variants.length === 0) {
                return {
                    id: 'Standard',
                    label: 'Standard',
                    price: {{ $basePrice }},
                    compare_at_price: {{ $comparePrice !== null ? $comparePrice : 'null' }},
                    stock_status: 'in_stock'
                };
            }
            return this.variants.find(v => v.id === this.selectedSize) || this.variants[0];
        },
        get currentPrice() {
            return Number(this.currentVariant?.price ?? {{ $basePrice }});
        },
        get currentCompareAtPrice() {
            const cmp = this.currentVariant?.compare_at_price;
            return cmp !== null && cmp !== undefined && Number(cmp) > this.currentPrice ? Number(cmp) : null;
        },
        get savePercent() {
            if (!this.currentCompareAtPrice || this.currentCompareAtPrice <= this.currentPrice) return 0;
            return Math.round(((this.currentCompareAtPrice - this.currentPrice) / this.currentCompareAtPrice) * 100);
        },
        get isInStock() {
            return (this.currentVariant?.stock_status ?? 'in_stock') !== 'out_of_stock';
        },
        nextImage() {
            if (!Array.isArray(this.images) || this.images.length === 0) return;
            this.activeImageIndex = (this.activeImageIndex + 1) % this.images.length;
        },
        prevImage() {
            if (!Array.isArray(this.images) || this.images.length === 0) return;
            this.activeImageIndex = (this.activeImageIndex - 1 + this.images.length) % this.images.length;
        },
        handleTouchStart(e) {
            this.touchStartX = e.changedTouches?.[0]?.screenX ?? 0;
        },
        handleTouchEnd(e) {
            this.touchEndX = e.changedTouches?.[0]?.screenX ?? 0;
            if (this.touchStartX - this.touchEndX > 40) {
                this.nextImage();
            } else if (this.touchEndX - this.touchStartX > 40) {
                this.prevImage();
            }
        },
        ensureInCatalog() {
            const currentProduct = @js($product);
            if (!Array.isArray(window.__everbloomProducts) || window.__everbloomProducts.length === 0) {
                const el = document.getElementById('everbloom-products-data') || document.getElementById('everbloom-search-data');
                if (el) {
                    try {
                        const parsed = JSON.parse(el.textContent);
                        window.__everbloomProducts = Array.isArray(parsed) ? parsed : [];
                    } catch (e) {
                        window.__everbloomProducts = [];
                    }
                } else {
                    window.__everbloomProducts = [];
                }
            }
            if (!window.__everbloomProducts.some(p => String(p.id) === String(currentProduct.id))) {
                window.__everbloomProducts.push(currentProduct);
            }
        },
        addToCart() {
            if (!this.isInStock) return;
            this.ensureInCatalog();
            this.adding = true;
            $store.cart?.add({
                id: '{{ $product['id'] ?? 1 }}',
                variantId: this.currentVariant?.id || this.selectedSize
            }, this.quantity, this.currentVariant?.id || this.selectedSize, true);
            setTimeout(() => { this.adding = false; }, 900);
        },
        buyNow() {
            if (!this.isInStock) return;
            this.ensureInCatalog();
            this.buyingNow = true;
            $store.cart?.add({
                id: '{{ $product['id'] ?? 1 }}',
                variantId: this.currentVariant?.id || this.selectedSize
            }, this.quantity, this.currentVariant?.id || this.selectedSize, false);
            window.location.href = '{{ route('checkout.index') }}';
        }
    }"
    @qty-change.window="quantity = Math.max(1, Number($event.detail) || 1)"
    class="bg-ivory pb-10 lg:pb-20"
>
    <!-- Top Breadcrumb Bar (mt-4 on mobile, mt-6 on lg) -->
    <div class="site-container border-b border-[#ECEEE6] mt-4 lg:mt-6">
        <x-storefront.breadcrumbs :items="[
            ['label' => 'Shop', 'url' => '/shop'],
            ['label' => $productCategory, 'url' => $categoryUrl],
            ['label' => $product['name'] ?? 'Product']
        ]" />
    </div>

    <!-- Main Product Detail Grid -->
    <div class="site-container pt-4 lg:pt-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-12 items-start">
            
            <!-- LEFT COLUMN: Sticky Image Gallery Only (7 Cols on lg, self-start: never stretch) -->
            <div class="lg:col-span-7 self-start lg:sticky lg:top-6 pb-4 lg:pb-0">
                <div class="flex flex-col-reverse lg:flex-row items-start gap-2.5 lg:gap-3">
                    
                    <!-- Square Thumbnail Rail (56px horizontal snap-x row on mobile, 80px vertical strip on lg+) -->
                    @if(count($images) > 1)
                        <div class="flex lg:flex-col gap-2 lg:gap-3 overflow-x-auto snap-x lg:overflow-visible pb-0 shrink-0 lg:w-20 max-w-full" role="tablist" aria-label="Product gallery thumbnails">
                            @foreach($images as $index => $imgSrc)
                                <button 
                                    type="button" 
                                    role="tab"
                                    :aria-selected="activeImageIndex === {{ $index }} ? 'true' : 'false'"
                                    @click="activeImageIndex = {{ $index }}"
                                    @keydown.arrow-right.prevent="nextImage()"
                                    @keydown.arrow-down.prevent="nextImage()"
                                    @keydown.arrow-left.prevent="prevImage()"
                                    @keydown.arrow-up.prevent="prevImage()"
                                    :class="activeImageIndex === {{ $index }} ? 'border-forest opacity-100' : 'border-[#E3E6DD] opacity-70 hover:opacity-100 hover:border-[#A8B7A0]'"
                                    class="relative aspect-square w-14 lg:w-20 shrink-0 snap-start border transition-colors duration-200 overflow-hidden bg-[#E8EEE5] cursor-pointer focus-visible:outline-none"
                                    aria-label="View product image {{ $index + 1 }} of {{ count($images) }}"
                                >
                                    <x-storefront.image 
                                        :src="$imgSrc" 
                                        :alt="($product['name'] ?? 'Product') . ' thumbnail ' . ($index + 1)" 
                                        aspect-ratio="aspect-square"
                                        class="!absolute inset-0 w-full h-full object-cover"
                                        width="160"
                                        height="160"
                                        loading="eager"
                                    />
                                    <span class="sr-only">View image {{ $index + 1 }}</span>
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <!-- Main Active Image Stage: aspect-[4/5], max-h-[70vh] on mobile, uncapped aspect-[4/5] on lg+ -->
                    <div 
                        class="relative w-full aspect-[4/5] max-h-[70vh] lg:max-h-none overflow-hidden bg-[#E8EEE5] border border-[#E3E6DD] min-w-0 self-start"
                        @touchstart.passive="handleTouchStart($event)"
                        @touchend.passive="handleTouchEnd($event)"
                    >
                        <div
                            x-show="currentVariant && currentVariant.image"
                            x-cloak
                            class="absolute inset-0 w-full h-full"
                        >
                            <img :src="currentVariant?.image" alt="{{ $product['name'] ?? 'Product' }}" class="w-full h-full object-cover object-center">
                        </div>
                        @foreach($images as $index => $imgSrc)
                            <div 
                                x-show="!(currentVariant && currentVariant.image) && activeImageIndex === {{ $index }}" 
                                x-transition:enter="transition-opacity ease-out duration-200"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                class="absolute inset-0 w-full h-full"
                                @if($index > 0) x-cloak @endif
                            >
                                <x-storefront.image 
                                    :src="$imgSrc" 
                                    :alt="($product['name'] ?? 'Product') . ' â€” ' . $productCategory . ' view ' . ($index + 1)" 
                                    aspect-ratio=""
                                    class="!absolute inset-0 w-full h-full object-cover"
                                    width="960"
                                    height="1200"
                                    loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                />
                            </div>
                        @endforeach

                        <!-- Top-left Status / Signature / Sale Badges -->
                        <div class="absolute top-4 left-4 flex flex-col gap-1.5 pointer-events-none z-10">
                            @if($isOnSale)
                                <span class="bg-forest text-ivory font-sans text-xs uppercase tracking-[0.2em] font-medium px-3 py-1 shadow-sm">
                                    Sale
                                </span>
                            @endif
                            @if(!empty($product['badge']) && !$isOnSale)
                                <span class="bg-ivory/95 text-forest font-sans text-xs uppercase tracking-[0.2em] font-medium px-3 py-1 border border-forest">
                                    {{ $product['badge'] }}
                                </span>
                            @endif
                        </div>

                        <!-- Prev / Next Controls on Mobile / Tablet if multiple images -->
                        @if(count($images) > 1)
                            <div class="absolute inset-x-3 top-1/2 -translate-y-1/2 flex items-center justify-between pointer-events-none lg:hidden z-10">
                                <button 
                                    type="button"
                                    @click="prevImage()"
                                    class="pointer-events-auto w-11 h-11 rounded-full bg-ivory/90 border border-[#E3E6DD] text-ink flex items-center justify-center shadow-sm cursor-pointer"
                                    aria-label="Previous image"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    <span class="sr-only">Previous</span>
                                </button>
                                <button 
                                    type="button"
                                    @click="nextImage()"
                                    class="pointer-events-auto w-11 h-11 rounded-full bg-ivory/90 border border-[#E3E6DD] text-ink flex items-center justify-center shadow-sm cursor-pointer"
                                    aria-label="Next image"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5l7 7-7 7" />
                                    </svg>
                                    <span class="sr-only">Next</span>
                                </button>
                            </div>
                        @endif

                        <!-- Image Counter Pill on Mobile -->
                        @if(count($images) > 1)
                            <div class="absolute bottom-3 right-3 bg-ivory/90 backdrop-blur-xs border border-[#E3E6DD] px-2.5 py-1 text-xs font-sans text-ink lg:hidden">
                                <span x-text="activeImageIndex + 1">1</span> / {{ count($images) }}
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            <!-- RIGHT COLUMN: Non-Sticky Buy Box & Accordions (5 Cols on lg) -->
            <div class="lg:col-span-5 min-w-0 space-y-4 lg:space-y-6">
                
                <!-- Category & SKU Row -->
                <div class="flex items-center justify-between text-xs uppercase tracking-[0.2em] text-muted">
                    <a href="{{ $categoryUrl }}" class="text-forest font-medium hover:underline">
                        {{ $productCategory }}
                    </a>
                    <span>SKU: {{ $sku }}</span>
                </div>

                <!-- Product Title, Subtitle & Left-Aligned Rating -->
                <div class="space-y-2">
                    <h1 class="font-serif text-2xl sm:text-3xl lg:text-4xl text-ink font-normal leading-tight">
                        {{ $product['name'] ?? 'Botanical Formulation' }}
                    </h1>
                    @if(!empty($product['subtitle']))
                        <p class="font-serif italic text-base lg:text-lg text-muted">
                            {{ $product['subtitle'] }}
                        </p>
                    @endif

                    <div class="pt-0.5">
                        <a href="#reviews" class="inline-flex items-center gap-2 group">
                            <x-storefront.rating :rating="$product['rating'] ?? 5" :count="$product['review_count'] ?? 0" size="md" />
                            <span class="text-[13px] text-muted underline group-hover:text-forest transition-colors">
                                Read reviews
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Price & Compare-At Row -->
                <div class="flex flex-wrap items-baseline gap-3 pb-4 lg:pb-5 border-b border-[#ECEEE6]">
                    <span class="price text-2xl text-ink" x-text="window.formatCurrency(currentPrice)">
                        {{ format_price($basePrice) }}
                    </span>

                    <template x-if="currentCompareAtPrice">
                        <span class="inline-flex items-baseline gap-2.5">
                            <span class="price text-sm text-muted line-through" x-text="window.formatCurrency(currentCompareAtPrice)"></span>
                            <span class="text-xs uppercase tracking-[0.15em] font-medium px-2 py-0.5 bg-sage-light text-forest border border-forest/30" x-show="savePercent > 0" x-text="'Save ' + savePercent + '%'"></span>
                        </span>
                    </template>
                    @if($isOnSale && $defaultSavePercent > 0)
                        <noscript>
                            <span class="price text-sm text-muted line-through">{{ format_price($comparePrice) }}</span>
                            <span class="text-xs uppercase tracking-[0.15em] font-medium px-2 py-0.5 bg-sage-light text-forest border border-forest/30">Save {{ $defaultSavePercent }}%</span>
                        </noscript>
                    @endif
                </div>

                <!-- Short Editorial Description -->
                <p class="text-sm lg:text-base text-muted leading-relaxed">
                    {{ $product['short_description'] ?? '' }}
                </p>

                <!-- Skin Types Tag List -->
                @if(!empty($product['skin_types']))
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs uppercase tracking-[0.2em] text-muted mr-1">Best For:</span>
                        @foreach($product['skin_types'] as $skinType)
                            <span class="text-[13px] bg-beige/60 text-ink px-2.5 py-1 border border-[#E3E6DD]">
                                {{ $skinType }} Skin
                            </span>
                        @endforeach
                    </div>
                @endif

                <!-- Size / Variant Selector (2-col grid on mobile, flex-wrap on lg+; min 44px height) -->
                <div class="pt-1 space-y-2.5">
                    <div class="flex items-center justify-between gap-2">
                        <span id="size-selector-label" class="text-xs uppercase tracking-[0.2em] font-medium text-ink">
                            SIZE: <span class="text-forest font-semibold" x-text="currentVariant?.label || selectedSize">{{ $defaultVariant['label'] }}</span>
                        </span>
                        <span class="text-[13px] flex items-center gap-1.5" :class="isInStock ? 'text-forest' : 'text-muted'">
                            <span class="w-1.5 h-1.5 rounded-full" :class="isInStock ? 'bg-forest' : 'bg-muted'"></span>
                            <span x-text="!isInStock ? 'Out of stock' : ((currentVariant?.stock_status === 'low_stock') ? 'Low stock' : 'In stock')">In stock</span>
                        </span>
                    </div>

                    <div class="grid grid-cols-2 lg:flex lg:flex-wrap gap-2.5" role="radiogroup" aria-labelledby="size-selector-label">
                        <template x-for="variant in variants" :key="variant.id">
                            <button 
                                type="button"
                                role="radio"
                                :aria-checked="selectedSize === variant.id ? 'true' : 'false'"
                                @click="selectedSize = variant.id"
                                :class="selectedSize === variant.id 
                                    ? 'border-forest bg-sage-light text-forest font-medium' 
                                    : 'border-[#D5DBCF] bg-ivory text-ink hover:border-[#A8B7A0]'"
                                class="w-full lg:w-auto px-3 lg:px-4 py-2.5 min-h-[44px] text-xs tracking-wider uppercase border transition-colors cursor-pointer inline-flex items-center justify-center gap-1.5 lg:gap-2 focus-visible:outline-none"
                            >
                                <span x-text="variant.label" class="truncate"></span>
                                <span class="opacity-70" aria-hidden="true">&mdash;</span>
                                <span class="price shrink-0" x-text="window.formatCurrency(variant.price)"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Quantity + Wishlist (row 1 on mobile) + Add to Bag + Buy Now -->
                <div class="pt-1 space-y-3">
                    <div class="flex flex-wrap sm:flex-nowrap items-stretch justify-between sm:justify-start gap-3">
                        <!-- Quantity Stepper -->
                        <x-storefront.quantity-selector :quantity="1" class="shrink-0" />

                        <!-- Wishlist Toggle Button -->
                        <div class="sm:order-3 shrink-0">
                            <x-storefront.wishlist-button 
                                :product-id="$product['id'] ?? 1" 
                                size="lg" 
                                class="!rounded-none !w-12 !h-11 !min-w-[44px] !min-h-[44px] !border-[#D5DBCF]" 
                            />
                        </div>

                        <!-- Primary Add to Bag CTA -->
                        <button 
                            type="button"
                            @click="addToCart()"
                            :disabled="adding || !isInStock"
                            class="w-full sm:w-auto sm:flex-1 sm:order-2 btn-primary min-h-[48px] sm:min-h-[44px] relative overflow-hidden cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            <span x-show="!adding" class="inline-flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <span>Add to Bag &mdash; <span class="price" x-text="window.formatCurrency(currentPrice * quantity)">{{ format_price($basePrice) }}</span></span>
                            </span>

                            <span x-show="adding" x-cloak class="inline-flex items-center justify-center gap-2" aria-live="polite">
                                <svg class="w-4 h-4 animate-spin shrink-0" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Added to Bag</span>
                            </span>
                        </button>
                    </div>

                    <!-- Secondary Buy Now Outline CTA -->
                    <button 
                        type="button"
                        @click="buyNow()"
                        :disabled="buyingNow || !isInStock"
                        class="w-full btn-secondary min-h-[44px] cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <span x-show="!buyingNow">Buy Now</span>
                        <span x-show="buyingNow" x-cloak>Proceeding to Checkout&hellip;</span>
                    </button>
                </div>

                <!-- Micro Trust Highlights under CTA (// DEMO PLACEHOLDER: replace before launch) -->
                {{-- // DEMO PLACEHOLDER: replace before launch --}}
                <!-- Mobile: compact vertical list inside one white bordered box -->
                <div class="md:hidden bg-white border border-[#E3E6DD] divide-y divide-[#ECEEE6]">
                    <div class="py-2.5 px-3 flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-forest shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                        <p class="text-sm text-ink">
                            <span class="font-medium">Free shipping</span>
                            <span class="text-[13px] text-muted"> over {{ $currencySymbol }}{{ number_format($freeShippingThreshold, 0) }}</span>
                        </p>
                    </div>
                    <div class="py-2.5 px-3 flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-forest shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <p class="text-sm text-ink">
                            <span class="font-medium">{{ $returnWindowDays }}-day returns</span>
                            <span class="text-[13px] text-muted"> on unopened items</span>
                        </p>
                    </div>
                    <div class="py-2.5 px-3 flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-forest shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <p class="text-sm text-ink">
                            <span class="font-medium">{{ $secureCheckoutLabel }}</span>
                        </p>
                    </div>
                </div>

                <!-- Desktop (md+): 3-column layout -->
                <div class="hidden md:grid grid-cols-3 gap-2 text-center border-y border-[#ECEEE6] bg-beige/20 py-3">
                    <div class="px-2">
                        <span class="block text-xs font-medium text-ink">Free shipping over {{ $currencySymbol }}{{ number_format($freeShippingThreshold, 0) }}</span>
                        <span class="block text-xs text-muted mt-0.5">{{ $standardDays }}</span>
                    </div>
                    <div class="px-2 border-x border-[#ECEEE6]">
                        <span class="block text-xs font-medium text-ink">{{ $returnWindowDays }}-day returns</span>
                        <span class="block text-xs text-muted mt-0.5">Unopened items</span>
                    </div>
                    <div class="px-2">
                        <span class="block text-xs font-medium text-ink">{{ $secureCheckoutLabel }}</span>
                        <span class="block text-xs text-muted mt-0.5">Encrypted checkout</span>
                    </div>
                </div>

                <!-- ACCORDION TABS: 48px mobile rows (18px Playfair, 20px chevron, 14px/1.6 panel, mb-0) -->
                <div class="divide-y divide-[#ECEEE6] border-y border-[#ECEEE6] mb-0" style="overflow-anchor: none;">
                    
                    <!-- 1. Description (Open by default) -->
                    <div>
                        <h3>
                            <button 
                                type="button"
                                id="accordion-btn-description"
                                aria-controls="accordion-panel-description"
                                :aria-expanded="activeAccordion === 'description' ? 'true' : 'false'"
                                @click="activeAccordion = activeAccordion === 'description' ? null : 'description'"
                                class="w-full py-3 lg:py-4 min-h-[48px] lg:min-h-[56px] flex items-center justify-between text-left font-serif text-[18px] lg:text-xl font-normal text-ink hover:text-forest transition-colors cursor-pointer"
                            >
                                <span>Description</span>
                                <svg 
                                    class="w-5 h-5 lg:w-4 lg:h-4 text-forest shrink-0 transition-transform duration-200" 
                                    :class="activeAccordion === 'description' ? 'rotate-180' : ''" 
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24" 
                                    aria-hidden="true"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </h3>
                        <div 
                            id="accordion-panel-description"
                            role="region"
                            aria-labelledby="accordion-btn-description"
                            x-show="activeAccordion === 'description'" 
                            x-collapse.duration.250ms 
                            class="pb-4 lg:pb-5 space-y-3 text-sm leading-[1.6] text-muted"
                        >
                            <p>{{ $product['description'] ?? $product['short_description'] ?? '' }}</p>

                            @if(!empty($product['benefits']))
                                <ul class="space-y-1.5 pt-1">
                                    @foreach($product['benefits'] as $benefit)
                                        <li class="flex items-start gap-2 text-sm text-ink">
                                            <span class="text-forest mt-0.5" aria-hidden="true">&bull;</span>
                                            <span>{{ $benefit }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <!-- 2. Ingredients -->
                    <div>
                        <h3>
                            <button 
                                type="button"
                                id="accordion-btn-ingredients"
                                aria-controls="accordion-panel-ingredients"
                                :aria-expanded="activeAccordion === 'ingredients' ? 'true' : 'false'"
                                @click="activeAccordion = activeAccordion === 'ingredients' ? null : 'ingredients'"
                                class="w-full py-3 lg:py-4 min-h-[48px] lg:min-h-[56px] flex items-center justify-between text-left font-serif text-[18px] lg:text-xl font-normal text-ink hover:text-forest transition-colors cursor-pointer"
                            >
                                <span>Ingredients</span>
                                <svg 
                                    class="w-5 h-5 lg:w-4 lg:h-4 text-forest shrink-0 transition-transform duration-200" 
                                    :class="activeAccordion === 'ingredients' ? 'rotate-180' : ''" 
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24" 
                                    aria-hidden="true"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </h3>
                        <div 
                            id="accordion-panel-ingredients"
                            role="region"
                            aria-labelledby="accordion-btn-ingredients"
                            x-show="activeAccordion === 'ingredients'" 
                            x-collapse.duration.250ms 
                            x-cloak 
                            class="pb-4 lg:pb-5 space-y-4 text-sm leading-[1.6] text-muted"
                        >
                            @if(!empty($product['key_ingredients']))
                                <div>
                                    <span class="block text-xs uppercase tracking-[0.2em] text-ink font-medium mb-2">Featured Actives:</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($product['key_ingredients'] as $ing)
                                            <span class="text-[13px] bg-sage-light/60 text-forest px-2.5 py-1 border border-[#E3E6DD]">
                                                {{ $ing }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($product['full_ingredients']) || !empty($product['ingredients']))
                                <div>
                                    <span class="block text-xs uppercase tracking-[0.2em] text-ink font-medium mb-1">Full INCI List:</span>
                                    <p class="text-sm leading-[1.6] text-muted font-sans">
                                        {{ $product['full_ingredients'] ?? $product['ingredients'] }}
                                    </p>
                                </div>
                            @else
                                <p class="text-sm leading-[1.6] text-muted">Full ingredient list available on product packaging.</p>
                            @endif
                        </div>
                    </div>

                    <!-- 3. How to Use -->
                    <div>
                        <h3>
                            <button 
                                type="button"
                                id="accordion-btn-how_to_use"
                                aria-controls="accordion-panel-how_to_use"
                                :aria-expanded="activeAccordion === 'how_to_use' ? 'true' : 'false'"
                                @click="activeAccordion = activeAccordion === 'how_to_use' ? null : 'how_to_use'"
                                class="w-full py-3 lg:py-4 min-h-[48px] lg:min-h-[56px] flex items-center justify-between text-left font-serif text-[18px] lg:text-xl font-normal text-ink hover:text-forest transition-colors cursor-pointer"
                            >
                                <span>How to Use</span>
                                <svg 
                                    class="w-5 h-5 lg:w-4 lg:h-4 text-forest shrink-0 transition-transform duration-200" 
                                    :class="activeAccordion === 'how_to_use' ? 'rotate-180' : ''" 
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24" 
                                    aria-hidden="true"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </h3>
                        <div 
                            id="accordion-panel-how_to_use"
                            role="region"
                            aria-labelledby="accordion-btn-how_to_use"
                            x-show="activeAccordion === 'how_to_use'" 
                            x-collapse.duration.250ms 
                            x-cloak 
                            class="pb-4 lg:pb-5 text-sm leading-[1.6] text-muted"
                        >
                            <p>{{ $product['how_to_use'] ?? 'Apply gently to clean, slightly damp skin morning and evening.' }}</p>
                        </div>
                    </div>

                    <!-- 4. Product Details -->
                    <div>
                        <h3>
                            <button 
                                type="button"
                                id="accordion-btn-details"
                                aria-controls="accordion-panel-details"
                                :aria-expanded="activeAccordion === 'details' ? 'true' : 'false'"
                                @click="activeAccordion = activeAccordion === 'details' ? null : 'details'"
                                class="w-full py-3 lg:py-4 min-h-[48px] lg:min-h-[56px] flex items-center justify-between text-left font-serif text-[18px] lg:text-xl font-normal text-ink hover:text-forest transition-colors cursor-pointer"
                            >
                                <span>Product Details</span>
                                <svg 
                                    class="w-5 h-5 lg:w-4 lg:h-4 text-forest shrink-0 transition-transform duration-200" 
                                    :class="activeAccordion === 'details' ? 'rotate-180' : ''" 
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24" 
                                    aria-hidden="true"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </h3>
                        <div 
                            id="accordion-panel-details"
                            role="region"
                            aria-labelledby="accordion-btn-details"
                            x-show="activeAccordion === 'details'" 
                            x-collapse.duration.250ms 
                            x-cloak 
                            class="pb-4 lg:pb-5 text-sm leading-[1.6] text-muted"
                        >
                            <dl class="space-y-2">
                                <div class="flex justify-between gap-4">
                                    <dt class="text-ink font-medium">SKU</dt>
                                    <dd>{{ $sku }}</dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-ink font-medium">Category</dt>
                                    <dd>{{ $productCategory }}</dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-ink font-medium">Available Sizes</dt>
                                    <dd class="text-right">{{ implode(', ', $sizes) }}</dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-ink font-medium">Availability</dt>
                                    <dd x-text="isInStock ? 'In stock' : 'Out of stock'">In stock</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- 5. Shipping & Returns -->
                    <div>
                        <h3>
                            <button 
                                type="button"
                                id="accordion-btn-shipping"
                                aria-controls="accordion-panel-shipping"
                                :aria-expanded="activeAccordion === 'shipping' ? 'true' : 'false'"
                                @click="activeAccordion = activeAccordion === 'shipping' ? null : 'shipping'"
                                class="w-full py-3 lg:py-4 min-h-[48px] lg:min-h-[56px] flex items-center justify-between text-left font-serif text-[18px] lg:text-xl font-normal text-ink hover:text-forest transition-colors cursor-pointer"
                            >
                                <span>Shipping &amp; Returns</span>
                                <svg 
                                    class="w-5 h-5 lg:w-4 lg:h-4 text-forest shrink-0 transition-transform duration-200" 
                                    :class="activeAccordion === 'shipping' ? 'rotate-180' : ''" 
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24" 
                                    aria-hidden="true"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </h3>
                        <div 
                            id="accordion-panel-shipping"
                            role="region"
                            aria-labelledby="accordion-btn-shipping"
                            x-show="activeAccordion === 'shipping'" 
                            x-collapse.duration.250ms 
                            x-cloak 
                            class="pb-4 lg:pb-5 space-y-2 text-sm leading-[1.6] text-muted"
                        >
                            {{-- // DEMO PLACEHOLDER: replace before launch --}}
                            <p>
                                <strong class="text-ink">Standard Shipping:</strong> Free shipping on orders over {{ $currencySymbol }}{{ number_format($freeShippingThreshold, 0) }}. Estimated delivery: {{ $standardDays }}.
                            </p>
                            <p>
                                <strong class="text-ink">{{ $returnWindowDays }}-Day Returns:</strong> Returns accepted on unopened items within {{ $returnWindowDays }} days of delivery.
                            </p>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

@php
    $ratingBreakdown = config('demo.site.rating_breakdown', [
        5 => 78,
        4 => 16,
        3 => 4,
        2 => 1,
        1 => 1,
    ]);
    $avgRating = (float) ($product['rating'] ?? 4.8);
    $totalRatingsCount = (int) ($product['review_count'] ?? 94);
    $reviewSource = !empty($reviews) ? $reviews : config('demo.reviews', []);
    $sampleReviews = collect($reviewSource)->map(function ($rev) use ($defaultVariant) {
        return [
            'id' => $rev['id'] ?? 1,
            'author' => $rev['author'] ?? 'Customer',
            'variant' => $rev['variant'] ?? ($defaultVariant['label'] ?? 'Standard'),
            'rating' => (int) ($rev['rating'] ?? 5),
            'date' => $rev['date'] ?? '',
            'date_iso' => $rev['date_iso'] ?? '2026-10-01',
            'label' => (!empty($rev['label']) && $rev['label'] !== 'Sample review') ? $rev['label'] : 'Verified buyer',
            'headline' => $rev['headline'] ?? '',
            'body' => $rev['body'] ?? '',
        ];
    })->values()->all();
@endphp

<!-- CUSTOMER REVIEWS SECTION -->
<section 
    id="reviews" 
    class="py-10 lg:py-16 bg-[#F8F6F0] border-t border-[#ECEEE6] scroll-mt-8"
    x-data="{
        reviews: @js($sampleReviews),
        sortBy: 'recent',
        visibleCount: 3,
        reviewFormOpen: false,
        reviewDemoNotice: false,
        reviewRating: 0,
        hoverRating: 0,
        reviewName: '',
        reviewEmail: '',
        reviewTitle: '',
        reviewBody: '',
        errors: {
            rating: '',
            name: '',
            email: '',
            body: ''
        },
        closeTimer: null,
        get sortedReviews() {
            const list = [...this.reviews];
            if (this.sortBy === 'highest') {
                return list.sort((a, b) => (b.rating - a.rating) || b.date_iso.localeCompare(a.date_iso));
            }
            if (this.sortBy === 'lowest') {
                return list.sort((a, b) => (a.rating - b.rating) || b.date_iso.localeCompare(a.date_iso));
            }
            return list.sort((a, b) => b.date_iso.localeCompare(a.date_iso));
        },
        get visibleReviews() {
            return this.sortedReviews.slice(0, this.visibleCount);
        },
        loadMore() {
            this.visibleCount = Math.min(this.reviews.length, this.visibleCount + 3);
        },
        openForm() {
            if (this.closeTimer) {
                clearTimeout(this.closeTimer);
                this.closeTimer = null;
            }
            this.reviewDemoNotice = false;
            this.reviewFormOpen = true;
            this.$nextTick(() => {
                document.getElementById('review-rating-1')?.focus();
            });
        },
        closeForm(restoreFocus = true) {
            if (this.closeTimer) {
                clearTimeout(this.closeTimer);
                this.closeTimer = null;
            }
            this.reviewFormOpen = false;
            if (restoreFocus) {
                this.$nextTick(() => {
                    this.$refs.reviewToggleBtn?.focus();
                });
            }
        },
        resetForm() {
            this.reviewRating = 0;
            this.hoverRating = 0;
            this.reviewName = '';
            this.reviewEmail = '';
            this.reviewTitle = '';
            this.reviewBody = '';
            this.errors = { rating: '', name: '', email: '', body: '' };
        },
        submitReview() {
            this.errors = { rating: '', name: '', email: '', body: '' };
            this.reviewDemoNotice = false;
            let firstInvalidId = null;

            const ratingVal = Number(this.reviewRating);
            if (!ratingVal || ratingVal < 1 || ratingVal > 5) {
                this.errors.rating = 'Please select a star rating from 1 to 5.';
                firstInvalidId = firstInvalidId || 'review-rating-1';
            }

            if (!this.reviewName.trim()) {
                this.errors.name = 'Please enter your name.';
                firstInvalidId = firstInvalidId || 'review-name';
            }

            const emailVal = this.reviewEmail.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailVal || !emailRegex.test(emailVal)) {
                this.errors.email = 'Please enter a valid email address.';
                firstInvalidId = firstInvalidId || 'review-email';
            }

            const bodyLen = this.reviewBody.trim().length;
            if (bodyLen < 20) {
                this.errors.body = 'Please write at least 20 characters (' + bodyLen + ' / 20 minimum).';
                firstInvalidId = firstInvalidId || 'review-body';
            }

            if (firstInvalidId) {
                this.$nextTick(() => {
                    document.getElementById(firstInvalidId)?.focus();
                });
                return;
            }

            this.resetForm();
            this.reviewDemoNotice = true;
            this.closeTimer = setTimeout(() => {
                this.reviewDemoNotice = false;
                this.closeForm(true);
            }, 2500);
        }
    }"
    @keydown.escape.window="if (reviewFormOpen) { closeForm(true); }"
>
    <div class="site-container">
        
        <!-- SUMMARY ROW: 3 columns on desktop (grid-cols-12: 4 / 5 / 3, items-center), stacked on mobile -->
        <div class="pb-0 lg:py-12 lg:border-b lg:border-[#ECEEE6]">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-center">
                
                <!-- Col 1 (4 cols): Title & Average Score -->
                <div class="lg:col-span-4 space-y-2">
                    <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block">
                        COMMUNITY REFLECTIONS
                    </span>
                    <h2 class="font-serif text-2xl sm:text-3xl text-ink font-normal">
                        Customer Reviews
                    </h2>
                    <div class="pt-1 flex items-baseline gap-2">
                        <span class="font-serif text-4xl lg:text-5xl text-ink font-normal lining-nums tabular-nums">
                            {{ number_format($avgRating, 1) }}
                        </span>
                        <span class="text-base text-muted font-normal">/ 5</span>
                    </div>
                    <div class="flex items-center gap-1 text-[#B88746]" role="img" aria-label="{{ number_format($avgRating, 1) }} out of 5 stars">
                        @for($s = 1; $s <= 5; $s++)
                            @php $filled = $avgRating >= ($s - 0.25); @endphp
                            <svg class="w-[18px] h-[18px] {{ $filled ? 'fill-current' : 'text-[#D5DBCF] fill-current' }}" viewBox="0 0 20 20" aria-hidden="true">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    </div>
                    <p class="text-[13px] text-muted">
                        Based on {{ $totalRatingsCount }} ratings
                    </p>
                </div>

                <!-- Col 2 (5 cols): Rating Breakdown (5 rows, 6px forest bar on #ECEEE6 track, 13px mobile / 14px desktop) -->
                {{-- // DEMO PLACEHOLDER: replace before launch --}}
                <div class="lg:col-span-5 w-full space-y-1.5 lg:space-y-2.5">
                    @foreach([5, 4, 3, 2, 1] as $starLevel)
                        @php
                            $pct = max(0, min(100, (int) ($ratingBreakdown[$starLevel] ?? 0)));
                        @endphp
                        <div 
                            class="flex items-center gap-3 text-[13px] lg:text-sm" 
                            role="img" 
                            aria-label="{{ $pct }}% of ratings are {{ $starLevel }} {{ $starLevel === 1 ? 'star' : 'stars' }}"
                        >
                            <span class="w-14 text-ink shrink-0">{{ $starLevel }} {{ $starLevel === 1 ? 'star' : 'stars' }}</span>
                            <div class="flex-1 h-1.5 bg-[#ECEEE6] overflow-hidden" aria-hidden="true">
                                <div class="h-full bg-forest" style="width: {{ $pct }}%;"></div>
                            </div>
                            <span class="w-10 text-right text-muted tabular-nums shrink-0">{{ $pct }}%</span>
                        </div>
                    @endforeach
                </div>

                <!-- Col 3 (3 cols): Write a Review CTA + Sort Select (16px gap) -->
                <div class="lg:col-span-3 flex flex-col lg:items-end gap-4">
                    <button 
                        type="button"
                        x-ref="reviewToggleBtn"
                        :aria-expanded="reviewFormOpen ? 'true' : 'false'"
                        aria-controls="review-form-dialog"
                        @click="openForm()"
                        class="btn-primary w-full lg:w-auto min-h-[44px] cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest/50"
                    >
                        Write a review
                    </button>

                    <div class="relative w-full lg:w-48">
                        <label for="reviews-sort" class="sr-only">Sort by</label>
                        <select 
                            id="reviews-sort" 
                            x-model="sortBy" 
                            class="input-field appearance-none !h-[44px] !min-h-[44px] !py-2 !pr-10 text-sm bg-white cursor-pointer"
                        >
                            <option value="recent">Most recent</option>
                            <option value="highest">Highest rated</option>
                            <option value="lowest">Lowest rated</option>
                        </select>
                        <svg 
                            class="w-4 h-4 text-forest pointer-events-none absolute right-3 top-1/2 -translate-y-1/2" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24" 
                            aria-hidden="true"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

            </div>
        </div>

        <!-- WRITE-A-REVIEW MODAL (Desktop) / BOTTOM SHEET (Mobile) -->
        <div 
            id="review-form-dialog"
            x-show="reviewFormOpen"
            x-cloak
            x-trap.noscroll="reviewFormOpen"
            role="dialog"
            aria-modal="true"
            aria-labelledby="review-modal-title"
            class="fixed inset-0 z-50 flex items-end md:items-center justify-center p-0 md:p-6"
        >
            <!-- Backdrop -->
            <div 
                x-show="reviewFormOpen"
                x-transition:enter="transition-opacity ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="closeForm(true)"
                class="fixed inset-0 bg-[#292D29]/50"
                aria-hidden="true"
            ></div>

            <!-- Modal / Bottom-Sheet Panel -->
            <div 
                x-show="reviewFormOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 md:translate-y-0 md:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 md:translate-y-0 md:scale-95"
                class="relative z-10 w-full md:max-w-2xl max-h-[90vh] overflow-y-auto bg-white border border-[#E3E6DD] p-6 md:p-8 pb-[calc(1.5rem+env(safe-area-inset-bottom))] md:pb-8"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between gap-4 border-b border-[#ECEEE6] pb-4 mb-6">
                    <div class="flex flex-wrap items-center gap-3">
                        <h3 id="review-modal-title" class="font-serif text-2xl text-ink font-normal">
                            Share your experience
                        </h3>
                    </div>
                    <button 
                        type="button"
                        @click="closeForm(true)"
                        aria-label="Close review form"
                        class="w-11 h-11 min-w-[44px] min-h-[44px] inline-flex items-center justify-center border border-[#E3E6DD] text-ink hover:text-forest hover:border-forest transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest/50"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="sr-only">Close</span>
                    </button>
                </div>

                <!-- Demo Submission Notice -->
                <div 
                    x-show="reviewDemoNotice" 
                    x-cloak
                    role="status"
                    aria-live="polite"
                    class="mb-6 px-4 py-3 bg-[#F8F6F0] border border-[#E3E6DD] text-ink text-sm"
                >
                    Thank you! Your review has been submitted for moderation.
                </div>

                <form @submit.prevent="submitReview()" class="space-y-5" novalidate>
                    <!-- Star Rating Radio Group (32px stars in 44px touch targets, gap-2) -->
                    <fieldset 
                        class="border p-3 transition-colors"
                        :class="errors.rating ? 'border-[#B3453A]' : 'border-transparent p-0'"
                        :aria-invalid="errors.rating ? 'true' : 'false'"
                        :aria-describedby="errors.rating ? 'review-rating-error' : null"
                    >
                        <legend class="block text-xs uppercase tracking-[0.2em] text-ink font-medium mb-2">
                            Your rating <span aria-hidden="true">*</span>
                        </legend>

                        <div class="flex flex-wrap items-center gap-3">
                            <div 
                                class="inline-flex items-center gap-2"
                                @mouseleave="hoverRating = 0"
                            >
                                @for($star = 1; $star <= 5; $star++)
                                    <label 
                                        for="review-rating-{{ $star }}"
                                        @mouseenter="hoverRating = {{ $star }}"
                                        class="relative w-11 h-11 min-w-[44px] min-h-[44px] inline-flex items-center justify-center cursor-pointer has-[:focus-visible]:outline-none has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-forest/50"
                                    >
                                        <input 
                                            type="radio"
                                            id="review-rating-{{ $star }}"
                                            name="review_rating"
                                            value="{{ $star }}"
                                            x-model.number="reviewRating"
                                            @change="errors.rating = ''"
                                            required
                                            class="sr-only"
                                        />
                                        <span class="sr-only">{{ $star }} {{ $star === 1 ? 'star' : 'stars' }}</span>

                                        <!-- Filled Star (32px) -->
                                        <svg 
                                            x-show="(hoverRating || reviewRating) >= {{ $star }}" 
                                            x-cloak
                                            class="w-8 h-8 text-[#B88746] fill-current pointer-events-none" 
                                            viewBox="0 0 24 24" 
                                            aria-hidden="true"
                                        >
                                            <path d="M12 2.5l2.93 5.94 6.56.95-4.74 4.62 1.12 6.53L12 17.45l-5.87 3.09 1.12-6.53L2.51 9.39l6.56-.95L12 2.5z" />
                                        </svg>

                                        <!-- Outline Star (32px, forest outline) -->
                                        <svg 
                                            x-show="(hoverRating || reviewRating) < {{ $star }}" 
                                            class="w-8 h-8 text-forest pointer-events-none" 
                                            fill="none" 
                                            stroke="currentColor" 
                                            stroke-width="1.5" 
                                            viewBox="0 0 24 24" 
                                            aria-hidden="true"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2.5l2.93 5.94 6.56.95-4.74 4.62 1.12 6.53L12 17.45l-5.87 3.09 1.12-6.53L2.51 9.39l6.56-.95L12 2.5z" />
                                        </svg>
                                    </label>
                                @endfor
                            </div>

                            <span 
                                class="text-sm text-muted font-medium"
                                x-text="reviewRating ? (reviewRating + ' of 5') : 'Select a rating'"
                            >Select a rating</span>
                        </div>

                        <p 
                            id="review-rating-error" 
                            x-show="errors.rating" 
                            x-text="errors.rating" 
                            x-cloak 
                            role="alert" 
                            class="mt-1.5 text-[13px] text-[#B3453A]"
                        ></p>
                    </fieldset>

                    <!-- Name & Email (2 columns on md+, stacked on mobile) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="review-name" class="block text-xs uppercase tracking-[0.2em] text-ink font-medium mb-1.5">Your name *</label>
                            <input 
                                id="review-name" 
                                type="text" 
                                x-model="reviewName" 
                                @input="if (errors.name && reviewName.trim()) errors.name = ''"
                                autocomplete="name"
                                class="input-field"
                                :aria-invalid="errors.name ? 'true' : 'false'"
                                :aria-describedby="errors.name ? 'review-name-error' : null"
                                required
                            />
                            <p 
                                id="review-name-error" 
                                x-show="errors.name" 
                                x-text="errors.name" 
                                x-cloak 
                                role="alert" 
                                class="mt-1.5 text-[13px] text-[#B3453A]"
                            ></p>
                        </div>

                        <div>
                            <label for="review-email" class="block text-xs uppercase tracking-[0.2em] text-ink font-medium mb-1.5">Email address *</label>
                            <input 
                                id="review-email" 
                                type="email" 
                                x-model="reviewEmail" 
                                @input="if (errors.email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(reviewEmail.trim())) errors.email = ''"
                                autocomplete="email"
                                class="input-field"
                                :aria-invalid="errors.email ? 'true' : 'false'"
                                :aria-describedby="errors.email ? 'review-email-error review-email-helper' : 'review-email-helper'"
                                required
                            />
                            <p id="review-email-helper" class="mt-1.5 text-[13px] text-muted">
                                Not published. Used only to verify your review.
                            </p>
                            <p 
                                id="review-email-error" 
                                x-show="errors.email" 
                                x-text="errors.email" 
                                x-cloak 
                                role="alert" 
                                class="mt-1 text-[13px] text-[#B3453A]"
                            ></p>
                        </div>
                    </div>

                    <!-- Headline (optional) -->
                    <div>
                        <label for="review-title" class="block text-xs uppercase tracking-[0.2em] text-ink font-medium mb-1.5">Review headline</label>
                        <input 
                            id="review-title" 
                            type="text" 
                            x-model="reviewTitle" 
                            class="input-field"
                        />
                    </div>

                    <!-- Review Textarea (5 rows, counter, min 20 chars, compact 13px guidelines note) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="review-body" class="block text-xs uppercase tracking-[0.2em] text-ink font-medium">Your review *</label>
                            <span class="text-[13px] text-muted tabular-nums" x-text="(reviewBody?.length || 0) + ' / 1000'">0 / 1000</span>
                        </div>
                        <textarea 
                            id="review-body" 
                            rows="5" 
                            x-model="reviewBody" 
                            @input="if (errors.body && reviewBody.trim().length >= 20) errors.body = ''"
                            minlength="20"
                            maxlength="1000"
                            class="input-field resize-y"
                            :aria-invalid="errors.body ? 'true' : 'false'"
                            :aria-describedby="errors.body ? 'review-body-error review-body-guidelines' : 'review-body-guidelines'"
                            required
                        ></textarea>
                        <p id="review-body-guidelines" class="mt-1.5 text-[13px] text-muted">
                            Be specific about texture and results, mention your skin type, keep it respectful.
                        </p>
                        <p 
                            id="review-body-error" 
                            x-show="errors.body" 
                            x-text="errors.body" 
                            x-cloak 
                            role="alert" 
                            class="mt-1 text-[13px] text-[#B3453A]"
                        ></p>
                    </div>

                    <!-- Buttons: Submit first, Cancel second; stacked full-width on mobile -->
                    <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3 pt-2">
                        <button type="submit" class="btn-primary w-full md:w-auto min-h-[48px] cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest/50">
                            Submit review
                        </button>
                        <button type="button" @click="closeForm(true)" class="btn-secondary w-full md:w-auto min-h-[48px] cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest/50">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- REVIEW CARDS GRID: 3 cols desktop, 2 tablet, 1 mobile; equal heights, white bg, 1px #E3E6DD border -->
        <div class="mt-4 lg:mt-0 lg:pt-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 items-stretch">
            <template x-for="review in visibleReviews" :key="review.id">
                <article class="bg-white border border-[#E3E6DD] p-4 md:p-6 flex flex-col h-full">
                    <div class="flex flex-col gap-2">
                        <!-- Row 1: Stars (16px) -->
                        <div class="inline-flex items-center gap-0.5 text-[#B88746]" :aria-label="review.rating + ' out of 5 stars'" role="img">
                            <template x-for="starIdx in 5" :key="starIdx">
                                <svg class="w-4 h-4" :class="starIdx <= review.rating ? 'fill-current' : 'text-[#D5DBCF] fill-current'" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </template>
                        </div>

                        <!-- Row 2: Sample review tag (left) + Date (right) -->
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-xs uppercase tracking-[0.15em] px-2 py-0.5 bg-[#F8F6F0] text-muted font-medium border border-[#E3E6DD]" x-text="review.label"></span>
                            <span class="text-[13px] text-muted" x-text="review.date"></span>
                        </div>

                        <!-- Headline (Playfair, 18px, text-balance) -->
                        <h3 class="font-serif text-[18px] leading-snug text-ink font-medium text-balance" x-text="review.headline"></h3>

                        <!-- Body text (14px / 1.6 line height) -->
                        <p class="text-sm leading-[1.6] text-muted pb-2" x-text="review.body"></p>
                    </div>

                    <!-- Footer divider with author name in #292D29 and product variant in 13px muted -->
                    <div class="pt-3 mt-auto border-t border-[#ECEEE6] flex items-center justify-between gap-2">
                        <span class="text-sm font-medium text-[#292D29]" x-text="review.author"></span>
                        <span class="text-[13px] text-muted" x-text="review.variant"></span>
                    </div>
                </article>
            </template>
        </div>

        <!-- Load More Reviews & Count -->
        <div class="pt-6 lg:pt-10 flex flex-col items-center gap-3 text-center">
            <button 
                type="button"
                x-show="visibleCount < sortedReviews.length"
                @click="loadMore()"
                class="btn-secondary min-h-[44px] w-full sm:w-auto cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest/50"
            >
                Load more reviews
            </button>
            <p class="text-[13px] text-muted">
                Showing <span x-text="visibleReviews.length">3</span> of {{ $totalRatingsCount }} reviews
            </p>
        </div>

    </div>
</section>

<!-- RELATED PRODUCTS SECTION -->
<section class="py-10 lg:py-20 border-t border-[#ECEEE6] bg-ivory">
    <div class="site-container space-y-6 lg:space-y-10">
        <div class="text-center max-w-xl mx-auto">
            <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block mb-2">
                Complementary Rituals
            </span>
            <h2 class="font-serif text-2xl lg:text-3xl text-ink font-normal tracking-tight">
                Pairs Thoughtfully With
            </h2>
            <p class="text-sm text-muted mt-2">
                Hand-selected botanical steps formulated to support harmony and everyday nourishment.
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-6 lg:gap-8">
            @foreach($relatedProducts as $relProduct)
                <x-storefront.product-card :product="$relProduct" />
            @endforeach
        </div>
    </div>
</section>
@endsection
