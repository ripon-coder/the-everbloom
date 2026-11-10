@extends('layouts.app')

@section('title', config('demo.site.name') . ' — ' . config('demo.site.tagline'))
@section('meta_description', 'Thoughtfully formulated for everyday rituals using carefully selected botanical ingredients. Naturally beautiful, always blooming.')

@section('content')
<div class="space-y-16 md:space-y-24 pb-16">

    <!-- 1. HERO SECTION: Asymmetric, Editorial, Overlapping Images, py-20 Desktop -->
    <section class="relative bg-sage-light/30 border-b border-beige/70 py-12 md:py-20 overflow-hidden">
        <div class="site-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                
                <!-- Left Text Column -->
                <div class="lg:col-span-7 space-y-6">
                    <!-- Small Label Above Headline -->
                    <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block">
                        {{ config('demo.site.tagline') }}
                    </span>

                    <!-- Editorial Asymmetric Headline -->
                    <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl text-ink font-normal tracking-tight leading-[1.12]">
                        A Ritual of<br>
                        <span class="italic font-normal">Natural Beauty.</span>
                    </h1>

                    <!-- Factual Copy -->
                    <p class="text-base sm:text-lg text-muted max-w-xl leading-relaxed font-sans">
                        Carefully selected botanical ingredients and plant-derived oils, thoughtfully formulated for everyday rituals. Designed to nurture skin balance and a radiant finish.
                    </p>

                    <!-- CTAs (Min 13px link) -->
                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <x-storefront.button href="/shop" variant="primary" size="lg" class="text-[13px]">
                            Explore Formulations
                        </x-storefront.button>
                        <x-storefront.button href="/about" variant="secondary" size="lg" class="text-[13px]">
                            Our Philosophy
                        </x-storefront.button>
                    </div>
                </div>

                <!-- Right Overlapping Editorial Image Composition -->
                <div class="lg:col-span-5 relative">
                    <div class="relative max-w-md mx-auto">
                        <!-- Primary Arch Image (1px subtle sage border, no heavy white frame) -->
                        <div class="relative overflow-hidden rounded-t-[140px] rounded-b-none border border-sage/40 bg-sage-light/40 shadow-soft">
                            <x-storefront.image 
                                src="hero/hero-botanical-arch.jpg" 
                                alt="A Ritual of Natural Beauty with Botanicals" 
                                aspect-ratio="aspect-[4/5]" 
                                class="rounded-t-[138px]"
                            />
                        </div>

                        <!-- Secondary Overlapping Rectangular Image (Asymmetric Editorial Accent) -->
                        <div class="absolute -bottom-6 -left-6 sm:-bottom-8 sm:-left-8 w-44 sm:w-52 aspect-[3/4] overflow-hidden border border-beige-dark/40 shadow-soft bg-ivory p-1.5 hidden sm:block">
                            <x-storefront.image 
                                src="hero/hero-botanical-detail.jpg" 
                                alt="Botanical Ingredients and Glass Pipette" 
                                aspect-ratio="aspect-[3/4]"
                            />
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 2. WHY CHOOSE THE EVERBLOOM: 4 Points in a simple icon + text row (NO cards) -->
    <section class="site-container py-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-10 border-b border-beige/70 pb-12 md:pb-16">
            
            <!-- Point 1 -->
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 shrink-0 text-forest flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-serif text-base text-ink font-medium">Carefully Selected</h3>
                    <p class="text-xs text-muted leading-relaxed mt-1">Formulated with plant-derived extracts and gentle floral waters.</p>
                </div>
            </div>

            <!-- Point 2 -->
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 shrink-0 text-forest flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-serif text-base text-ink font-medium">Thoughtful Formulations</h3>
                    <p class="text-xs text-muted leading-relaxed mt-1">Crafted for everyday rituals to support skin barrier balance.</p>
                </div>
            </div>

            <!-- Point 3 -->
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 shrink-0 text-forest flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-serif text-base text-ink font-medium">Recyclable Glass Jars</h3>
                    <p class="text-xs text-muted leading-relaxed mt-1">Packaged in amber UV-protective bottles and recyclable cartons.</p>
                </div>
            </div>

            <!-- Point 4 -->
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 shrink-0 text-forest flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-serif text-base text-ink font-medium">Gentle & Tested</h3>
                    <p class="text-xs text-muted leading-relaxed mt-1">Formulated without synthetic dyes or harsh artificial fragrances.</p>
                </div>
            </div>

        </div>
    </section>

    <!-- 3. ASYMMETRIC CATEGORY GRID: 2 Large + 4 Small (Layout Variety) -->
    <section class="site-container">
        <x-storefront.section-heading 
            eyebrow="Targeted Rituals" 
            title="Curated by Skin Goal" 
            description="Explore our specialized formulations crafted for face, body, and daily protection."
            link-text="Shop All Categories" 
            link-url="/shop"
        />

        @php
            $cats = config('demo.categories');
            $faceCat = $cats[0] ?? null;
            $serumCat = $cats[2] ?? null;
            $otherCats = array_slice($cats, 3, 4);
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            
            <!-- Large Card 1: Face Care (Spans 6 cols on md, tall aspect) -->
            @if($faceCat)
                <div class="md:col-span-6 group relative overflow-hidden bg-sage-light/40 border border-beige">
                    <a href="/shop/category/{{ $faceCat['slug'] }}" class="block w-full h-full">
                        <div class="aspect-[4/3] sm:aspect-[16/11] overflow-hidden">
                            <x-storefront.image 
                                :src="$faceCat['image']" 
                                :alt="$faceCat['name']" 
                                aspect-ratio="aspect-[4/3] sm:aspect-[16/11]" 
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            />
                        </div>
                        <div class="p-6 bg-ivory/95 border-t border-beige/60 flex items-center justify-between">
                            <div>
                                <span class="text-xs uppercase tracking-[0.2em] text-muted block mb-1">Featured Collection</span>
                                <h3 class="font-serif text-xl sm:text-2xl text-ink font-normal group-hover:text-forest transition-colors">
                                    {{ $faceCat['name'] }}
                                </h3>
                                <p class="text-xs text-muted mt-1 line-clamp-1 max-w-sm">{{ $faceCat['tagline'] }}</p>
                            </div>
                            <span class="text-[13px] uppercase tracking-widest text-forest font-medium group-hover:translate-x-1 transition-transform">
                                Explore →
                            </span>
                        </div>
                    </a>
                </div>
            @endif

            <!-- Large Card 2: Serums (Spans 6 cols on md, tall aspect) -->
            @if($serumCat)
                <div class="md:col-span-6 group relative overflow-hidden bg-sage-light/40 border border-beige">
                    <a href="/shop/category/{{ $serumCat['slug'] }}" class="block w-full h-full">
                        <div class="aspect-[4/3] sm:aspect-[16/11] overflow-hidden">
                            <x-storefront.image 
                                :src="$serumCat['image']" 
                                :alt="$serumCat['name']" 
                                aspect-ratio="aspect-[4/3] sm:aspect-[16/11]" 
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            />
                        </div>
                        <div class="p-6 bg-ivory/95 border-t border-beige/60 flex items-center justify-between">
                            <div>
                                <span class="text-xs uppercase tracking-[0.2em] text-muted block mb-1">Concentrated Actives</span>
                                <h3 class="font-serif text-xl sm:text-2xl text-ink font-normal group-hover:text-forest transition-colors">
                                    {{ $serumCat['name'] }}
                                </h3>
                                <p class="text-xs text-muted mt-1 line-clamp-1 max-w-sm">{{ $serumCat['tagline'] }}</p>
                            </div>
                            <span class="text-[13px] uppercase tracking-widest text-forest font-medium group-hover:translate-x-1 transition-transform">
                                Explore →
                            </span>
                        </div>
                    </a>
                </div>
            @endif

            <!-- 4 Smaller Cards (Spanning 3 cols each) -->
            @foreach($otherCats as $cat)
                <div class="md:col-span-3 group relative overflow-hidden bg-sage-light/40 border border-beige">
                    <a href="/shop/category/{{ $cat['slug'] }}" class="block w-full h-full">
                        <div class="aspect-square overflow-hidden">
                            <x-storefront.image 
                                :src="$cat['image']" 
                                :alt="$cat['name']" 
                                aspect-ratio="aspect-square" 
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                        </div>
                        <div class="p-4 bg-ivory/95 border-t border-beige/60">
                            <h3 class="font-serif text-base text-ink font-medium group-hover:text-forest transition-colors">
                                {{ $cat['name'] }}
                            </h3>
                            <p class="text-xs text-muted uppercase tracking-wider mt-0.5">
                                {{ $cat['item_count'] }} Formulations
                            </p>
                        </div>
                    </a>
                </div>
            @endforeach

        </div>
    </section>

    <!-- 4. FEATURED PRODUCTS: Responsive Grid with Quick Add & Sale Badges -->
    <section class="site-container">
        <x-storefront.section-heading 
            eyebrow="The Essentials" 
            title="Signature Formulations" 
            description="Our customer favorites formulated with carefully selected botanical ingredients."
            link-text="Shop Full Catalogue" 
            link-url="/shop"
        />

        @php
            $featured = array_filter(config('demo.products'), fn($p) => $p['is_featured'] ?? false);
            $featured = array_slice($featured, 0, 4);
        @endphp

        <x-storefront.product-grid :products="$featured" columns="4" />
    </section>

    <!-- 5. BRAND PHILOSOPHY: Split Layout with Botanical Image ("Beauty That Grows With You.") -->
    <section class="site-container">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center bg-white border border-beige p-6 sm:p-10 md:p-16">
            
            <!-- Left Side: Editorial Botanical Image -->
            <div class="lg:col-span-5 relative">
                <div class="aspect-[4/5] bg-sage-light/40 border border-beige overflow-hidden">
                    <x-storefront.image 
                        src="editorial/philosophy-botanical.jpg" 
                        alt="Botanical Still Life and Wild Harvest Plants" 
                        aspect-ratio="aspect-[4/5]"
                    />
                </div>
            </div>

            <!-- Right Side: Philosophy Copy -->
            <div class="lg:col-span-7 space-y-6">
                <span class="text-xs uppercase tracking-[0.25em] font-medium text-forest block">
                    Our Botanical Philosophy
                </span>
                <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl text-ink font-normal tracking-tight leading-tight">
                    Beauty That Grows With You.
                </h2>
                <div class="space-y-4 text-sm sm:text-base text-muted leading-relaxed font-sans">
                    <p>
                        True skincare is a daily act of listening. We formulate with carefully selected botanical ingredients to complement your skin's natural rhythm across every season.
                    </p>
                    <p>
                        From elderberry water to evening primrose oil, our recipes avoid unnecessary fillers, artificial dyes, and harsh sulfates. The result is pure, comforting skincare that works in harmony with your body.
                    </p>
                </div>
                <div class="pt-2">
                    <x-storefront.button href="/about" variant="secondary" size="md" class="text-[13px]">
                        Read Our Full Story
                    </x-storefront.button>
                </div>
            </div>

        </div>
    </section>

    <!-- 6. FEATURED COLLECTION: Large Editorial Image with Overlapping Text Panel -->
    <section class="site-container">
        <div class="relative overflow-hidden border border-beige bg-sage-light/30">
            <!-- Full Width Editorial Image Background -->
            <div class="aspect-[16/9] md:aspect-[21/9] w-full overflow-hidden">
                <x-storefront.image 
                    src="editorial/featured-collection-hydrating.jpg" 
                    alt="The Hydration Ritual Feature" 
                    aspect-ratio="aspect-[16/9] md:aspect-[21/9]" 
                    class="w-full h-full object-cover"
                />
            </div>

            <!-- Overlapping Editorial Text Card (Desktop: positioned over the right side) -->
            <div class="md:absolute md:right-8 md:top-1/2 md:-translate-y-1/2 max-w-lg bg-ivory p-6 sm:p-8 md:p-10 border border-beige shadow-soft m-4 md:m-0">
                <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block mb-2">
                    Curated Ritual
                </span>
                <h2 class="font-serif text-2xl sm:text-3xl text-ink font-normal leading-tight">
                    The Restorative Evening Protocol
                </h2>
                <p class="mt-3 text-xs sm:text-sm text-muted leading-relaxed">
                    A three-step evening sequence featuring gentle cleansing milk, bakuchiol night elixir, and camellia cream to soothe and replenish your skin barrier while you rest.
                </p>
                <div class="mt-6 flex items-center gap-4">
                    <x-storefront.button href="/shop" variant="primary" size="md" class="text-[13px]">
                        Shop The Routine
                    </x-storefront.button>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. TESTIMONIALS: 1 Large Featured Quote + 2 Smaller Quotes (NO borders, keep "Sample review") -->
    <section class="site-container" id="testimonials">
        <x-storefront.section-heading 
            eyebrow="Client Experiences" 
            title="Real Words from Our Community" 
            centered
        />

        @php
            $reviews = config('demo.testimonials');
            $featuredReview = $reviews[0] ?? null;
            $otherReviews = array_slice($reviews, 1, 2);
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center pt-4">
            
            <!-- 1 Large Featured Quote (No borders) -->
            @if($featuredReview)
                <div class="lg:col-span-7 bg-ivory p-6 sm:p-10 space-y-5">
                    <div class="flex items-center justify-between">
                        <x-storefront.rating :rating="$featuredReview['rating']" :show-count="false" />
                        <span class="text-xs uppercase tracking-widest text-muted bg-sage-light/60 px-2 py-0.5 font-medium">Sample review</span>
                    </div>

                    <blockquote class="font-serif text-2xl sm:text-3xl text-ink font-normal leading-snug italic">
                        "{{ $featuredReview['quote'] }}"
                    </blockquote>

                    <div class="pt-2 text-xs text-muted font-sans space-y-0.5">
                        <p class="font-semibold text-ink text-sm">{{ $featuredReview['author'] }}</p>
                        <p>{{ $featuredReview['location'] }} · <span class="text-forest font-medium">{{ $featuredReview['product'] }}</span></p>
                    </div>
                </div>
            @endif

            <!-- 2 Smaller Quotes (No borders) -->
            <div class="lg:col-span-5 space-y-6">
                @foreach($otherReviews as $review)
                    <div class="bg-ivory/80 p-6 space-y-3">
                        <div class="flex items-center justify-between">
                            <x-storefront.rating :rating="$review['rating']" :show-count="false" size="sm" />
                            <span class="text-xs uppercase tracking-widest text-muted bg-sage-light/50 px-2 py-0.5 font-medium">Sample review</span>
                        </div>

                        <p class="font-serif text-base text-ink italic leading-relaxed">
                            "{{ $review['quote'] }}"
                        </p>

                        <div class="pt-1 text-xs text-muted font-sans">
                            <span class="font-semibold text-ink">{{ $review['author'] }}</span> · 
                            <span class="text-forest">{{ $review['product'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- 8. INSTAGRAM-STYLE GALLERY: 6 Images, Asymmetric Grid, Link to Instagram Placeholder -->
    <section class="site-container">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
            <div>
                <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block mb-1">
                    Visual Archive
                </span>
                <h2 class="font-serif text-2xl sm:text-3xl text-ink font-normal">
                    Captured from the Apothecary
                </h2>
            </div>
            <!-- Min 13px link -->
            <a 
                href="https://instagram.com/theeverbloom" 
                target="_blank" 
                rel="noopener noreferrer" 
                class="inline-flex items-center gap-2 text-[13px] uppercase tracking-widest text-forest font-medium hover:text-ink transition-colors pb-1 border-b border-forest"
            >
                <span>Follow @theeverbloom</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>

        <!-- 6 Images Asymmetric Grid -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-3 sm:gap-4">
            <div class="md:col-span-2 aspect-square bg-sage-light/40 border border-beige overflow-hidden">
                <x-storefront.image src="gallery/gallery-botanical-01.jpg" alt="Botanical leaf texture and droplets" aspect-ratio="aspect-square" class="hover:scale-105 transition-transform duration-500" />
            </div>
            <div class="aspect-square bg-sage-light/40 border border-beige overflow-hidden">
                <x-storefront.image src="gallery/gallery-botanical-02.jpg" alt="Amber dropper bottle on linen" aspect-ratio="aspect-square" class="hover:scale-105 transition-transform duration-500" />
            </div>
            <div class="aspect-square bg-sage-light/40 border border-beige overflow-hidden">
                <x-storefront.image src="gallery/gallery-botanical-03.jpg" alt="Whipped cream swirl on ceramic dish" aspect-ratio="aspect-square" class="hover:scale-105 transition-transform duration-500" />
            </div>
            <div class="md:col-span-2 aspect-square bg-sage-light/40 border border-beige overflow-hidden">
                <x-storefront.image src="gallery/gallery-botanical-04.jpg" alt="Harvested wild chamomile flowers" aspect-ratio="aspect-square" class="hover:scale-105 transition-transform duration-500" />
            </div>
            <div class="aspect-square bg-sage-light/40 border border-beige overflow-hidden">
                <x-storefront.image src="gallery/gallery-botanical-05.jpg" alt="Pouring golden jojoba botanical oil" aspect-ratio="aspect-square" class="hover:scale-105 transition-transform duration-500" />
            </div>
            <div class="aspect-square bg-sage-light/40 border border-beige overflow-hidden">
                <x-storefront.image src="gallery/gallery-botanical-06.jpg" alt="Apothecary shelf with amber glassware" aspect-ratio="aspect-square" class="hover:scale-105 transition-transform duration-500" />
            </div>
        </div>
    </section>

</div>
@endsection
