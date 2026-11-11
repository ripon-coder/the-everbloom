@extends('layouts.app')

@section('title', $product['name'] . ' | The Everbloom')
@section('meta_description', $product['short_description'])

@php
    $allProducts = collect(config('demo.products', []));
    $categories = collect(config('demo.categories', []));
    $categoryObj = $categories->firstWhere('name', $product['category']);
    $categoryUrl = $categoryObj ? '/shop/category/' . $categoryObj['slug'] : '/shop';

    // Related products: 4 items from the same category, or fallback to featured products
    $sameCategory = $allProducts
        ->where('category', $product['category'])
        ->where('id', '!=', $product['id'])
        ->values();

    if ($sameCategory->count() < 4) {
        $needed = 4 - $sameCategory->count();
        $excludeIds = $sameCategory->pluck('id')->push($product['id'])->all();
        $fallbacks = $allProducts->whereNotIn('id', $excludeIds)->take($needed);
        $relatedProducts = $sameCategory->concat($fallbacks)->values()->all();
    } else {
        $relatedProducts = $sameCategory->take(4)->values()->all();
    }

    $images = !empty($product['images']) ? $product['images'] : ['products/placeholder.jpg'];
    $sizes = !empty($product['sizes']) ? $product['sizes'] : ['Standard'];
    $sku = $product['sku'] ?? ('EB-' . str_pad((string)($product['id'] ?? 1), 3, '0', STR_PAD_LEFT));
    $isOnSale = !empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price'];
    $productJson = htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8');

    // Prepare explicit variant options from config/demo/products.php variants or sizes fallback
    $basePrice = (float) $product['price'];
    $comparePrice = !empty($product['compare_at_price']) ? (float) $product['compare_at_price'] : null;
    $variants = [];
    if (!empty($product['variants']) && is_array($product['variants'])) {
        foreach ($product['variants'] as $v) {
            $vLabel = $v['size'] ?? $v['id'] ?? $v['label'] ?? $sizes[0];
            $variants[] = [
                'id' => $vLabel,
                'label' => $vLabel,
                'price' => (float) ($v['price'] ?? $basePrice),
                'compare_at_price' => !empty($v['compare_at_price']) ? (float) $v['compare_at_price'] : null,
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
            ];
        }
    }
    $variantsJson = htmlspecialchars(json_encode($variants), ENT_QUOTES, 'UTF-8');

    // Structured Data (Schema.org Product)
    $productSchema = [
        '@context' => 'https://schema.org/',
        '@type' => 'Product',
        'name' => $product['name'],
        'description' => $product['short_description'],
        'sku' => $sku,
        'brand' => [
            '@type' => 'Brand',
            'name' => 'The Everbloom',
        ],
        'offers' => [
            '@type' => 'Offer',
            'url' => url()->current(),
            'priceCurrency' => 'USD',
            'price' => number_format((float)$product['price'], 2, '.', ''),
            'availability' => ($product['stock_status'] ?? 'in_stock') === 'in_stock'
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
        ],
        'aggregateRating' => [
            '@type' => 'AggregateRating',
            'ratingValue' => $product['rating'],
            'reviewCount' => $product['review_count'],
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
        images: {{ htmlspecialchars(json_encode($images), ENT_QUOTES, 'UTF-8') }},
        variants: {{ $variantsJson }},
        selectedSize: '{{ $sizes[0] }}',
        quantity: 1,
        adding: false,
        activeAccordion: 'description',
        touchStartX: 0,
        touchEndX: 0,
        get currentVariant() {
            return this.variants.find(v => v.id === this.selectedSize) || this.variants[0];
        },
        nextImage() {
            this.activeImageIndex = (this.activeImageIndex + 1) % this.images.length;
        },
        prevImage() {
            this.activeImageIndex = (this.activeImageIndex - 1 + this.images.length) % this.images.length;
        },
        handleTouchStart(e) {
            this.touchStartX = e.changedTouches[0].screenX;
        },
        handleTouchEnd(e) {
            this.touchEndX = e.changedTouches[0].screenX;
            if (this.touchStartX - this.touchEndX > 40) {
                this.nextImage();
            } else if (this.touchEndX - this.touchStartX > 40) {
                this.prevImage();
            }
        },
        addToCart() {
            this.adding = true;
            $store.cart.add({
                id: '{{ $product['id'] }}',
                variantId: this.selectedSize
            }, this.quantity, this.selectedSize, true);
            setTimeout(() => { this.adding = false; }, 900);
        }
    }"
    @qty-change.window="quantity = $event.detail"
    class="bg-ivory pb-16 md:pb-20"
>
    <!-- Top Breadcrumb Bar -->
    <div class="site-container border-b border-[#ECEEE6]">
        <x-storefront.breadcrumbs :items="[
            ['label' => 'Shop', 'url' => '/shop'],
            ['label' => $product['category'], 'url' => $categoryUrl],
            ['label' => $product['name']]
        ]" />
    </div>

    <!-- Main Product Detail Grid -->
    <div class="site-container pt-6 md:pt-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">
            
            <!-- LEFT COLUMN: Sticky Image Gallery Only (7 Cols on lg, sticky top-6 since header is static) -->
            <div class="lg:col-span-7 lg:sticky lg:top-6">
                <div class="flex flex-col-reverse sm:flex-row gap-4">
                    
                    <!-- Square Thumbnail Rail (Horizontal on mobile, Vertical on sm+) -->
                    @if(count($images) > 1)
                        <div class="flex sm:flex-col gap-3 overflow-x-auto sm:overflow-visible pb-1 sm:pb-0 shrink-0 sm:w-20" role="tablist" aria-label="Product gallery thumbnails">
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
                                    :class="activeImageIndex === {{ $index }} ? 'border-forest opacity-100 shadow-[0_0_0_2px_rgba(48,76,61,0.15)]' : 'border-[#E3E6DD] opacity-70 hover:opacity-100 hover:border-[#A8B7A0]'"
                                    class="relative w-16 h-16 sm:w-20 sm:h-20 aspect-square shrink-0 border transition-all duration-200 overflow-hidden bg-sage-light/30 cursor-pointer focus-visible:outline-none"
                                    aria-label="View product image {{ $index + 1 }} of {{ count($images) }}"
                                >
                                    <x-storefront.image 
                                        :src="$imgSrc" 
                                        :alt="$product['name'] . ' thumbnail ' . ($index + 1)" 
                                        aspect-ratio="aspect-square"
                                        width="160"
                                        height="160"
                                        loading="eager"
                                    />
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <!-- Main Active Image Stage (Swipeable on touch devices) -->
                    <div 
                        class="relative flex-1 bg-sage-light/30 border border-[#E3E6DD] overflow-hidden"
                        @touchstart.passive="handleTouchStart($event)"
                        @touchend.passive="handleTouchEnd($event)"
                    >
                        @foreach($images as $index => $imgSrc)
                            <div 
                                x-show="activeImageIndex === {{ $index }}" 
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                @if($index > 0) x-cloak @endif
                            >
                                <x-storefront.image 
                                    :src="$imgSrc" 
                                    :alt="$product['name'] . ' - Image ' . ($index + 1)" 
                                    aspect-ratio="aspect-[4/5]"
                                    width="960"
                                    height="1200"
                                    loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                />
                            </div>
                        @endforeach

                        <!-- Top-left Status / Sale Badges -->
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
                            <div class="absolute inset-x-3 top-1/2 -translate-y-1/2 flex items-center justify-between pointer-events-none sm:hidden z-10">
                                <button 
                                    type="button"
                                    @click="prevImage()"
                                    class="pointer-events-auto w-11 h-11 rounded-full bg-ivory/90 border border-[#E3E6DD] text-ink flex items-center justify-center shadow-sm"
                                    aria-label="Previous image"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button 
                                    type="button"
                                    @click="nextImage()"
                                    class="pointer-events-auto w-11 h-11 rounded-full bg-ivory/90 border border-[#E3E6DD] text-ink flex items-center justify-center shadow-sm"
                                    aria-label="Next image"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        @endif

                        <!-- Image Counter Pill on Mobile -->
                        @if(count($images) > 1)
                            <div class="absolute bottom-3 right-3 bg-ivory/90 backdrop-blur-xs border border-[#E3E6DD] px-2.5 py-1 text-xs font-sans text-ink sm:hidden">
                                <span x-text="activeImageIndex + 1">1</span> / {{ count($images) }}
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            <!-- RIGHT COLUMN: Non-Sticky Buy Box & Accordions (5 Cols on lg) -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Category & SKU Row -->
                <div class="flex items-center justify-between text-xs uppercase tracking-[0.2em] text-muted">
                    <a href="{{ $categoryUrl }}" class="text-forest font-medium hover:underline">
                        {{ $product['category'] }}
                    </a>
                    <span>SKU: {{ $sku }}</span>
                </div>

                <!-- Product Title &Subtitle -->
                <div>
                    <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal leading-tight">
                        {{ $product['name'] }}
                    </h1>
                    @if(!empty($product['subtitle']))
                        <p class="font-serif italic text-base sm:text-lg text-muted mt-1">
                            {{ $product['subtitle'] }}
                        </p>
                    @endif
                </div>

                <!-- Price & Rating Row -->
                <div class="flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-[#ECEEE6]">
                    <div class="inline-flex items-baseline gap-2.5">
                        <span class="price text-2xl text-ink" x-text="'$' + currentVariant.price.toFixed(2)">
                            ${{ number_format((float)$product['price'], 2) }}
                        </span>
                        <template x-if="currentVariant.compare_at_price && currentVariant.compare_at_price > currentVariant.price">
                            <span class="price text-sm text-muted line-through" x-text="'$' + currentVariant.compare_at_price.toFixed(2)"></span>
                        </template>
                    </div>

                    <a href="#reviews" class="inline-flex items-center gap-2 group">
                        <x-storefront.rating :rating="$product['rating']" :count="$product['review_count']" size="md" />
                        <span class="text-xs text-muted underline group-hover:text-forest transition-colors">
                            Read reviews
                        </span>
                    </a>
                </div>

                <!-- Short Editorial Description -->
                <!-- VERIFY CLAIM before launch -->
                <p class="text-base text-muted leading-relaxed">
                    {{ $product['short_description'] }}
                </p>

                <!-- Skin Types Tag List -->
                @if(!empty($product['skin_types']))
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs uppercase tracking-[0.2em] text-muted mr-1">Best For:</span>
                        @foreach($product['skin_types'] as $skinType)
                            <span class="text-xs bg-beige/60 text-ink px-2.5 py-1 border border-[#E3E6DD]">
                                {{ $skinType }} Skin
                            </span>
                        @endforeach
                    </div>
                @endif

                <!-- Size / Variant Selector (Explicit Price Per Variant, 44px height) -->
                <div class="pt-2 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span id="size-selector-label" class="text-xs uppercase tracking-[0.2em] font-medium text-ink">
                            Size: <span class="text-forest font-semibold" x-text="selectedSize">{{ $sizes[0] }}</span>
                        </span>
                        <span class="text-xs text-forest flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-forest"></span>
                            In Stock &middot; Ready to Ship
                        </span>
                    </div>

                    <div class="flex flex-wrap gap-2.5" role="radiogroup" aria-labelledby="size-selector-label">
                        <template x-for="variant in variants" :key="variant.id">
                            <button 
                                type="button"
                                role="radio"
                                :aria-checked="selectedSize === variant.id ? 'true' : 'false'"
                                @click="selectedSize = variant.id"
                                :class="selectedSize === variant.id 
                                    ? 'border-forest bg-forest text-ivory font-medium' 
                                    : 'border-[#D5DBCF] bg-ivory text-ink hover:border-[#A8B7A0]'"
                                class="px-4 py-2.5 min-h-[44px] text-xs tracking-wider uppercase border transition-colors cursor-pointer inline-flex items-center gap-2 focus-visible:outline-none"
                            >
                                <span x-text="variant.label"></span>
                                <span class="opacity-80">&mdash;</span>
                                <span class="price" x-text="'$' + variant.price.toFixed(2)"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Quantity + Add to Bag + Wishlist Row (Wraps cleanly at 320px so Add to Bag is never crushed) -->
                <div class="pt-3 flex flex-wrap sm:flex-nowrap items-stretch gap-3">
                    <!-- Quantity Stepper -->
                    <x-storefront.quantity-selector :quantity="1" class="shrink-0" />

                    <!-- Wishlist Toggle Button (shown alongside quantity on mobile, right-aligned on sm+) -->
                    <div class="sm:order-3 shrink-0">
                        <x-storefront.wishlist-button 
                            :product="$product" 
                            size="lg" 
                            class="!rounded-none !w-12 !h-11 !min-w-[44px] !min-h-[44px] !border-[#D5DBCF]" 
                        />
                    </div>

                    <!-- Primary Add to Bag CTA (Full width on narrow mobile, flex-1 on sm+) -->
                    <button 
                        type="button"
                        @click="addToCart()"
                        :disabled="adding"
                        class="w-full sm:w-auto sm:flex-1 sm:order-2 btn-primary min-h-[48px] sm:min-h-[44px] relative overflow-hidden cursor-pointer"
                    >
                        <span x-show="!adding" class="inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span>Add to Bag &mdash; <span class="price" x-text="'$' + (currentVariant.price * quantity).toFixed(2)">${{ number_format((float)$product['price'], 2) }}</span></span>
                        </span>

                        <span x-show="adding" x-cloak class="inline-flex items-center gap-2" aria-live="polite">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Added to Ritual Bag</span>
                        </span>
                    </button>
                </div>

                <!-- Micro Trust Highlights under CTA -->
                <div class="grid grid-cols-3 gap-2 pt-3 pb-2 text-center border-y border-[#ECEEE6] bg-beige/20 py-3">
                    <div class="px-2">
                        <span class="block text-xs uppercase tracking-[0.15em] font-medium text-ink">Vegan &amp; Clean</span>
                        <span class="block text-xs text-muted mt-0.5">Plant-derived</span>
                    </div>
                    <div class="px-2 border-x border-[#ECEEE6]">
                        <span class="block text-xs uppercase tracking-[0.15em] font-medium text-ink">Cruelty-Free</span>
                        <span class="block text-xs text-muted mt-0.5">Never animal tested</span>
                    </div>
                    <div class="px-2">
                        <span class="block text-xs uppercase tracking-[0.15em] font-medium text-ink">Free Shipping</span>
                        <span class="block text-xs text-muted mt-0.5">Orders over ${{ number_format((float) config('demo.site.shipping.free_shipping_threshold', 75), 0) }}</span>
                    </div>
                </div>

                <!-- ACCORDION TABS: Description (Open by default), Key Ingredients, How to Use, Shipping & Returns -->
                <div class="divide-y divide-[#ECEEE6] border-b border-[#ECEEE6] pt-2">
                    
                    <!-- 1. Description & Benefits (Open by default) -->
                    <div>
                        <h3>
                            <button 
                                type="button"
                                id="accordion-btn-description"
                                aria-controls="accordion-panel-description"
                                :aria-expanded="activeAccordion === 'description' ? 'true' : 'false'"
                                @click="activeAccordion = activeAccordion === 'description' ? null : 'description'"
                                class="w-full py-4 flex items-center justify-between text-left font-sans text-xs uppercase tracking-[0.2em] font-semibold text-ink hover:text-forest transition-colors cursor-pointer min-h-[44px]"
                            >
                                <span>Description &amp; Benefits</span>
                                <span class="text-lg font-light text-forest" x-text="activeAccordion === 'description' ? '−' : '+'" aria-hidden="true">−</span>
                            </button>
                        </h3>
                        <div 
                            id="accordion-panel-description"
                            role="region"
                            aria-labelledby="accordion-btn-description"
                            x-show="activeAccordion === 'description'" 
                            x-collapse 
                            class="pb-5 space-y-3 text-sm text-muted leading-relaxed"
                        >
                            <!-- VERIFY CLAIM before launch -->
                            <p>{{ $product['description'] }}</p>

                            @if(!empty($product['benefits']))
                                <ul class="space-y-1.5 pt-2">
                                    @foreach($product['benefits'] as $benefit)
                                        <li class="flex items-start gap-2 text-xs text-ink">
                                            <span class="text-forest mt-0.5" aria-hidden="true">&bull;</span>
                                            <span>{{ $benefit }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <!-- 2. Key Ingredients & Full INCI -->
                    <div>
                        <h3>
                            <button 
                                type="button"
                                id="accordion-btn-ingredients"
                                aria-controls="accordion-panel-ingredients"
                                :aria-expanded="activeAccordion === 'ingredients' ? 'true' : 'false'"
                                @click="activeAccordion = activeAccordion === 'ingredients' ? null : 'ingredients'"
                                class="w-full py-4 flex items-center justify-between text-left font-sans text-xs uppercase tracking-[0.2em] font-semibold text-ink hover:text-forest transition-colors cursor-pointer min-h-[44px]"
                            >
                                <span>Key Botanicals &amp; Full Ingredients</span>
                                <span class="text-lg font-light text-forest" x-text="activeAccordion === 'ingredients' ? '−' : '+'" aria-hidden="true">+</span>
                            </button>
                        </h3>
                        <div 
                            id="accordion-panel-ingredients"
                            role="region"
                            aria-labelledby="accordion-btn-ingredients"
                            x-show="activeAccordion === 'ingredients'" 
                            x-collapse 
                            x-cloak 
                            class="pb-5 space-y-4 text-sm text-muted leading-relaxed"
                        >
                            @if(!empty($product['key_ingredients']))
                                <div>
                                    <span class="block text-xs uppercase tracking-[0.2em] text-ink font-medium mb-2">Featured Actives:</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($product['key_ingredients'] as $ing)
                                            <span class="text-xs bg-sage-light/60 text-forest px-2.5 py-1 border border-[#E3E6DD]">
                                                {{ $ing }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($product['full_ingredients']) || !empty($product['ingredients']))
                                <div>
                                    <span class="block text-xs uppercase tracking-[0.2em] text-ink font-medium mb-1">Full INCI List:</span>
                                    <p class="text-xs text-muted leading-relaxed font-sans">
                                        {{ $product['full_ingredients'] ?? $product['ingredients'] }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 3. How to Use (Ritual Application) -->
                    <div>
                        <h3>
                            <button 
                                type="button"
                                id="accordion-btn-how_to_use"
                                aria-controls="accordion-panel-how_to_use"
                                :aria-expanded="activeAccordion === 'how_to_use' ? 'true' : 'false'"
                                @click="activeAccordion = activeAccordion === 'how_to_use' ? null : 'how_to_use'"
                                class="w-full py-4 flex items-center justify-between text-left font-sans text-xs uppercase tracking-[0.2em] font-semibold text-ink hover:text-forest transition-colors cursor-pointer min-h-[44px]"
                            >
                                <span>Ritual Application (How to Use)</span>
                                <span class="text-lg font-light text-forest" x-text="activeAccordion === 'how_to_use' ? '−' : '+'" aria-hidden="true">+</span>
                            </button>
                        </h3>
                        <div 
                            id="accordion-panel-how_to_use"
                            role="region"
                            aria-labelledby="accordion-btn-how_to_use"
                            x-show="activeAccordion === 'how_to_use'" 
                            x-collapse 
                            x-cloak 
                            class="pb-5 text-sm text-muted leading-relaxed"
                        >
                            <p>{{ $product['how_to_use'] ?? 'Apply gently to clean, slightly damp skin morning and evening.' }}</p>
                        </div>
                    </div>

                    <!-- 4. Shipping & Returns -->
                    <div>
                        <h3>
                            <button 
                                type="button"
                                id="accordion-btn-shipping"
                                aria-controls="accordion-panel-shipping"
                                :aria-expanded="activeAccordion === 'shipping' ? 'true' : 'false'"
                                @click="activeAccordion = activeAccordion === 'shipping' ? null : 'shipping'"
                                class="w-full py-4 flex items-center justify-between text-left font-sans text-xs uppercase tracking-[0.2em] font-semibold text-ink hover:text-forest transition-colors cursor-pointer min-h-[44px]"
                            >
                                <span>Shipping &amp; Returns</span>
                                <span class="text-lg font-light text-forest" x-text="activeAccordion === 'shipping' ? '−' : '+'" aria-hidden="true">+</span>
                            </button>
                        </h3>
                        <div 
                            id="accordion-panel-shipping"
                            role="region"
                            aria-labelledby="accordion-btn-shipping"
                            x-show="activeAccordion === 'shipping'" 
                            x-collapse 
                            x-cloak 
                            class="pb-5 space-y-2 text-xs text-muted leading-relaxed"
                        >
                            <!-- DEMO PLACEHOLDER: replace before launch -->
                            <p>
                                <strong class="text-ink">Complimentary Standard Shipping:</strong> Available on all orders of ${{ number_format((float) config('demo.site.shipping.free_shipping_threshold', 75), 0) }} or more. Standard delivery arrives within {{ config('demo.site.shipping.standard_days', '3–5 business days') }}.
                            </p>
                            <p>
                                <strong class="text-ink">{{ config('demo.site.returns.window_days', 30) }}-Day Return Policy:</strong> If a formulation isn't suited to your skin, contact our Care Team within {{ config('demo.site.returns.window_days', 30) }} days of delivery for a return or exchange.
                            </p>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<!-- CUSTOMER REVIEWS SECTION (Default state: "Write a Review" button; sample reviews top-aligned, min 12px text) -->
<section 
    id="reviews" 
    class="py-16 md:py-20 bg-beige/25 border-t border-[#ECEEE6] scroll-mt-8"
    x-data="{
        reviewFormOpen: false,
        reviewSubmitted: false,
        reviewRating: 5,
        reviewName: '',
        reviewEmail: '',
        reviewTitle: '',
        reviewBody: '',
        reviewError: '',
        submitReview() {
            if (!this.reviewName.trim() || !this.reviewEmail.trim() || !this.reviewBody.trim()) {
                this.reviewError = 'Please complete your name, email address, and review notes.';
                return;
            }
            this.reviewError = '';
            this.reviewSubmitted = true;
            this.reviewFormOpen = false;
        }
    }"
>
    <div class="site-container">
        
        <!-- Reviews Header & Summary -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-10 border-b border-[#ECEEE6]">
            <div>
                <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block mb-2">
                    Community Reflections
                </span>
                <h2 class="font-serif text-2xl sm:text-3xl text-ink font-normal">
                    Customer Reviews
                </h2>
                <div class="mt-3 flex items-center gap-3">
                    <x-storefront.rating :rating="$product['rating']" :showCount="false" size="md" />
                    <span class="text-sm font-medium text-ink">
                        {{ number_format((float)$product['rating'], 1) }} out of 5
                    </span>
                    <span class="text-xs text-muted">
                        Based on {{ $product['review_count'] }} ratings (Sample data)
                    </span>
                </div>
            </div>

            <!-- Default state shows "Write a Review" button, NOT "Review Submitted" -->
            <div>
                <button 
                    type="button"
                    x-show="!reviewSubmitted"
                    @click="reviewFormOpen = !reviewFormOpen"
                    class="btn-secondary min-h-[44px]"
                >
                    <span x-text="reviewFormOpen ? 'Cancel Review' : 'Write a Review'">Write a Review</span>
                </button>

                <div 
                    x-show="reviewSubmitted" 
                    x-cloak
                    role="status"
                    aria-live="polite"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-sage-light/60 border border-forest/30 text-forest text-xs uppercase tracking-[0.2em] font-medium"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Thank you — your review has been submitted</span>
                </div>
            </div>
        </div>

        <!-- Collapsible Write a Review Form -->
        <div x-show="reviewFormOpen" x-collapse x-cloak class="pt-8 pb-4 border-b border-[#ECEEE6]">
            <form @submit.prevent="submitReview()" class="max-w-2xl bg-ivory border border-[#E3E6DD] p-6 sm:p-8 space-y-5" novalidate>
                <div class="flex items-center justify-between border-b border-[#ECEEE6] pb-3">
                    <h3 class="font-serif text-xl text-ink font-normal">Share Your Ritual Experience</h3>
                    <span class="text-xs uppercase tracking-[0.2em] text-muted">Demo Mode</span>
                </div>

                <p x-show="reviewError" x-text="reviewError" x-cloak role="alert" class="text-xs text-rose-700 font-medium"></p>

                <!-- Rating Selector -->
                <div>
                    <span id="review-rating-label" class="block text-xs uppercase tracking-[0.2em] text-ink font-medium mb-2">Your Rating</span>
                    <div class="flex items-center gap-1" role="radiogroup" aria-labelledby="review-rating-label">
                        @for($star = 1; $star <= 5; $star++)
                            <button 
                                type="button"
                                role="radio"
                                :aria-checked="reviewRating === {{ $star }} ? 'true' : 'false'"
                                @click="reviewRating = {{ $star }}"
                                class="w-11 h-11 inline-flex items-center justify-center text-[#B88746] cursor-pointer focus-visible:outline-none"
                                aria-label="Rate {{ $star }} out of 5 stars"
                            >
                                <svg class="w-5 h-5" :class="reviewRating >= {{ $star }} ? 'fill-current' : 'text-beige fill-current'" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </button>
                        @endfor
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="review-name" class="block text-xs uppercase tracking-[0.2em] text-ink font-medium mb-1.5">Your Name *</label>
                        <input 
                            id="review-name" 
                            type="text" 
                            x-model="reviewName" 
                            autocomplete="name"
                            placeholder="Your name"
                            class="input-field"
                            required
                        />
                    </div>
                    <div>
                        <label for="review-email" class="block text-xs uppercase tracking-[0.2em] text-ink font-medium mb-1.5">Email Address *</label>
                        <input 
                            id="review-email" 
                            type="email" 
                            x-model="reviewEmail" 
                            autocomplete="email"
                            placeholder="name@example.com"
                            class="input-field"
                            required
                        />
                    </div>
                </div>

                <div>
                    <label for="review-title" class="block text-xs uppercase tracking-[0.2em] text-ink font-medium mb-1.5">Review Headline</label>
                    <input 
                        id="review-title" 
                        type="text" 
                        x-model="reviewTitle" 
                        placeholder="Summarize your experience"
                        class="input-field"
                    />
                </div>

                <div>
                    <label for="review-body" class="block text-xs uppercase tracking-[0.2em] text-ink font-medium mb-1.5">Your Review *</label>
                    <textarea 
                        id="review-body" 
                        rows="4" 
                        x-model="reviewBody" 
                        placeholder="How did this formulation feel on your skin?"
                        class="input-field"
                        required
                    ></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="reviewFormOpen = false" class="btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary">
                        Submit Review
                    </button>
                </div>
            </form>
        </div>

        <!-- Sample Reviews Grid (Top-aligned items-start, labeled "Sample review" at 12px text-xs, no "Verified" badge) -->
        <div class="pt-10 grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
            
            <!-- Sample Review 1 -->
            <div class="bg-ivory border border-[#E3E6DD] p-6 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs uppercase tracking-[0.2em] px-2.5 py-0.5 bg-beige/50 text-muted font-medium border border-[#E3E6DD]">
                            Sample review
                        </span>
                        <span class="text-xs text-muted">October 14, 2026</span>
                    </div>

                    <div class="flex items-center gap-1.5 text-[#B88746]">
                        <x-storefront.rating :rating="5" :showCount="false" size="sm" />
                    </div>

                    <h3 class="font-serif text-base text-ink font-medium">
                        "Calm, balanced hydration from the first week."
                    </h3>

                    <!-- VERIFY CLAIM before launch -->
                    <p class="text-xs text-muted leading-relaxed font-sans">
                        A truly delicate botanical infusion. After two weeks of morning application, my dry patches feel comfortably restored with no heavy residue underneath my daily sunscreen.
                    </p>
                </div>

                <div class="pt-4 mt-4 border-t border-[#ECEEE6] text-xs">
                    <span class="font-medium text-ink">Eleanor Vance</span>
                </div>
            </div>

            <!-- Sample Review 2 -->
            <div class="bg-ivory border border-[#E3E6DD] p-6 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs uppercase tracking-[0.2em] px-2.5 py-0.5 bg-beige/50 text-muted font-medium border border-[#E3E6DD]">
                            Sample review
                        </span>
                        <span class="text-xs text-muted">October 28, 2026</span>
                    </div>

                    <div class="flex items-center gap-1.5 text-[#B88746]">
                        <x-storefront.rating :rating="5" :showCount="false" size="sm" />
                    </div>

                    <h3 class="font-serif text-base text-ink font-medium">
                        "Subtle botanical aroma and pure comfort."
                    </h3>

                    <!-- VERIFY CLAIM before launch -->
                    <p class="text-xs text-muted leading-relaxed font-sans">
                        I am extremely cautious with fragrances and active oils. This formula feels gentle on reactive skin and leaves my face feeling nourished throughout long autumn working days.
                    </p>
                </div>

                <div class="pt-4 mt-4 border-t border-[#ECEEE6] text-xs">
                    <span class="font-medium text-ink">Clara Lindqvist</span>
                </div>
            </div>

            <!-- Sample Review 3 -->
            <div class="bg-ivory border border-[#E3E6DD] p-6 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs uppercase tracking-[0.2em] px-2.5 py-0.5 bg-beige/50 text-muted font-medium border border-[#E3E6DD]">
                            Sample review
                        </span>
                        <span class="text-xs text-muted">November 02, 2026</span>
                    </div>

                    <div class="flex items-center gap-1.5 text-[#B88746]">
                        <x-storefront.rating :rating="5" :showCount="false" size="sm" />
                    </div>

                    <h3 class="font-serif text-base text-ink font-medium">
                        "Thoughtfully packaged in amber glass."
                    </h3>

                    <!-- VERIFY CLAIM before launch -->
                    <p class="text-xs text-muted leading-relaxed font-sans">
                        Beyond the formulation itself, the presentation is deeply satisfying. The weight of the glass bottle and the gentle dropper make morning skincare feel like an intentional self-care ritual.
                    </p>
                </div>

                <div class="pt-4 mt-4 border-t border-[#ECEEE6] text-xs">
                    <span class="font-medium text-ink">Margaux Chen</span>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- RELATED PRODUCTS SECTION: 4 products from same category or fallback in standard 4-column grid (2 on mobile) -->
<section class="py-16 md:py-20 border-t border-[#ECEEE6] bg-ivory">
    <div class="site-container space-y-10">
        <div class="text-center max-w-xl mx-auto">
            <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block mb-2">
                Complementary Rituals
            </span>
            <h2 class="font-serif text-2xl sm:text-3xl text-ink font-normal tracking-tight">
                Pairs Thoughtfully With
            </h2>
            <p class="text-xs sm:text-sm text-muted mt-2">
                Hand-selected botanical steps formulated to support harmony and everyday nourishment.
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
            @foreach($relatedProducts as $relProduct)
                <x-storefront.product-card :product="$relProduct" />
            @endforeach
        </div>
    </div>
</section>
@endsection
