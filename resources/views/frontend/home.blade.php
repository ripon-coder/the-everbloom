@extends('layouts.app')

@section('title', 'The Everbloom | Botanical Skincare')
@section('meta_description', 'Thoughtful botanical skincare crafted for everyday rituals. Discover serums, moisturizers, cleansers, and mineral sunscreens.')

@section('content')
@php
    $allProducts = collect(config('demo.products', []));
    $featuredProducts = $allProducts->where('is_featured', true)->take(4)->values()->all();
    $bestsellerProducts = $allProducts->where('best_seller', true)
        ->whereNotIn('id', collect($featuredProducts)->pluck('id')->all())
        ->take(4)
        ->values()
        ->all();
    if (empty($bestsellerProducts)) {
        $bestsellerProducts = $allProducts->whereNotIn('id', collect($featuredProducts)->pluck('id')->all())->take(4)->values()->all();
    }
    $categories = config('demo.categories', []);
    $testimonials = array_slice(config('demo.testimonials', []), 0, 3);
    $trustItems = array_values(array_filter(config('demo.site.trust_badges', []), fn($item) => is_array($item) && isset($item['title'])));
@endphp

<!-- 1. HERO SECTION -->
<section class="relative overflow-hidden bg-ivory pt-6 pb-8 lg:py-16 border-b border-[#ECEEE6]">
    <div class="site-container">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-12 items-center">
            
            <!-- Editorial Text Column (First in DOM for mobile & desktop left) -->
            <div class="lg:col-span-5 space-y-3 sm:space-y-4 lg:space-y-5 text-left">
                <div class="inline-flex items-center gap-2 border-b border-forest/30 pb-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-forest"></span>
                    <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest">
                        Botanical Skincare Formulations
                    </span>
                </div>

                <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl text-ink font-normal leading-[1.08] tracking-tight text-balance">
                    A Ritual of <span class="italic font-normal text-forest">Natural</span> Beauty.
                </h1>

                <p class="text-base text-muted leading-relaxed max-w-md font-normal">
                    Thoughtful formulations crafted for everyday skincare rituals. Made with carefully selected ingredients to support comfort and daily hydration.
                </p>

                <div class="pt-1 flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-center gap-3 sm:gap-4">
                    <x-storefront.button href="/shop" variant="primary" size="lg" class="w-full sm:w-auto min-h-[48px]">
                        Explore Collection
                    </x-storefront.button>
                    <x-storefront.button href="/about" variant="secondary" size="lg" class="w-full sm:w-auto min-h-[48px]">
                        Our Philosophy
                    </x-storefront.button>
                </div>

                <!-- Neutral Editorial Credential List (Vertical on mobile, clean wrapping row on sm+ with no stray dividers) -->
                <ul class="pt-3 lg:pt-4 border-t border-[#ECEEE6] flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-2 sm:gap-x-5 sm:gap-y-2 text-sm sm:text-[13px] text-muted">
                    <li class="inline-flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-forest shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Carefully Selected Ingredients</span>
                    </li>
                    <li class="inline-flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-forest shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Thoughtful Formulations</span>
                    </li>
                    <li class="inline-flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-forest shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Everyday Skincare Rituals</span>
                    </li>
                </ul>
            </div>

            <!-- Right Arch Visual Composition (7 cols on lg, below text on mobile) -->
            <div class="lg:col-span-7 relative flex justify-center lg:justify-end">
                <div class="relative w-full max-w-[320px] mx-auto lg:mx-0 lg:max-w-lg xl:max-w-xl">
                    <!-- Main Arch Hero Image (max-h-[50vh] on mobile, uncapped aspect-[4/5] on lg+) -->
                    <div class="relative z-10 w-full aspect-[4/5] max-h-[50vh] lg:max-h-none overflow-hidden rounded-t-[999px] bg-sage-light/60">
                        <x-storefront.image 
                            src="hero/hero-main.jpg" 
                            alt="The Everbloom Botanical Skincare Collection" 
                            aspect-ratio=""
                            width="960"
                            height="1200"
                            loading="eager"
                            class="!absolute inset-0 w-full h-full object-cover"
                        />
                    </div>

                    <!-- Caption Quote Panel: left-rule caption on mobile (no box/bg), overlapping bottom-left edge on lg+ -->
                    {{-- // DEMO COPY --}}
                    <div class="mt-4 w-full border-l-2 border-[#A8B7A0] pl-4 py-0.5 lg:mt-0 lg:bg-white lg:border lg:border-[#E3E6DD] lg:p-5 lg:absolute lg:-left-6 lg:bottom-10 lg:max-w-[260px] z-20">
                        <p class="font-serif italic text-[15px] text-ink/80 lg:text-forest leading-snug">
                            "Formulated to honor your skin's natural rhythm."
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 2. TRUST / PROMISE BAR (No outer box, thin vertical dividers, 2x2 on mobile, 13px/14px text) -->
<section class="bg-[#F2EFE9]/70 border-b border-[#ECEEE6] py-10 lg:py-12" aria-label="Our Standards">
    <div class="site-container">
        {{-- // DEMO PLACEHOLDER: verify before launch --}}
        <!-- VERIFY CLAIM before launch -->
        <div class="grid grid-cols-2 lg:grid-cols-4 divide-y lg:divide-y-0 lg:divide-x divide-[#D5DBCF]">
            @foreach($trustItems as $idx => $badge)
                <div class="p-4 lg:px-6 lg:py-2 flex flex-col sm:flex-row items-start gap-3 {{ $idx % 2 === 0 ? 'border-r lg:border-r-0 border-[#D5DBCF]' : '' }} {{ $idx < 2 ? '' : 'border-b-0' }}">
                    <div class="w-9 h-9 rounded-full bg-ivory border border-[#E3E6DD] flex items-center justify-center shrink-0 text-forest">
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
                        <h2 class="font-sans text-sm font-medium text-ink leading-snug lining-nums">
                            {{ $badge['title'] }}
                        </h2>
                        <p class="text-[13px] text-muted mt-1 leading-relaxed lining-nums">
                            {{ $badge['description'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 3. FEATURED CATEGORIES (2 Large Feature Cards + 4 Clean Text-Below Category Tiles) -->
<section class="py-10 lg:py-16 bg-ivory">
    <div class="site-container">
        <x-storefront.section-heading 
            eyebrow="Curated Rituals" 
            title="Shop by Category" 
            description="Explore botanical formulations designed to cleanse, replenish, and protect every skin state."
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

        <div class="space-y-8">
            <!-- Top Row: 2 Primary Category Feature Cards (Strong dark gradient overlay, 15px description) -->
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
                            <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(41,45,41,0.75) 0%, rgba(41,45,41,0.35) 55%, transparent 100%);"></div>
                        </div>
                        <div class="relative z-10 text-ivory max-w-md">
                            <span class="text-[13px] uppercase tracking-[0.2em] text-ivory/95 block mb-1.5">
                                {{ $faceCat['item_count'] }} Formulations
                            </span>
                            <h3 class="font-serif text-2xl sm:text-3xl font-normal tracking-tight text-ivory">
                                {{ $faceCat['name'] }}
                            </h3>
                            <p class="text-[15px] text-ivory/95 mt-1.5 leading-relaxed">
                                {{ $faceCat['tagline'] }}
                            </p>
                            <span class="mt-4 inline-flex items-center gap-2 text-[13px] uppercase tracking-[0.2em] font-medium text-ivory border-b border-ivory/70 pb-1 group-hover:border-ivory transition-colors">
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
                            <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(41,45,41,0.75) 0%, rgba(41,45,41,0.35) 55%, transparent 100%);"></div>
                        </div>
                        <div class="relative z-10 text-ivory max-w-md">
                            <span class="text-[13px] uppercase tracking-[0.2em] text-ivory/95 block mb-1.5">
                                {{ $bodyCat['item_count'] }} Formulations
                            </span>
                            <h3 class="font-serif text-2xl sm:text-3xl font-normal tracking-tight text-ivory">
                                {{ $bodyCat['name'] }}
                            </h3>
                            <p class="text-[15px] text-ivory/95 mt-1.5 leading-relaxed">
                                {{ $bodyCat['tagline'] }}
                            </p>
                            <span class="mt-4 inline-flex items-center gap-2 text-[13px] uppercase tracking-[0.2em] font-medium text-ivory border-b border-ivory/70 pb-1 group-hover:border-ivory transition-colors">
                                <span>Explore {{ $bodyCat['name'] }}</span>
                                <span aria-hidden="true">&rarr;</span>
                            </span>
                        </div>
                    </a>
                @endif
            </div>

            <!-- Bottom Row: 4 Category Tiles with Text Below Image on Ivory Background -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach(array_filter($subCats) as $cat)
                    <a href="{{ url('/shop/category/' . $cat['slug']) }}" class="group flex flex-col bg-ivory">
                        <div class="relative overflow-hidden aspect-[4/5] bg-sage-light/30 border border-[#E3E6DD]">
                            <x-storefront.image 
                                :src="$cat['image']" 
                                :alt="$cat['name']" 
                                aspect-ratio="aspect-[4/5]"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                        </div>
                        <div class="pt-3 space-y-1">
                            <div class="flex items-baseline justify-between gap-2">
                                <h3 class="font-serif text-[18px] font-normal tracking-tight text-ink group-hover:text-forest transition-colors">
                                    {{ $cat['name'] }}
                                </h3>
                                <span class="text-[13px] text-muted shrink-0">
                                    {{ $cat['item_count'] }} items
                                </span>
                            </div>
                            <p class="text-sm text-muted leading-snug line-clamp-1">
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
<section class="py-10 lg:py-16 bg-beige/25 border-y border-[#ECEEE6]">
    <div class="site-container">
        <x-storefront.section-heading 
            eyebrow="Seasonal Edit" 
            title="Featured Botanicals" 
            description="Thoughtfully selected formulations to support hydration, luminosity, and daily comfort."
            link-text="Shop All Products"
            link-url="/shop"
        />

        <x-storefront.product-grid :products="$featuredProducts" columns="4" />
    </div>
</section>

<!-- 5. EDITORIAL STORY / BRAND PHILOSOPHY SPLIT -->
<section class="py-10 lg:py-16 bg-ivory overflow-hidden">
    <div class="site-container">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
            
            <!-- Left Visual Column -->
            <div class="lg:col-span-6 relative">
                <div class="relative z-10 overflow-hidden max-h-[560px] aspect-[4/5] w-full">
                    <x-storefront.image 
                        src="editorial/about-founder.jpg" 
                        alt="Botanical studio apothecary at The Everbloom" 
                        aspect-ratio="aspect-[4/5] max-h-[560px]"
                        width="800"
                        height="1000"
                    />
                </div>
            </div>

            <!-- Right Editorial Narrative Column -->
            <div class="lg:col-span-6 space-y-5">
                <span class="block text-xs uppercase tracking-[0.2em] font-medium text-forest">
                    The Everbloom Philosophy
                </span>

                <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl text-ink font-normal leading-[1.15]">
                    Slow Skincare for <span class="italic text-forest">Resilient</span> Skin Barriers.
                </h2>

                <!-- VERIFY CLAIM before launch -->
                <p class="text-base text-muted leading-relaxed">
                    We believe skincare should never overwhelm the skin. Rather than chasing aggressive quick fixes, our thoughtful formulations focus on comfort, daily hydration, and gentle nourishment.
                </p>

                <!-- VERIFY CLAIM before launch -->
                <p class="text-sm text-muted leading-relaxed">
                    Every bottle begins with carefully selected ingredients—thoughtfully prepared and housed in amber glass vessels for your daily routine.
                </p>

                <!-- Ingredient Highlight Columns -->
                <div class="pt-2 grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-[#ECEEE6]">
                    <div class="pt-3">
                        <h3 class="font-serif text-lg text-ink font-normal">Carefully Selected Ingredients</h3>
                        <p class="text-sm text-muted mt-1 leading-relaxed">
                            Formulated with botanical oils, hydrosols, and soothing extracts for everyday skincare rituals.
                        </p>
                    </div>
                    <div class="pt-3">
                        <h3 class="font-serif text-lg text-ink font-normal">Thoughtful Formulations</h3>
                        <p class="text-sm text-muted mt-1 leading-relaxed">
                            Balanced textures with squalane, ceramides, and niacinamide designed for gentle daily use.
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

<!-- 6. MOST LOVED RITUALS (4 distinct best_seller products) -->
<section class="py-10 lg:py-16 bg-sage-light/30 border-t border-[#ECEEE6]">
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

<!-- 7. TESTIMONIALS (1 large featured quote left + 2 smaller quotes right divided by thin lines, initials circles) -->
<section class="py-10 lg:py-16 bg-ivory border-t border-[#ECEEE6]">
    <div class="site-container">
        <x-storefront.section-heading 
            eyebrow="Community Reflections" 
            title="What Our Community Says" 
            description="Experiences shared by rituals practiced morning and evening."
            :centered="true"
        />

        @php
            $featuredQuote = $testimonials[0] ?? null;
            $secondaryQuotes = array_slice($testimonials, 1, 2);
            $getInitials = function (string $name): string {
                $parts = preg_split('/\s+/', trim($name));
                $first = mb_substr($parts[0] ?? '', 0, 1);
                $last = mb_substr($parts[count($parts) - 1] ?? '', 0, 1);
                return mb_strtoupper($first . ($count = count($parts) > 1 ? $last : ''));
            };
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            <!-- Left: Large Featured Quote (No border, Playfair italic 24px) -->
            @if($featuredQuote)
                <div class="lg:col-span-6 space-y-5">
                    <div class="flex items-center justify-between gap-3">
                        <x-storefront.rating :rating="$featuredQuote['rating']" :show-count="false" size="sm" />
                        <span class="text-xs uppercase tracking-[0.2em] text-muted border border-[#E3E6DD] bg-white px-2.5 py-0.5">
                            Sample review
                        </span>
                    </div>

                    <blockquote class="font-serif italic text-2xl text-ink leading-relaxed">
                        "{{ $featuredQuote['quote'] }}"
                    </blockquote>

                    <div class="pt-4 border-t border-[#ECEEE6] flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-sage-light border border-[#E3E6DD] flex items-center justify-center shrink-0 text-xs font-medium text-forest" aria-hidden="true">
                            {{ $getInitials($featuredQuote['author']) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-sans text-sm font-medium text-ink">
                                {{ $featuredQuote['author'] }}
                            </p>
                            <p class="text-[13px] text-muted">
                                {{ $featuredQuote['location'] ?? 'Daily Ritual' }} &middot; <span class="text-forest">{{ $featuredQuote['product'] ?? 'Botanical Collection' }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Right: Two Smaller Quotes Divided by Thin Lines -->
            <div class="lg:col-span-6 lg:border-l lg:border-[#ECEEE6] lg:pl-10 divide-y divide-[#ECEEE6]">
                @foreach($secondaryQuotes as $idx => $review)
                    <div class="{{ $idx === 0 ? 'pb-6' : 'pt-6' }} space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <x-storefront.rating :rating="$review['rating']" :show-count="false" size="sm" />
                            <span class="text-xs uppercase tracking-[0.2em] text-muted border border-[#E3E6DD] bg-white px-2.5 py-0.5">
                                Sample review
                            </span>
                        </div>

                        <blockquote class="font-serif italic text-lg text-ink leading-relaxed">
                            "{{ $review['quote'] }}"
                        </blockquote>

                        <div class="pt-2 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-sage-light border border-[#E3E6DD] flex items-center justify-center shrink-0 text-xs font-medium text-forest" aria-hidden="true">
                                {{ $getInitials($review['author']) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-sans text-sm font-medium text-ink">
                                    {{ $review['author'] }}
                                </p>
                                <p class="text-[13px] text-muted">
                                    {{ $review['location'] ?? 'Daily Ritual' }} &middot; <span class="text-forest">{{ $review['product'] ?? 'Botanical Collection' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- 8. BOTANICAL GALLERY ("From the Apothecary", 3-column grid of 6 tiles on desktop, 2 columns on mobile) -->
<section class="py-10 lg:py-16 bg-ivory border-t border-[#ECEEE6]">
    <div class="site-container">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-6 lg:mb-8 gap-4">
            <div>
                <span class="block text-xs uppercase tracking-[0.2em] font-medium text-forest mb-1.5">
                    @@theeverbloom.botanicals
                </span>
                <h2 class="font-serif text-2xl sm:text-3xl text-ink font-normal">
                    From the Apothecary
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

        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($galleryItems as $item)
                <div class="group relative overflow-hidden aspect-square border border-[#E3E6DD]">
                    <x-storefront.image 
                        :src="$item['src']" 
                        :alt="$item['alt']" 
                        aspect-ratio="aspect-square"
                        width="640"
                        height="640"
                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                    />
                    <div class="absolute inset-0 bg-forest/35 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4 pointer-events-none">
                        <span class="text-sm text-ivory font-sans tracking-wide leading-snug">{{ $item['alt'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
