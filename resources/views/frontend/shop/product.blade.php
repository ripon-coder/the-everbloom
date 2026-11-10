@extends('layouts.app')

@section('title', ($product['name'] ?? 'Botanical Formulation') . ' — The Everbloom')
@section('meta_description', $product['short_description'] ?? 'Thoughtfully formulated botanical skincare for your daily ritual.')

@push('structured-data')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $product['name'] ?? 'Botanical Formulation',
    'image' => !empty($product['images'][0]) ? asset('images/' . $product['images'][0]) : null,
    'description' => $product['description'] ?? $product['short_description'] ?? '',
    'sku' => 'EB-' . str_pad($product['id'] ?? 1, 4, '0', STR_PAD_LEFT),
    'brand' => [
        '@type' => 'Brand',
        'name' => config('demo.site.name', 'The Everbloom'),
    ],
    'offers' => [
        '@type' => 'Offer',
        'url' => url()->current(),
        'priceCurrency' => config('demo.site.currency.code', 'USD'),
        'price' => number_format((float)($product['price'] ?? 0), 2, '.', ''),
        'availability' => (($product['stock_status'] ?? 'in_stock') === 'in_stock') ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        'itemCondition' => 'https://schema.org/NewCondition',
    ],
    'aggregateRating' => [
        '@type' => 'AggregateRating',
        'ratingValue' => (float)($product['rating'] ?? 5.0),
        'reviewCount' => (int)($product['review_count'] ?? 1),
        'bestRating' => '5',
        'worstRating' => '1',
    ],
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => url('/'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Shop All',
            'item' => url('/shop'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $product['category'] ?? 'Category',
            'item' => url('/shop/category/' . ($product['category_slug'] ?? '')),
        ],
        [
            '@type' => 'ListItem',
            'position' => 4,
            'name' => $product['name'] ?? 'Product',
            'item' => url()->current(),
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@php
    $images = $product['images'] ?? [];
    if (empty($images)) {
        $images = ['products/placeholder-botanical-01.jpg'];
    }
    $primaryImage = $images[0];
    
    // Explicit variants fallback
    $variants = $product['variants'] ?? [];
    if (empty($variants) && !empty($product['sizes'])) {
        foreach ($product['sizes'] as $i => $s) {
            $variants[] = [
                'size' => $s,
                'price' => (float)$product['price'],
                'compare_at_price' => !empty($product['compare_at_price']) ? (float)$product['compare_at_price'] : null,
                'stock_status' => $product['stock_status'] ?? 'in_stock',
            ];
        }
    } elseif (empty($variants)) {
        $variants[] = [
            'size' => 'Standard',
            'price' => (float)$product['price'],
            'compare_at_price' => !empty($product['compare_at_price']) ? (float)$product['compare_at_price'] : null,
            'stock_status' => $product['stock_status'] ?? 'in_stock',
        ];
    }

    $initialVariant = $variants[0];
    $hasInitialCompare = !empty($initialVariant['compare_at_price']) && $initialVariant['compare_at_price'] > $initialVariant['price'];
    $initialSavings = $hasInitialCompare ? round((($initialVariant['compare_at_price'] - $initialVariant['price']) / $initialVariant['compare_at_price']) * 100) : 0;

    // Fallback ensure exactly 4 related products
    $relatedProducts = $related ?? [];
    if (count($relatedProducts) < 4) {
        $allProducts = config('demo.products', []);
        $fallbacks = collect($allProducts)->where('slug', '!=', $product['slug'])->values();
        $relatedProducts = collect($relatedProducts)->concat($fallbacks)->unique('slug')->take(4)->all();
    }
@endphp

@section('content')
<div class="site-container py-8 md:py-12">
    <!-- Breadcrumbs -->
    <x-storefront.breadcrumbs :items="[
        ['label' => 'Shop All', 'url' => '/shop'],
        ['label' => $product['category'] ?? 'Category', 'url' => '/shop/category/' . ($product['category_slug'] ?? '')],
        ['label' => $product['name'] ?? 'Product', 'url' => '']
    ]" />

    <!-- Main Product Section: Gallery (Sticky on Desktop) & Buy Box -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">
        
        <!-- 1. GALLERY COLUMN (Sticky on Desktop: lg:sticky lg:top-24) -->
        <div 
            x-data="{
                activeImageIndex: 0,
                activeImage: '{{ $primaryImage }}',
                images: {{ json_encode($images) }},
                zoomActive: false,
                originX: '50%',
                originY: '50%',
                lightboxOpen: false,
                handleMouseMove(e) {
                    const rect = e.currentTarget.getBoundingClientRect();
                    const x = ((e.clientX - rect.left) / rect.width) * 100;
                    const y = ((e.clientY - rect.top) / rect.height) * 100;
                    this.originX = `${x.toFixed(1)}%`;
                    this.originY = `${y.toFixed(1)}%`;
                },
                openLightbox() {
                    if (window.innerWidth < 1024) {
                        this.lightboxOpen = true;
                    }
                },
                nextImage() {
                    this.activeImageIndex = (this.activeImageIndex + 1) % this.images.length;
                    this.activeImage = this.images[this.activeImageIndex];
                },
                prevImage() {
                    this.activeImageIndex = (this.activeImageIndex - 1 + this.images.length) % this.images.length;
                    this.activeImage = this.images[this.activeImageIndex];
                }
            }"
            @keydown.escape.window="lightboxOpen = false"
            class="lg:col-span-7 lg:sticky lg:top-24"
        >
            <div class="flex flex-col-reverse lg:flex-row gap-4 items-start">
                <!-- Thumbnails: fixed aspect-square, object-cover, no overflowing text -->
                @if(count($images) > 1)
                    <div class="grid grid-cols-4 lg:flex lg:flex-col gap-3 w-full lg:w-20 shrink-0">
                        @foreach($images as $index => $img)
                            <button 
                                type="button" 
                                @click="activeImageIndex = {{ $index }}; activeImage = '{{ $img }}'"
                                class="aspect-square w-full bg-sage-light/40 border transition-all duration-200 overflow-hidden cursor-pointer shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest p-0"
                                :class="activeImageIndex === {{ $index }} ? 'border-forest ring-1 ring-forest' : 'border-beige hover:border-forest/50'"
                                aria-label="View photo {{ $index + 1 }} of {{ count($images) }}"
                            >
                                <x-storefront.image :src="$img" :alt="($product['name'] ?? 'Product') . ' thumbnail ' . ($index + 1)" aspect-ratio="aspect-square" class="w-full h-full object-cover pointer-events-none" />
                            </button>
                        @endforeach
                    </div>
                @endif

                <!-- Main Image: aspect-[4/5], max-h-[80vh], Desktop Hover Zoom & Mobile Tap-to-Lightbox -->
                <div 
                    class="flex-1 w-full aspect-[4/5] max-h-[80vh] bg-sage-light/40 border border-beige/80 relative overflow-hidden group select-none"
                    @mouseenter="zoomActive = true"
                    @mouseleave="zoomActive = false"
                    @mousemove="handleMouseMove($event)"
                    @click="openLightbox()"
                >
                    <!-- Main dynamic image display -->
                    <div class="w-full h-full relative overflow-hidden">
                        @foreach($images as $index => $img)
                            <div 
                                x-show="activeImageIndex === {{ $index }}" 
                                class="w-full h-full"
                                :style="zoomActive ? { transformOrigin: `${originX} ${originY}`, transform: 'scale(1.85)' } : { transform: 'scale(1)' }"
                                style="transition: transform 120ms ease-out;"
                            >
                                <x-storefront.image 
                                    :src="$img" 
                                    :alt="$product['name'] ?? 'Botanical Skincare'" 
                                    aspect-ratio="aspect-[4/5]"
                                    class="w-full h-full object-cover cursor-zoom-in lg:cursor-crosshair"
                                />
                            </div>
                        @endforeach
                    </div>

                    <!-- Tap to enlarge hint on mobile -->
                    <div class="absolute bottom-3 right-3 lg:hidden bg-ivory/90 backdrop-blur-xs px-2.5 py-1 text-[11px] text-ink/80 rounded-sm border border-beige flex items-center gap-1.5 pointer-events-none">
                        <svg class="w-3.5 h-3.5 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                        </svg>
                        <span>Tap to expand</span>
                    </div>
                </div>
            </div>

            <!-- Mobile Lightbox Modal (Alpine, Focus Trap, Esc to close) -->
            <div 
                x-show="lightboxOpen" 
                x-trap.noscroll="lightboxOpen"
                x-cloak
                class="fixed inset-0 z-50 bg-ink/95 flex flex-col justify-between p-4 lg:hidden"
                role="dialog"
                aria-modal="true"
                aria-label="Image gallery lightbox"
            >
                <!-- Top Lightbox Bar -->
                <div class="flex items-center justify-between text-ivory pt-2 px-2">
                    <span class="text-xs uppercase tracking-widest font-sans font-medium text-ivory/80">
                        <span x-text="activeImageIndex + 1"></span> / {{ count($images) }}
                    </span>
                    <button 
                        type="button" 
                        @click="lightboxOpen = false" 
                        class="p-2 text-ivory/80 hover:text-ivory focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ivory rounded"
                        aria-label="Close lightbox"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Center Lightbox Image -->
                <div class="flex-1 flex items-center justify-center p-2 relative overflow-hidden">
                    @foreach($images as $index => $img)
                        <div x-show="activeImageIndex === {{ $index }}" class="max-w-full max-h-[75vh] flex items-center justify-center">
                            <x-storefront.image :src="$img" :alt="$product['name'] ?? ''" aspect-ratio="aspect-[4/5]" class="max-h-[75vh] w-auto object-contain rounded-xs" />
                        </div>
                    @endforeach

                    @if(count($images) > 1)
                        <!-- Prev / Next Controls -->
                        <button 
                            type="button" 
                            @click.stop="prevImage()"
                            class="absolute left-2 top-1/2 -translate-y-1/2 p-2.5 bg-ivory/20 hover:bg-ivory/40 text-ivory rounded-full backdrop-blur-xs cursor-pointer"
                            aria-label="Previous photo"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        </button>
                        <button 
                            type="button" 
                            @click.stop="nextImage()"
                            class="absolute right-2 top-1/2 -translate-y-1/2 p-2.5 bg-ivory/20 hover:bg-ivory/40 text-ivory rounded-full backdrop-blur-xs cursor-pointer"
                            aria-label="Next photo"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </button>
                    @endif
                </div>

                <!-- Bottom Dots Selector -->
                <div class="flex justify-center gap-2 pb-4">
                    @foreach($images as $index => $img)
                        <button 
                            type="button" 
                            @click="activeImageIndex = {{ $index }}; activeImage = '{{ $img }}'" 
                            class="w-2.5 h-2.5 rounded-full transition-all"
                            :class="activeImageIndex === {{ $index }} ? 'bg-ivory w-6' : 'bg-ivory/40'"
                            aria-label="Select photo {{ $index + 1 }}"
                        ></button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 2. BUY BOX COLUMN (Not sticky - gallery is sticky per instruction 3) -->
        <div 
            x-data="{
                product: {{ json_encode($product) }},
                variants: {{ json_encode($variants) }},
                selectedVariantIndex: 0,
                selectedSize: '{{ $initialVariant['size'] }}',
                price: {{ (float)$initialVariant['price'] }},
                comparePrice: {{ !empty($initialVariant['compare_at_price']) ? (float)$initialVariant['compare_at_price'] : 'null' }},
                stockStatus: '{{ $initialVariant['stock_status'] ?? 'in_stock' }}',
                quantity: 1,
                adding: false,
                selectVariant(idx) {
                    this.selectedVariantIndex = idx;
                    const v = this.variants[idx];
                    if (v) {
                        this.selectedSize = v.size;
                        this.price = v.price;
                        this.comparePrice = v.compare_at_price ? v.compare_at_price : null;
                        this.stockStatus = v.stock_status || 'in_stock';
                    }
                },
                get savingsPercent() {
                    if (this.comparePrice && this.comparePrice > this.price) {
                        return Math.round(((this.comparePrice - this.price) / this.comparePrice) * 100);
                    }
                    return null;
                },
                addToBag() {
                    this.adding = true;
                    const item = {
                        ...this.product,
                        price: this.price,
                        compare_at_price: this.comparePrice
                    };
                    $store.cart.add(item, this.quantity, this.selectedSize, true);
                    setTimeout(() => this.adding = false, 750);
                },
                buyNow() {
                    const item = {
                        ...this.product,
                        price: this.price,
                        compare_at_price: this.comparePrice
                    };
                    $store.cart.add(item, this.quantity, this.selectedSize, false);
                    window.location.href = '/checkout';
                }
            }"
            class="lg:col-span-5 space-y-6 bg-transparent"
        >
            <!-- Product Header: Category, Name & Rating with Review Link -->
            <div>
                <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block mb-1.5">
                    {{ $product['category'] ?? 'Botanical Formulations' }}
                </span>
                <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal leading-tight tracking-tight">
                    {{ $product['name'] ?? '' }}
                </h1>
                
                <div class="mt-3 flex items-center gap-3">
                    <a href="#reviews" class="inline-flex items-center gap-2 group focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-forest">
                        <x-storefront.rating :rating="$product['rating'] ?? 5.0" :count="$product['review_count'] ?? 0" size="md" />
                        <span class="text-xs text-muted group-hover:text-forest underline underline-offset-4 transition-colors">
                            Read {{ $product['review_count'] ?? 0 }} reviews
                        </span>
                    </a>
                </div>
            </div>

            <!-- Price & Compare-at Price with Save X% Badge -->
            <div class="border-y border-beige/80 py-4 flex items-center justify-between">
                <div class="flex items-baseline gap-3">
                    <span class="font-serif text-2xl sm:text-3xl text-ink font-normal tracking-tight">
                        $<span x-text="price.toFixed(2)">{{ number_format($initialVariant['price'], 2) }}</span>
                    </span>
                    <span 
                        x-show="comparePrice" 
                        class="text-sm sm:text-base text-muted/80 line-through font-sans"
                    >
                        $<span x-text="comparePrice ? comparePrice.toFixed(2) : ''">{{ !empty($initialVariant['compare_at_price']) ? number_format($initialVariant['compare_at_price'], 2) : '' }}</span>
                    </span>
                </div>

                <!-- Dynamic Save X% Badge -->
                <template x-if="savingsPercent">
                    <span 
                        class="px-2.5 py-1 bg-forest text-ivory text-xs uppercase tracking-wider font-medium rounded-sm shadow-xs"
                        x-text="'Save ' + savingsPercent + '%'"
                    >
                        Save {{ $initialSavings }}%
                    </span>
                </template>
            </div>

            <!-- Short Description -->
            <!-- VERIFY CLAIM before launch -->
            <p class="text-sm text-muted leading-relaxed font-sans">
                {{ $product['short_description'] ?? $product['description'] ?? '' }}
            </p>

            <!-- Variant Selector: Explicit Price & Size per Variant -->
            @if(!empty($variants))
                <div class="space-y-2.5 pt-1">
                    <div class="flex items-center justify-between text-xs">
                        <span class="uppercase tracking-[0.18em] font-semibold text-ink">Volume / Size:</span>
                        <span class="text-muted font-medium" x-text="selectedSize"></span>
                    </div>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach($variants as $idx => $v)
                            <button 
                                type="button" 
                                @click="selectVariant({{ $idx }})"
                                class="px-3.5 py-2 text-xs font-sans rounded-none transition-all cursor-pointer border focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-forest flex items-center gap-2"
                                :class="selectedVariantIndex === {{ $idx }} ? 'bg-sage-light/60 border-forest text-forest font-semibold shadow-xs' : 'bg-ivory border-beige text-ink hover:border-forest/60'"
                            >
                                <span>{{ $v['size'] }}</span>
                                <span class="text-muted/80 font-normal">(${{ number_format($v['price'], 2) }})</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Quantity, Add to Bag & Wishlist Row -->
            <div class="space-y-3 pt-2">
                <div class="flex items-center gap-3">
                    <!-- Quantity Selector Component -->
                    <div class="shrink-0" @qty-change="quantity = $event.detail">
                        <x-storefront.quantity-selector :quantity="1" />
                    </div>

                    <!-- Add to Bag CTA Button -->
                    <button 
                        type="button" 
                        @click="addToBag()"
                        :disabled="adding || stockStatus === 'out_of_stock'"
                        class="btn-primary flex-1 min-h-[44px] flex items-center justify-center gap-2 cursor-pointer transition-all"
                    >
                        <span x-show="!adding" class="inline-flex items-center gap-2">
                            <svg class="w-4 h-4 text-inherit" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span>Add to Bag</span>
                        </span>
                        <span x-show="adding" x-cloak class="inline-flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-inherit" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Added to Bag</span>
                        </span>
                    </button>

                    <!-- Wishlist Button -->
                    <div class="shrink-0">
                        <x-storefront.wishlist-button :product="$product" size="lg" />
                    </div>
                </div>

                <!-- Buy Now Button (Direct to checkout) -->
                <button 
                    type="button" 
                    @click="buyNow()"
                    :disabled="stockStatus === 'out_of_stock'"
                    class="btn-secondary w-full min-h-[44px] text-center font-medium tracking-[0.18em] cursor-pointer"
                >
                    Buy Now
                </button>
            </div>

            <!-- Stock Status: strictly 'In stock' with aria-live -->
            <div class="flex items-center gap-2 pt-1" aria-live="polite">
                <span class="w-2 h-2 rounded-full" :class="stockStatus === 'in_stock' ? 'bg-forest' : 'bg-amber-600'"></span>
                <span 
                    class="text-xs uppercase tracking-wider font-medium" 
                    :class="stockStatus === 'in_stock' ? 'text-forest' : 'text-amber-700'"
                    x-text="stockStatus === 'in_stock' ? 'In stock' : 'Out of stock'"
                >
                    In stock
                </span>
            </div>

            <!-- Shipping & Returns Note Row (Pulled from config/demo/site.php) -->
            <div class="flex items-center gap-2.5 text-xs text-muted pt-3 border-t border-beige/60">
                <svg class="w-4 h-4 text-forest shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                <!-- DEMO PLACEHOLDER: replace before launch -->
                <span>{{ config('demo.site.shipping_and_returns_note', 'Free standard shipping over $75. 30-day returns on unopened items.') }}</span>
            </div>

            <!-- 3. ACCORDIONS SECTION (Description open by default; opening another closes previous) -->
            <div class="pt-6 border-t border-beige/80">
                <div 
                    x-data="{
                        active: 0,
                        toggle(idx) {
                            this.active = (this.active === idx ? null : idx);
                        },
                        isOpen(idx) {
                            return this.active === idx;
                        }
                    }"
                    class="divide-y divide-beige/80 border-t border-b border-beige/80"
                >
                    <!-- Accordion 1: Description (Open by default) -->
                    <div class="py-3.5">
                        <button 
                            type="button" 
                            @click="toggle(0)"
                            class="w-full flex items-center justify-between text-left font-serif text-base text-ink hover:text-forest transition-colors py-1 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-forest cursor-pointer"
                            :aria-expanded="isOpen(0)"
                        >
                            <span class="font-medium">Description</span>
                            <span class="ml-4 shrink-0 text-muted transition-transform duration-200" :class="{ 'rotate-180': isOpen(0) }">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </span>
                        </button>
                        <div x-show="isOpen(0)" x-collapse class="text-sm text-muted leading-relaxed pt-2 pb-3 font-sans" x-cloak>
                            <!-- VERIFY CLAIM before launch -->
                            <p>{{ $product['description'] ?? 'Thoughtfully formulated botanical skincare designed to nurture moisture balance and lasting luminosity throughout your daily rituals.' }}</p>
                        </div>
                    </div>

                    <!-- Accordion 2: Ingredients -->
                    <div class="py-3.5">
                        <button 
                            type="button" 
                            @click="toggle(1)"
                            class="w-full flex items-center justify-between text-left font-serif text-base text-ink hover:text-forest transition-colors py-1 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-forest cursor-pointer"
                            :aria-expanded="isOpen(1)"
                        >
                            <span class="font-medium">Ingredients</span>
                            <span class="ml-4 shrink-0 text-muted transition-transform duration-200" :class="{ 'rotate-180': isOpen(1) }">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </span>
                        </button>
                        <div x-show="isOpen(1)" x-collapse class="text-xs text-muted leading-relaxed pt-2 pb-3 font-mono tracking-tight" x-cloak>
                            <!-- VERIFY CLAIM before launch -->
                            <p>{{ $product['ingredients'] ?? 'Camellia Sinensis Leaf Hydrosol, Sclerocarya Birrea Seed Oil, Sambucus Nigra Fruit Extract, Glycerin, Sodium Hyaluronate, Tocopherol, Citric Acid.' }}</p>
                            <p class="mt-2 text-[11px] text-muted/80 font-sans italic">Formulated without synthetic perfumes, harsh sulfates, parabens, or artificial colorants.</p>
                        </div>
                    </div>

                    <!-- Accordion 3: How to Use -->
                    <div class="py-3.5">
                        <button 
                            type="button" 
                            @click="toggle(2)"
                            class="w-full flex items-center justify-between text-left font-serif text-base text-ink hover:text-forest transition-colors py-1 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-forest cursor-pointer"
                            :aria-expanded="isOpen(2)"
                        >
                            <span class="font-medium">How to Use</span>
                            <span class="ml-4 shrink-0 text-muted transition-transform duration-200" :class="{ 'rotate-180': isOpen(2) }">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </span>
                        </button>
                        <div x-show="isOpen(2)" x-collapse class="text-sm text-muted leading-relaxed pt-2 pb-3 font-sans space-y-2" x-cloak>
                            <!-- VERIFY CLAIM before launch -->
                            <p>{{ $product['how_to_use'] ?? 'Warm 3 to 4 drops between clean palms and gently press onto freshly cleansed face, neck, and décolletage using mindful upward motions.' }}</p>
                            <p class="text-xs text-forest font-medium">Ritual Tip: Apply while skin remains slightly damp from botanical mist or essence for optimal hydration absorption.</p>
                        </div>
                    </div>

                    <!-- Accordion 4: Product Details -->
                    <div class="py-3.5">
                        <button 
                            type="button" 
                            @click="toggle(3)"
                            class="w-full flex items-center justify-between text-left font-serif text-base text-ink hover:text-forest transition-colors py-1 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-forest cursor-pointer"
                            :aria-expanded="isOpen(3)"
                        >
                            <span class="font-medium">Product Details</span>
                            <span class="ml-4 shrink-0 text-muted transition-transform duration-200" :class="{ 'rotate-180': isOpen(3) }">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </span>
                        </button>
                        <div x-show="isOpen(3)" x-collapse class="text-xs text-muted leading-relaxed pt-2 pb-3 font-sans space-y-1.5" x-cloak>
                            <div class="flex justify-between py-1 border-b border-beige/40">
                                <span class="font-medium text-ink">Packaging</span>
                                <span>100% Recyclable Amber Glass with Dropper</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-beige/40">
                                <span class="font-medium text-ink">Skin Type</span>
                                <span>Suitable for all skin types, including sensitive</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-beige/40">
                                <span class="font-medium text-ink">Texture</span>
                                <span>Silky, rapidly absorbing botanical essence</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="font-medium text-ink">Aroma</span>
                                <span>Subtle, naturally derived whole botanical aroma</span>
                            </div>
                        </div>
                    </div>

                    <!-- Accordion 5: Shipping & Returns -->
                    <div class="py-3.5">
                        <button 
                            type="button" 
                            @click="toggle(4)"
                            class="w-full flex items-center justify-between text-left font-serif text-base text-ink hover:text-forest transition-colors py-1 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-forest cursor-pointer"
                            :aria-expanded="isOpen(4)"
                        >
                            <span class="font-medium">Shipping & Returns</span>
                            <span class="ml-4 shrink-0 text-muted transition-transform duration-200" :class="{ 'rotate-180': isOpen(4) }">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </span>
                        </button>
                        <div x-show="isOpen(4)" x-collapse class="text-sm text-muted leading-relaxed pt-2 pb-3 font-sans space-y-2" x-cloak>
                            <p>Free standard shipping over $75. 30-day returns on unopened items.</p>
                            <p>Orders are dispatched within 24 to 48 business hours. Mindful return policy applies to unopened bottles or sealed packages.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- 4. BELOW THE FOLD SECTIONS (Reviews -> Related Products) -->

