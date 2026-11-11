@extends('layouts.app')

@section('title', 'The Everbloom | Clean Botanical Skincare')
@section('meta_description', 'Pure botanical skincare crafted for daily ritual and calm skin. Discover nutrient-dense serums, moisturizers, cleansers, and mineral sunscreens.')

@section('content')
@php
    $allProducts = collect(config('demo.products', []));
    $featuredProducts = $allProducts->where('is_featured', true)->take(4)->values()->all();
    $bestsellerProducts = $allProducts->where('is_bestseller', true)->take(4)->values()->all();
    $categories = config('demo.categories', []);
    $testimonials = array_slice(config('demo.testimonials', []), 0, 3);
    $trustItems = array_values(array_filter(config('demo.site.trust_badges', []), fn($item) => is_array($item) && isset($item['title'])));
@endphp

<!-- 1. HERO SECTION (Asymmetric 50/50 split on desktop, natural rhythm, arch frame without white border) -->
<section class="relative overflow-hidden bg-ivory pt-6 pb-16 md:py-16 lg:py-20 border-b border-[#ECEEE6]">
    <div class="site-container">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
            
            <!-- Left Editorial Column (5 cols on lg) -->
            <div class="lg:col-span-5 space-y-6 text-left order-2 lg:order-1">
                <div class="inline-flex items-center gap-2 border-b border-forest/30 pb-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-forest"></span>
                    <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest">
                        Small-Batch Botanical Formulations
                    </span>
                </div>

                <h1 class="font-serif text-5xl lg:text-6xl text-ink font-normal leading-[1.08] tracking-tight">
                    A Ritual of <span class="italic font-normal text-forest">Natural</span> Beauty.
                </h1>

                <p class="text-base text-muted leading-relaxed max-w-md font-normal">
                    Pure botanical skincare crafted for daily ritual and calm skin. Thoughtfully formulated with plant oils, hydrosols, and barrier-supportive actives.
                </p>

                <div class="pt-2 flex flex-wrap items-center gap-4">
                    <x-storefront.button href="/shop" variant="primary" size="lg">
                        Explore Collection
                    </x-storefront.button>
                    <x-storefront.button href="/about" variant="secondary" size="lg">
                        Our Philosophy
                    </x-storefront.button>
                </div>

                <!-- Subtle Editorial Credential Line (No duplicate 3-col stat block or old-style 1oo% numerals) -->
                <!-- VERIFY CLAIM before launch -->
                <div class="pt-5 border-t border-[#ECEEE6] flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-muted">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-forest/70"></span>
                        <span>pH-Balanced Formulas</span>
                    </span>
                    <span class="text-[#D5DBCF]" aria-hidden="true">&bull;</span>
                    <span>No Synthetic Fragrance</span>
                    <span class="text-[#D5DBCF]" aria-hidden="true">&bull;</span>
                    <span>Gentle Daily Rituals</span>
                </div>
            </div>

            <!-- Right Arch Visual Composition (7 cols on lg, no white border/frame, clean overlapping detail) -->
            <div class="lg:col-span-7 relative order-1 lg:order-2 flex justify-center lg:justify-end">
                <div class="relative w-full max-w-md lg:max-w-lg xl:max-w-xl lg:pl-12">
                    <!-- Main Arch Hero Image (No white frame or padding) -->
                    <div class="relative z-10 overflow-hidden rounded-t-[200px] sm:rounded-t-[260px]">
                        <x-storefront.image 
                            src="hero/hero-main.jpg" 
                            alt="The Everbloom Botanical Skincare Collection" 
                            aspect-ratio="aspect-[4/5]"
                            width="960"
                            height="1200"
                            loading="eager"
                            class="w-full"
                        />
                    </div>

                    <!-- Overlapping Secondary Botanical Detail Image (Positioned lower-left so it never hides the main subject) -->
                    <div class="hidden sm:block absolute -bottom-6 left-0 z-20 w-36 md:w-44 border border-[#E3E6DD] shadow-ambient overflow-hidden">
                        <x-storefront.image 
                            src="hero/hero-secondary.jpg" 
                            alt="Freshly pressed botanical oils and flora" 
                            aspect-ratio="aspect-square"
                            width="360"
                            height="360"
                            loading="eager"
                        />
                    </div>

                    <!-- Floating Editorial Glass Card -->
                    <div class="absolute top-6 right-3 sm:top-10 sm:-left-2 sm:right-auto z-20 glass-panel px-4 py-3 max-w-[210px] border border-[#E3E6DD]">
                        <p class="font-serif italic text-xs text-forest leading-snug">
                            "Formulated to honor your skin's natural rhythm."
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 2. TRUST / PROMISE BAR (Architectural 2x2 grid on mobile with stacked icon, 4-col row on desktop) -->
<section class="bg-[#F2EFE9]/70 border-b border-[#ECEEE6] py-8 md:py-10" aria-label="Our Standards">
    <div class="site-container">
        <div class="grid grid-cols-2 lg:grid-cols-4 bg-ivory border border-[#E3E6DD]">
            @foreach($trustItems as $idx => $badge)
                <div class="p-4 sm:p-6 flex flex-col sm:flex-row items-start gap-3 sm:gap-3.5 border-[#ECEEE6] {{ $idx % 2 === 0 ? 'border-r' : '' }} {{ $idx < 2 ? 'border-b lg:border-b-0' : '' }} {{ $idx === 1 ? 'lg:border-r' : '' }}">
                    <div class="w-9 h-9 rounded-full bg-[#F2EFE9] border border-[#E3E6DD] flex items-center justify-center shrink-0 text-forest">
                        @if(($badge['icon'] ?? '') === 'leaf')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        @elseif(($badge['icon'] ?? '') === 'heart')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        @elseif(($badge['icon'] ?? '') === 'globe')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h2 class="font-sans text-xs uppercase tracking-[0.14em] sm:tracking-[0.18em] font-semibold text-ink leading-snug lining-nums">
                            {{ $badge['title'] }}
                        </h2>
                        <p class="text-xs text-muted mt-1 leading-relaxed lining-nums">
                            {{ $badge['description'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 3. FEATURED CATEGORIES (All 6 Categories: 2 Editorial Feature Cards + 4 Targeted Category Cards, no truncated taglines) -->
<section class="py-16 md:py-20 bg-ivory">
    <div class="site-container">
        <x-storefront.section-heading 
            eyebrow="Curated Rituals" 
            title="Shop by Category" 
            description="Explore targeted botanical formulations designed to cleanse, replenish, and protect every skin state."
            link-text="View All Formulations"
            link-url="/shop"
        />

        @php
            $catBySlug = collect($categories)->keyBy('slug');
            $faceCat = $catBySlug->get('face-care', $categories[0] ?? null);
            $bodyCat = $catBySlug->get('body-care', $categories[4] ?? null);
            $subCats = [
                $catBySlug->get('serums'),
                $catBySlug->get('moisturizers'),
                $catBySlug->get('cleansers'),
                $catBySlug->get('sunscreens'),
            ];
        @endphp

        <div class="space-y-6">
            <!-- Top Row: 2 Primary Category Feature Cards (Face Care & Body Care) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($faceCat)
                    <a href="{{ url('/shop/category/' . $faceCat['slug']) }}" class="group relative overflow-hidden bg-sage-light/30 flex flex-col justify-end min-h-[340px] sm:min-h-[380px] p-6 sm:p-8 border border-[#E3E6DD]">
                        <div class="absolute inset-0 z-0">
                            <x-storefront.image 
                                :src="$faceCat['image']" 
                                :alt="$faceCat['name']" 
                                aspect-ratio="h-full w-full"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-ink/80 via-ink/30 to-transparent"></div>
                        </div>
                        <div class="relative z-10 text-ivory max-w-md">
                            <span class="text-xs uppercase tracking-[0.2em] text-ivory/90 block mb-1.5">
                                {{ $faceCat['item_count'] }} Formulations
                            </span>
                            <h3 class="font-serif text-2xl sm:text-3xl font-normal tracking-tight text-ivory">
                                {{ $faceCat['name'] }}
                            </h3>
                            <p class="text-xs sm:text-sm text-ivory/90 mt-1.5 leading-relaxed">
                                {{ $faceCat['tagline'] }}
                            </p>
                            <span class="mt-4 inline-flex items-center gap-2 text-xs uppercase tracking-[0.2em] font-medium text-ivory border-b border-ivory/60 pb-1 group-hover:border-ivory transition-colors">
                                <span>Explore {{ $faceCat['name'] }}</span>
                                <span aria-hidden="true">&rarr;</span>
                            </span>
                        </div>
                    </a>
                @endif

                @if($bodyCat)
                    <a href="{{ url('/shop/category/' . $bodyCat['slug']) }}" class="group relative overflow-hidden bg-sage-light/30 flex flex-col justify-end min-h-[340px] sm:min-h-[380px] p-6 sm:p-8 border border-[#E3E6DD]">
                        <div class="absolute inset-0 z-0">
                            <x-storefront.image 
                                :src="$bodyCat['image']" 
                                :alt="$bodyCat['name']" 
                                aspect-ratio="h-full w-full"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-ink/80 via-ink/30 to-transparent"></div>
                        </div>
                        <div class="relative z-10 text-ivory max-w-md">
                            <span class="text-xs uppercase tracking-[0.2em] text-ivory/90 block mb-1.5">
                                {{ $bodyCat['item_count'] }} Formulations
                            </span>
                            <h3 class="font-serif text-2xl sm:text-3xl font-normal tracking-tight text-ivory">
                                {{ $bodyCat['name'] }}
                            </h3>
                            <p class="text-xs sm:text-sm text-ivory/90 mt-1.5 leading-relaxed">
                                {{ $bodyCat['tagline'] }}
                            </p>
                            <span class="mt-4 inline-flex items-center gap-2 text-xs uppercase tracking-[0.2em] font-medium text-ivory border-b border-ivory/60 pb-1 group-hover:border-ivory transition-colors">
                                <span>Explore {{ $bodyCat['name'] }}</span>
                                <span aria-hidden="true">&rarr;</span>
                            </span>
                        </div>
                    </a>
                @endif
            </div>

            <!-- Bottom Row: 4 Equal Columns on Desktop (2x2 on Mobile) with full taglines (no ellipsis) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach(array_filter($subCats) as $cat)
                    <a href="{{ url('/shop/category/' . $cat['slug']) }}" class="group relative overflow-hidden bg-sage-light/30 flex flex-col justify-end aspect-[4/5] p-5 sm:p-6 border border-[#E3E6DD]">
                        <div class="absolute inset-0 z-0">
                            <x-storefront.image 
                                :src="$cat['image']" 
                                :alt="$cat['name']" 
                                aspect-ratio="h-full w-full"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-ink/85 via-ink/35 to-transparent"></div>
                        </div>
                        <div class="relative z-10 text-ivory">
                            <span class="text-xs uppercase tracking-[0.2em] text-ivory/90 block mb-1">
                                {{ $cat['item_count'] }} Products
                            </span>
                            <h3 class="font-serif text-lg sm:text-xl font-normal tracking-tight text-ivory group-hover:underline">
                                {{ $cat['name'] }}
                            </h3>
                            <p class="text-xs text-ivory/85 mt-1 leading-relaxed hidden sm:block">
                                {{ $cat['tagline'] }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- 4. FEATURED PRODUCTS (4 Products) -->
<section class="py-16 md:py-20 bg-beige/25 border-y border-[#ECEEE6]">
    <div class="site-container">
        <x-storefront.section-heading 
            eyebrow="Seasonal Edit" 
            title="Featured Botanicals" 
            description="Thoughtfully selected formulations to restore hydration, luminosity, and calm."
            link-text="Shop All Products"
            link-url="/shop"
        />

        <x-storefront.product-grid :products="$featuredProducts" columns="4" />
    </div>
</section>

<!-- 5. EDITORIAL STORY / BRAND PHILOSOPHY SPLIT (Borderless/bleeding image, max-h-[560px] aspect-[4/5]) -->
<section class="py-16 md:py-20 bg-ivory overflow-hidden">
    <div class="site-container">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
            
            <!-- Left Visual Column (Borderless, bleeding aesthetic, max-h-[560px] aspect-[4/5]) -->
            <div class="lg:col-span-6 relative">
                <div class="relative z-10 overflow-hidden max-h-[560px] aspect-[4/5] w-full">
                    <x-storefront.image 
                        src="editorial/about-founder.jpg" 
                        alt="Botanical extraction and slow infusion process at The Everbloom" 
                        aspect-ratio="aspect-[4/5] max-h-[560px]"
                        width="800"
                        height="1000"
                    />
                </div>
            </div>

            <!-- Right Editorial Narrative Column -->
            <div class="lg:col-span-6 space-y-6">
                <span class="block text-xs uppercase tracking-[0.2em] font-medium text-forest">
                    The Everbloom Philosophy
                </span>

                <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl text-ink font-normal leading-[1.15]">
                    Slow Skincare for <span class="italic text-forest">Resilient</span> Skin Barriers.
                </h2>

                <!-- VERIFY CLAIM before launch -->
                <p class="text-base text-muted leading-relaxed">
                    We believe skincare should never overwhelm the skin. Rather than chasing aggressive quick fixes, our formulations focus on lipid replenishment, pH harmony, and gentle botanical nourishment.
                </p>

                <!-- VERIFY CLAIM before launch -->
                <p class="text-sm text-muted leading-relaxed">
                    Every bottle begins with carefully selected seeds, flowers, and roots—prepared in small batches and housed in UV-protective amber glass to safeguard potency from our studio to your vanity.
                </p>

                <!-- Ingredient Highlight Pills -->
                <div class="pt-2 grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-[#ECEEE6]">
                    <div class="pt-4">
                        <h3 class="font-serif text-lg text-ink font-normal">Whole-Plant Infusions</h3>
                        <p class="text-xs text-muted mt-1 leading-relaxed">
                            Formulated with nutrient-rich plant oils, hydrosols, and soothing botanical extracts.
                        </p>
                    </div>
                    <div class="pt-4">
                        <h3 class="font-serif text-lg text-ink font-normal">Barrier-First Science</h3>
                        <p class="text-xs text-muted mt-1 leading-relaxed">
                            Biocompatible squalane, ceramides, and niacinamide paired with gentle plant actives.
                        </p>
                    </div>
                </div>

                <div class="pt-2">
                    <x-storefront.button href="/about" variant="secondary">
                        Read Our Full Story
                    </x-storefront.button>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 6. BEST SELLERS (4 Products) -->
<section class="py-16 md:py-20 bg-sage-light/30 border-t border-[#ECEEE6]">
    <div class="site-container">
        <x-storefront.section-heading 
            eyebrow="Community Favorites" 
            title="Most Loved Rituals" 
            description="Our foundational daily essentials—trusted for morning protection and evening recovery."
            link-text="Shop Best Sellers"
            link-url="/shop?sort=best_selling"
        />

        <x-storefront.product-grid :products="$bestsellerProducts" columns="4" />
    </div>
</section>

<!-- 7. TESTIMONIALS ("What Our Community Says", labeled "Sample review", top-aligned, no "Verified" labels) -->
<section class="py-16 md:py-20 bg-ivory border-t border-[#ECEEE6]">
    <div class="site-container">
        <x-storefront.section-heading 
            eyebrow="Community Reflections" 
            title="What Our Community Says" 
            description="Experiences shared by rituals practiced morning and evening."
            :centered="true"
        />

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 items-start">
            @foreach($testimonials as $index => $review)
                <div class="bg-[#F2EFE9]/60 border border-[#E3E6DD] p-7 sm:p-8 flex flex-col justify-between transition-all duration-300">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-5">
                            <x-storefront.rating :rating="$review['rating']" :show-count="false" size="sm" />
                            <span class="text-xs uppercase tracking-[0.2em] text-muted border border-[#E3E6DD] bg-ivory px-2.5 py-0.5">
                                Sample review
                            </span>
                        </div>

                        <blockquote class="font-serif italic text-lg sm:text-xl text-ink leading-relaxed mb-6">
                            "{{ $review['quote'] }}"
                        </blockquote>
                    </div>

                    <div class="pt-5 border-t border-[#ECEEE6] flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-full overflow-hidden shrink-0 border border-[#E3E6DD]">
                            <x-storefront.image 
                                :src="$review['avatar'] ?? ''" 
                                :alt="$review['author']" 
                                aspect-ratio="aspect-square"
                                :rounded="true"
                                width="88"
                                height="88"
                            />
                        </div>
                        <div class="min-w-0">
                            <p class="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-ink truncate">
                                {{ $review['author'] }}
                            </p>
                            <p class="text-xs text-muted truncate mt-0.5">
                                {{ $review['skin_type'] ?? $review['location'] ?? 'Daily Ritual' }} &middot; <span class="text-forest">{{ $review['product'] ?? 'Botanical Collection' }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 8. BOTANICAL GALLERY (No gaps, no giant panorama tile, 6 uniform square tiles) -->
<section class="py-16 md:py-20 bg-ivory border-t border-[#ECEEE6]">
    <div class="site-container">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
            <div>
                <span class="block text-xs uppercase tracking-[0.2em] font-medium text-forest mb-1.5">
                    @@theeverbloom.botanicals
                </span>
                <h2 class="font-serif text-2xl sm:text-3xl text-ink font-normal">
                    Captured from the Studio
                </h2>
            </div>
            <a href="/about" class="text-xs uppercase tracking-[0.2em] font-medium text-forest hover:text-ink border-b border-forest pb-0.5 self-start sm:self-auto transition-colors">
                Explore Our Rituals &rarr;
            </a>
        </div>

        @php
            $galleryItems = [
                ['src' => 'gallery/gallery-1.jpg', 'alt' => 'Morning botanical oil ritual'],
                ['src' => 'gallery/gallery-2.jpg', 'alt' => 'Dried calendula and chamomile petals'],
                ['src' => 'gallery/gallery-3.jpg', 'alt' => 'Amber glass dropper bottles in sunlight'],
                ['src' => 'gallery/gallery-4.jpg', 'alt' => 'Rich cream texture swatch on linen'],
                ['src' => 'gallery/gallery-5.jpg', 'alt' => 'Green tea and bamboo cleanser mousse'],
                ['src' => 'gallery/gallery-6.jpg', 'alt' => 'Evening self-care sanctuary'],
            ];
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-0 border border-[#E3E6DD]">
            @foreach($galleryItems as $item)
                <div class="group relative overflow-hidden aspect-square">
                    <x-storefront.image 
                        :src="$item['src']" 
                        :alt="$item['alt']" 
                        aspect-ratio="aspect-square"
                        width="480"
                        height="480"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                    />
                    <div class="absolute inset-0 bg-forest/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4 pointer-events-none">
                        <span class="text-xs text-ivory font-sans tracking-wide leading-snug">{{ $item['alt'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
