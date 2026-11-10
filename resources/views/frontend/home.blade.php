@extends('layouts.app')

@section('title', config('demo.site.name') . ' — ' . config('demo.site.tagline'))
@section('meta_description', 'Thoughtfully formulated for everyday rituals using carefully selected botanical ingredients. Naturally beautiful, always blooming.')

@section('content')
<div class="space-y-12 md:space-y-16 pb-12 md:pb-16">

    <!-- 1. HERO SECTION: Asymmetric Editorial, Overlapping Images, py-12 md:py-16 -->
    <!-- VERIFY CLAIM before launch: Review botanical ingredient statements -->
    <section class="relative bg-sage-light/30 border-b border-beige/70 py-12 md:py-16 overflow-hidden">
        <div class="site-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
                
                <!-- Left Text Column -->
                <div class="lg:col-span-7 space-y-5">
                    <!-- Small Label Above Headline -->
                    <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block">
                        {{ config('demo.site.tagline') }}
                    </span>

                    <!-- Editorial Asymmetric Headline (H1) -->
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
                        <div class="absolute -bottom-6 -left-6 sm:-bottom-8 sm:-left-8 w-44 sm:w-52 aspect-[3/4] overflow-hidden border border-beige-dark/40 shadow-soft bg-ivory p-1 hidden sm:block">
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

    <!-- 2. WHY CHOOSE THE EVERBLOOM: 4 Points in a simple icon + text row (NO cards, 13px description) -->
    <!-- VERIFY CLAIM before launch: Review packaging recyclability & formulation test claims -->
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
                    <p class="text-[13px] text-muted leading-relaxed mt-1">Formulated with plant-derived extracts and gentle floral waters.</p>
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
                    <p class="text-[13px] text-muted leading-relaxed mt-1">Crafted for everyday rituals to support skin barrier balance.</p>
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
                    <p class="text-[13px] text-muted leading-relaxed mt-1">Packaged in amber UV-protective bottles and recyclable cartons.</p>
                </div>
            </div>

            <!-- Point 4: "Gentle Formulations" (Instruction 3) -->
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 shrink-0 text-forest flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-serif text-base text-ink font-medium">Gentle Formulations</h3>
                    <p class="text-[13px] text-muted leading-relaxed mt-1">Formulated without synthetic dyes or harsh artificial fragrances.</p>
                </div>
            </div>

        </div>
    </section>

    <!-- 3. CATEGORIES: All 6 Categories in 2 Large (Row 1) + 4 Small (Row 2), Borderless Tiles -->
    <section class="site-container">
        <x-storefront.section-heading 
            eyebrow="Targeted Rituals" 
            title="Curated by Skin Goal" 
            description="Explore all six specialized formulations crafted for face, body, and daily sun care."
            link-text="Shop All Categories" 
            link-url="/shop"
        />

        @php
            $cats = collect(config('demo.categories'));
            $faceCat = $cats->firstWhere('slug', 'face-care');
            $bodyCat = $cats->firstWhere('slug', 'body-care');
            $serumCat = $cats->firstWhere('slug', 'serums');
            $moistCat = $cats->firstWhere('slug', 'moisturizers');
            $cleanCat = $cats->firstWhere('slug', 'cleansers');
            $sunCat = $cats->firstWhere('slug', 'sunscreens');
        @endphp

        <!-- 6-Category Asymmetric Borderless Grid -->
        <div class="grid grid-cols-2 md:grid-cols-12 gap-5 sm:gap-6">
            
            <!-- Row 1 - Large Card 1: Face Care (Half width, borderless, text below) -->
            @if($faceCat)
                <div class="col-span-2 md:col-span-6 group block">
                    <a href="/shop/category/{{ $faceCat['slug'] }}" class="block">
                        <div class="aspect-[16/10] overflow-hidden bg-sage-light/40">
                            <x-storefront.image 
                                :src="$faceCat['image']" 
                                :alt="$faceCat['name']" 
                                aspect-ratio="aspect-[16/10]" 
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            />
                        </div>
                        <div class="pt-3.5 flex items-baseline justify-between">
                            <div>
                                <span class="text-xs uppercase tracking-[0.2em] text-muted block">Essential Collection</span>
                                <h3 class="font-serif text-xl sm:text-2xl text-ink font-normal group-hover:text-forest transition-colors mt-0.5">
                                    {{ $faceCat['name'] }}
                                </h3>
                                <p class="text-[13px] text-muted mt-1 max-w-md line-clamp-1">{{ $faceCat['tagline'] }}</p>
                            </div>
                            <span class="text-[13px] uppercase tracking-widest text-forest font-medium group-hover:translate-x-1 transition-transform shrink-0 ml-4">
                                Explore →
                            </span>
                        </div>
                    </a>
                </div>
            @endif

            <!-- Row 1 - Large Card 2: Body Care (Half width, borderless, text below) -->
            @if($bodyCat)
                <div class="col-span-2 md:col-span-6 group block">
                    <a href="/shop/category/{{ $bodyCat['slug'] }}" class="block">
                        <div class="aspect-[16/10] overflow-hidden bg-sage-light/40">
                            <x-storefront.image 
                                :src="$bodyCat['image']" 
                                :alt="$bodyCat['name']" 
                                aspect-ratio="aspect-[16/10]" 
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            />
                        </div>
                        <div class="pt-3.5 flex items-baseline justify-between">
                            <div>
                                <span class="text-xs uppercase tracking-[0.2em] text-muted block">Full Body Therapies</span>
                                <h3 class="font-serif text-xl sm:text-2xl text-ink font-normal group-hover:text-forest transition-colors mt-0.5">
                                    {{ $bodyCat['name'] }}
                                </h3>
                                <p class="text-[13px] text-muted mt-1 max-w-md line-clamp-1">{{ $bodyCat['tagline'] }}</p>
                            </div>
                            <span class="text-[13px] uppercase tracking-widest text-forest font-medium group-hover:translate-x-1 transition-transform shrink-0 ml-4">
                                Explore →
                            </span>
                        </div>
                    </a>
                </div>
            @endif

            <!-- Row 2 - 4 Smaller Borderless Tiles: Serums, Moisturizers, Cleansers, Sunscreens -->
            @foreach([$serumCat, $moistCat, $cleanCat, $sunCat] as $cat)
                @if($cat)
                    <div class="col-span-1 md:col-span-3 group block">
                        <a href="/shop/category/{{ $cat['slug'] }}" class="block">
                            <div class="aspect-square overflow-hidden bg-sage-light/40">
                                <x-storefront.image 
                                    :src="$cat['image']" 
                                    :alt="$cat['name']" 
                                    aspect-ratio="aspect-square" 
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                            </div>
                            <div class="pt-3">
                                <h3 class="font-serif text-base text-ink font-medium group-hover:text-forest transition-colors">
                                    {{ $cat['name'] }}
                                </h3>
                                <p class="text-xs text-muted uppercase tracking-wider mt-0.5">
                                    {{ $cat['item_count'] }} Formulations
                                </p>
                            </div>
                        </a>
                    </div>
                @endif
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

    <!-- 5. BRAND PHILOSOPHY: Left-Bleeding Image, Text on Ivory, No Bordered Card (Instruction 4) -->
    <!-- VERIFY CLAIM before launch: Review plant extract claims and formulation notes -->
    <section class="py-12 md:py-16">
        <div class="site-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
                
                <!-- Left: Full-Height Bleeding Botanical Image (No card frame) -->
                <div class="lg:col-span-6">
                    <div class="aspect-[4/5] sm:aspect-[16/11] lg:aspect-[4/5] overflow-hidden bg-sage-light/40">
                        <x-storefront.image 
                            src="editorial/philosophy-botanical.jpg" 
                            alt="Botanical Still Life and Harvest Plants" 
                            aspect-ratio="aspect-[4/5] sm:aspect-[16/11] lg:aspect-[4/5]"
                            class="w-full h-full object-cover"
                        />
                    </div>
                </div>

                <!-- Right: Unboxed Typography Directly on Ivory Canvas -->
                <div class="lg:col-span-6 space-y-5 lg:pl-6">
                    <span class="text-xs uppercase tracking-[0.25em] font-medium text-forest block">
                        Our Botanical Philosophy
                    </span>
                    <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl text-ink font-normal tracking-tight leading-tight">
                        Beauty That Grows With You.
                    </h2>
                    <div class="space-y-4 text-[13px] sm:text-base text-muted leading-relaxed font-sans">
                        <p>
                            True skincare is a daily act of listening. We formulate with carefully selected botanical ingredients to complement your skin's natural rhythm across every season.
                        </p>
                        <p>
                            From elderberry extract to evening primrose oil, our recipes avoid unnecessary fillers, artificial dyes, and harsh sulfates. The result is pure, comforting skincare that works in harmony with your body.
                        </p>
                    </div>
                    <div class="pt-3">
                        <x-storefront.button href="/about" variant="secondary" size="md" class="text-[13px]">
                            Read Our Full Story
                        </x-storefront.button>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 6. FEATURED COLLECTION: Large Editorial Image with Overlapping Text Panel -->
    <!-- VERIFY CLAIM before launch: Review evening sequence formulation claims -->
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
                <p class="mt-3 text-xs sm:text-[13px] text-muted leading-relaxed">
                    A three-step evening sequence featuring gentle cleansing milk, bakuchiol night elixir, and camellia cream to soothe and replenish your skin barrier while you rest.
                </p>
                <div class="mt-5 flex items-center gap-4">
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

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center pt-2">
            
            <!-- 1 Large Featured Quote (No borders) -->
            @if($featuredReview)
                <div class="lg:col-span-7 bg-ivory p-6 sm:p-10 space-y-4">
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
            <div class="lg:col-span-5 space-y-5">
                @foreach($otherReviews as $review)
                    <div class="bg-ivory/80 p-5 space-y-2.5">
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

    <!-- 8. INSTAGRAM-STYLE GALLERY: 4-Column Desktop (2 Mobile), Item 1 Spans 2x2, Gap-3, No Empty Cells (Instruction 1) -->
    <section class="site-container py-12 md:py-16">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
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

        <!-- 4-Column Desktop / 2-Column Mobile Grid with Dense Packing and Equal gap-3 -->
        <div class="grid grid-cols-2 md:grid-cols-4 grid-flow-dense gap-3">
            
            <!-- Item 1: Spans 2 columns and 2 rows on desktop (and full width on mobile) -->
            <div class="col-span-2 row-span-2 md:col-span-2 md:row-span-2 overflow-hidden bg-sage-light/40 border border-beige">
                <x-storefront.image 
                    src="gallery/gallery-botanical-01.jpg" 
                    alt="Botanical leaf texture and water droplets" 
                    aspect-ratio="aspect-square" 
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" 
                />
            </div>

            <!-- Item 2: 1 Cell (Row 1, Col 3 on desktop) -->
            <div class="col-span-1 overflow-hidden bg-sage-light/40 border border-beige">
                <x-storefront.image 
                    src="gallery/gallery-botanical-02.jpg" 
                    alt="Amber dropper bottle on natural linen" 
                    aspect-ratio="aspect-square" 
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" 
                />
            </div>

            <!-- Item 3: 1 Cell (Row 1, Col 4 on desktop) -->
            <div class="col-span-1 overflow-hidden bg-sage-light/40 border border-beige">
                <x-storefront.image 
                    src="gallery/gallery-botanical-03.jpg" 
                    alt="Whipped cream swirl on handmade ceramic dish" 
                    aspect-ratio="aspect-square" 
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" 
                />
            </div>

            <!-- Item 4: 1 Cell (Row 2, Col 3 on desktop) -->
            <div class="col-span-1 overflow-hidden bg-sage-light/40 border border-beige">
                <x-storefront.image 
                    src="gallery/gallery-botanical-04.jpg" 
                    alt="Harvested wild chamomile flowers in morning light" 
                    aspect-ratio="aspect-square" 
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" 
                />
            </div>

            <!-- Item 5: 1 Cell (Row 2, Col 4 on desktop) -->
            <div class="col-span-1 overflow-hidden bg-sage-light/40 border border-beige">
                <x-storefront.image 
                    src="gallery/gallery-botanical-05.jpg" 
                    alt="Pouring golden jojoba botanical oil into beaker" 
                    aspect-ratio="aspect-square" 
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" 
                />
            </div>

            <!-- Item 6: Spans full width of Row 3 on desktop (cols 1-4) and Row 4 on mobile (cols 1-2) with no empty cells -->
            <div class="col-span-2 md:col-span-4 overflow-hidden bg-sage-light/40 border border-beige">
                <x-storefront.image 
                    src="gallery/gallery-botanical-06.jpg" 
                    alt="Apothecary shelf with amber glassware and botanical notebooks" 
                    aspect-ratio="aspect-[2/1] sm:aspect-[21/9]" 
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" 
                />
            </div>

        </div>
    </section>

</div>
@endsection