<!-- REVIEWS SECTION (id="reviews") -->
<section id="reviews" class="border-t border-beige/80 py-16 md:py-24 bg-sage-light/20 scroll-mt-24">
    <div class="site-container">
        
        <!-- Reviews Summary Header -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 pb-12 border-b border-beige/80 items-center">
            
            <!-- Left: Aggregate Score & Stars -->
            <div class="lg:col-span-4 text-center lg:text-left space-y-2">
                <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block">Client Reflections</span>
                <div class="flex items-baseline justify-center lg:justify-start gap-2">
                    <span class="font-serif text-4xl sm:text-5xl text-ink font-normal">{{ number_format((float)($product['rating'] ?? 5.0), 1) }}</span>
                    <span class="text-base text-muted font-sans font-light">/ 5.0</span>
                </div>
                <div class="flex justify-center lg:justify-start pt-1">
                    <x-storefront.rating :rating="$product['rating'] ?? 5.0" :count="$product['review_count'] ?? 0" size="md" />
                </div>
                <!-- Based on X reviews (sample data) per Instruction 2 -->
                <p class="text-xs text-muted">Based on {{ $product['review_count'] ?? 94 }} reviews (sample data)</p>
            </div>

            <!-- Middle: Simple Rating Breakdown Bars -->
            <div class="lg:col-span-5 space-y-2 text-xs">
                <div class="flex items-center gap-3">
                    <span class="w-12 text-right text-muted shrink-0">5 Stars</span>
                    <div class="flex-1 h-2 bg-beige/60 rounded-full overflow-hidden">
                        <div class="h-full bg-forest rounded-full" style="width: 86%;"></div>
                    </div>
                    <span class="w-8 text-right text-muted shrink-0 font-medium">86%</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-12 text-right text-muted shrink-0">4 Stars</span>
                    <div class="flex-1 h-2 bg-beige/60 rounded-full overflow-hidden">
                        <div class="h-full bg-forest rounded-full" style="width: 11%;"></div>
                    </div>
                    <span class="w-8 text-right text-muted shrink-0 font-medium">11%</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-12 text-right text-muted shrink-0">3 Stars</span>
                    <div class="flex-1 h-2 bg-beige/60 rounded-full overflow-hidden">
                        <div class="h-full bg-forest/70 rounded-full" style="width: 2%;"></div>
                    </div>
                    <span class="w-8 text-right text-muted shrink-0 font-medium">2%</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-12 text-right text-muted shrink-0">2 Stars</span>
                    <div class="flex-1 h-2 bg-beige/60 rounded-full overflow-hidden">
                        <div class="h-full bg-forest/50 rounded-full" style="width: 1%;"></div>
                    </div>
                    <span class="w-8 text-right text-muted shrink-0 font-medium">1%</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-12 text-right text-muted shrink-0">1 Star</span>
                    <div class="flex-1 h-2 bg-beige/60 rounded-full overflow-hidden">
                        <div class="h-full bg-forest/30 rounded-full" style="width: 0%;"></div>
                    </div>
                    <span class="w-8 text-right text-muted shrink-0 font-medium">0%</span>
                </div>
            </div>

            <!-- Right: Write a Review Button Default State & Submitted Message per Instruction 2 -->
            <div class="lg:col-span-3 text-center lg:text-right" x-data="{ reviewFormOpen: false, reviewSubmitted: false, reviewerName: '', reviewerComment: '' }">
                <!-- Default state: Write a review button -->
                <div x-show="!reviewSubmitted && !reviewFormOpen">
                    <button 
                        type="button" 
                        @click="reviewFormOpen = true"
                        class="btn-secondary !py-2.5 !px-5 text-xs tracking-[0.18em] cursor-pointer"
                    >
                        Write a review
                    </button>
                </div>

                <!-- Demo Review Form -->
                <div x-show="reviewFormOpen && !reviewSubmitted" x-cloak class="bg-ivory border border-beige p-4 text-left space-y-2.5 shadow-sm">
                    <div class="flex items-center justify-between pb-1.5 border-b border-beige">
                        <span class="text-[11px] uppercase tracking-wider font-semibold text-ink">Share Your Reflection</span>
                        <button type="button" @click="reviewFormOpen = false" class="text-muted hover:text-ink text-xs cursor-pointer">✕</button>
                    </div>
                    <form @submit.prevent="reviewSubmitted = true; reviewFormOpen = false" class="space-y-2">
                        <div>
                            <input type="text" x-model="reviewerName" required placeholder="Your name" class="w-full text-xs p-1.5 bg-sage-light/20 border border-beige focus:border-forest focus:outline-none">
                        </div>
                        <div>
                            <textarea x-model="reviewerComment" required rows="2" placeholder="Your review..." class="w-full text-xs p-1.5 bg-sage-light/20 border border-beige focus:border-forest focus:outline-none"></textarea>
                        </div>
                        <button type="submit" class="btn-primary !py-1.5 !px-3 text-xs w-full cursor-pointer">
                            Submit Review
                        </button>
                    </form>
                </div>

                <!-- Review submitted message: Shows ONLY after demo form submission -->
                <div x-show="reviewSubmitted" x-cloak class="p-3 bg-forest/10 border border-forest/30 text-forest text-xs space-y-1 text-center lg:text-right">
                    <p class="font-semibold flex items-center justify-center lg:justify-end gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Review submitted
                    </p>
                    <p class="text-[11px] text-muted">Thank you! Your review has been received (sample demo).</p>
                </div>
            </div>
        </div>

        <!-- 3 Sample Reviews Labeled "Sample review" (No "Verified Ritual" labels per Instruction 2) -->
        <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
            
            <!-- Sample Review 1 -->
            <div class="bg-ivory border border-beige/80 p-6 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase tracking-[0.2em] px-2 py-0.5 bg-beige/50 text-muted font-medium rounded-xs">
                            Sample review
                        </span>
                        <span class="text-xs text-muted">October 14, 2026</span>
                    </div>

                    <div class="flex items-center gap-1.5 text-[#B88746]">
                        <x-storefront.rating :rating="5" :showCount="false" size="sm" />
                    </div>

                    <h4 class="font-serif text-base text-ink font-medium">
                        "Luminous texture, instant calm."
                    </h4>

                    <!-- VERIFY CLAIM before launch -->
                    <p class="text-xs text-muted leading-relaxed font-sans">
                        A truly delicate botanical infusion. After two weeks of morning application, my dry patches feel completely restored with no heavy residue underneath my daily sunscreen.
                    </p>
                </div>

                <div class="pt-4 mt-4 border-t border-beige/60 text-xs">
                    <span class="font-medium text-ink">Eleanor Vance</span>
                </div>
            </div>

            <!-- Sample Review 2 -->
            <div class="bg-ivory border border-beige/80 p-6 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase tracking-[0.2em] px-2 py-0.5 bg-beige/50 text-muted font-medium rounded-xs">
                            Sample review
                        </span>
                        <span class="text-xs text-muted">October 28, 2026</span>
                    </div>

                    <div class="flex items-center gap-1.5 text-[#B88746]">
                        <x-storefront.rating :rating="5" :showCount="false" size="sm" />
                    </div>

                    <h4 class="font-serif text-base text-ink font-medium">
                        "Subtle botanical aroma and pure comfort."
                    </h4>

                    <!-- VERIFY CLAIM before launch -->
                    <p class="text-xs text-muted leading-relaxed font-sans">
                        I am extremely cautious with fragrances and active oils. This formula feels gentle on reactive skin and leaves my face feeling nourished throughout long autumn working days.
                    </p>
                </div>

                <div class="pt-4 mt-4 border-t border-beige/60 text-xs">
                    <span class="font-medium text-ink">Clara Lindqvist</span>
                </div>
            </div>

            <!-- Sample Review 3 -->
            <div class="bg-ivory border border-beige/80 p-6 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase tracking-[0.2em] px-2 py-0.5 bg-beige/50 text-muted font-medium rounded-xs">
                            Sample review
                        </span>
                        <span class="text-xs text-muted">November 02, 2026</span>
                    </div>

                    <div class="flex items-center gap-1.5 text-[#B88746]">
                        <x-storefront.rating :rating="5" :showCount="false" size="sm" />
                    </div>

                    <h4 class="font-serif text-base text-ink font-medium">
                        "Thoughtfully packaged in amber glass."
                    </h4>

                    <!-- VERIFY CLAIM before launch -->
                    <p class="text-xs text-muted leading-relaxed font-sans">
                        Beyond the formulation itself, the presentation is deeply satisfying. The weight of the glass bottle and the gentle dropper make morning skincare feel like an intentional self-care ritual.
                    </p>
                </div>

                <div class="pt-4 mt-4 border-t border-beige/60 text-xs">
                    <span class="font-medium text-ink">Margaux Chen</span>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- RELATED PRODUCTS SECTION: 4 products from same category or fallback in standard 4-column grid (2 on mobile) -->
<section class="py-16 md:py-24 border-t border-beige/80 bg-ivory">
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

        <!-- Standard 4-column grid (2 on mobile) per Instruction 5 -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
            @foreach($relatedProducts as $relProduct)
                <x-storefront.product-card :product="$relProduct" />
            @endforeach
        </div>
    </div>
</section>
@endsection
