@extends('layouts.app')

@section('title', 'The Everbloom | Botanical Skincare')
@section('meta_description', 'Thoughtful botanical skincare crafted for everyday rituals. Discover serums, moisturizers, cleansers, and mineral sunscreens.')

@section('content')
@php
    $allProducts = collect(config('demo.products', []));
    $flashSaleProducts = $flashSaleProducts ?? $allProducts->filter(
        fn ($p) => !empty($p['compare_at_price']) && $p['compare_at_price'] > $p['price']
    )->take(4)->values()->all();
    $flashSaleEndsAt = $flashSaleEndsAt ?? null;
    $featuredProducts = $featuredProducts ?? $allProducts->where('is_featured', true)->take(4)->values()->all();
    $newArrivalsProducts = $newArrivalsProducts ?? $allProducts->sortByDesc('id')->take(4)->values()->all();
    $bestsellerProducts = $bestsellerProducts ?? $allProducts->where('best_seller', true)
        ->whereNotIn('id', collect($featuredProducts)->pluck('id')->all())
        ->take(4)
        ->values()
        ->all();
    if (empty($bestsellerProducts)) {
        $bestsellerProducts = $allProducts->whereNotIn('id', collect($featuredProducts)->pluck('id')->all())->take(4)->values()->all();
    }
    $categories = $categories ?? config('demo.categories', []);
    $testimonials = $testimonials ?? array_slice(config('demo.testimonials', []), 0, 3);
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
                        DISCOVER YOUR BEAUTY ESSENTIALS
                    </span>
                </div>

                <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl text-ink font-normal leading-[1.08] tracking-tight text-balance">
                    Your Beauty, Your <span class="italic font-normal text-forest">Ritual.</span>
                </h1>

                <p class="text-base text-muted leading-relaxed max-w-md font-normal">
                    Explore skincare, beauty and self-care essentials from your favorite brands. Discover products that make your everyday beauty routine special.
                </p>

                <div class="inline-flex items-center gap-2.5 px-3.5 py-2 bg-forest/[0.06] border border-forest/20 rounded-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-forest shrink-0"></span>
                    <span class="text-xs uppercase tracking-[0.18em] font-medium text-forest">
                        SEASONAL BEAUTY SALE — UP TO 40% OFF
                    </span>
                </div>

                <div class="pt-1 flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-center gap-3 sm:gap-4">
                    <x-storefront.button href="/shop" variant="primary" size="lg" class="w-full sm:w-auto min-h-[48px]">
                        SHOP NOW
                    </x-storefront.button>
                    <x-storefront.button href="/shop" variant="secondary" size="lg" class="w-full sm:w-auto min-h-[48px]">
                        EXPLORE BRANDS
                    </x-storefront.button>
                </div>

                <!-- Neutral Editorial Credential List (Vertical on mobile, clean wrapping row on sm+ with no stray dividers) -->
                <ul class="pt-3 lg:pt-4 border-t border-[#ECEEE6] flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-2 sm:gap-x-5 sm:gap-y-2 text-sm sm:text-[13px] text-muted">
                    <li class="inline-flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-forest shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Curated Multi-Brand Edit</span>
                    </li>
                    <li class="inline-flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-forest shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Skincare &amp; Beauty Favorites</span>
                    </li>
                    <li class="inline-flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-forest shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Everyday Self-Care Rituals</span>
                    </li>
                </ul>
            </div>

            <!-- Right Arch Visual Composition (7 cols on lg, below text on mobile) -->
            <div class="lg:col-span-7 relative flex justify-center lg:justify-end">
                <div class="relative w-[92%] max-w-[480px] mx-auto lg:w-full lg:mx-0 lg:max-w-lg xl:max-w-xl">
                    <!-- Main Arch Hero Image (preserves aspect-[4/5] on mobile & desktop) -->
                    <div class="relative z-10 w-full aspect-[4/5] overflow-hidden rounded-t-[999px] hero-arch-mask">
                        <x-storefront.image 
                            src="hero/hero-multi-brand.jpg" 
                            alt="Curated multi-brand skincare and beauty essentials at The Everbloom" 
                            aspect-ratio=""
                            width="960"
                            height="1200"
                            loading="eager"
                            class="!absolute inset-0 w-full h-full object-cover !bg-transparent"
                        />
                    </div>

                    <!-- Caption Quote Panel: left-rule caption on mobile (no box/bg), overlapping bottom-left edge on lg+ -->
                    {{-- // DEMO COPY --}}
                    <div class="mt-5 w-full border-l-2 border-[#A8B7A0] pl-4 py-0.5 lg:mt-0 lg:bg-white lg:border lg:border-[#E3E6DD] lg:p-5 lg:absolute lg:-left-6 lg:bottom-10 lg:max-w-[260px] z-20">
                        <p class="font-serif italic text-[15px] text-ink/80 lg:text-forest leading-snug">
                            "Curated skincare and beauty from your favorite brands."
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

<!-- 4. FLASH SALE (Discounted Multi-Brand Products) -->
<section class="py-10 lg:py-16 bg-beige/25 border-y border-[#ECEEE6]">
    <div class="site-container">
        <div class="mb-8 md:mb-12 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div>
                <span class="block text-xs uppercase tracking-[0.2em] font-medium text-forest mb-2">
                    LIMITED-TIME BEAUTY DEALS
                </span>
                <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl text-ink font-normal tracking-tight">
                    Beauty Deals You Don't Want to Miss.
                </h2>
                <p class="mt-3 text-sm md:text-base text-muted max-w-xl leading-relaxed">
                    Discover special offers on your favorite skincare and beauty essentials.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-4 lg:gap-6 shrink-0">
                @if(!empty($flashSaleEndsAt))
                    <div
                        x-data="{
                            endsAt: new Date(@js($flashSaleEndsAt)).getTime(),
                            remaining: 0,
                            days: '00',
                            hours: '00',
                            minutes: '00',
                            seconds: '00',
                            timer: null,
                            tick() {
                                const diff = this.endsAt - Date.now();
                                if (diff <= 0) {
                                    this.remaining = 0;
                                    if (this.timer) clearInterval(this.timer);
                                    return;
                                }
                                this.remaining = diff;
                                const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                                const h = Math.floor((diff / (1000 * 60 * 60)) % 24);
                                const m = Math.floor((diff / (1000 * 60)) % 60);
                                const s = Math.floor((diff / 1000) % 60);
                                this.days = String(d).padStart(2, '0');
                                this.hours = String(h).padStart(2, '0');
                                this.minutes = String(m).padStart(2, '0');
                                this.seconds = String(s).padStart(2, '0');
                            },
                            init() {
                                this.tick();
                                if (this.remaining > 0) {
                                    this.timer = setInterval(() => this.tick(), 1000);
                                }
                            }
                        }"
                        x-show="remaining > 0"
                        x-cloak
                        aria-label="Flash sale countdown"
                        class="inline-flex items-center justify-center gap-3 px-4 py-2.5 bg-ivory border border-[#E3E6DD]"
                    >
                        <span class="text-[11px] uppercase tracking-[0.18em] font-medium text-muted">
                            Ends In
                        </span>
                        <div class="flex items-center gap-2 font-sans text-sm font-medium text-ink tabular-nums lining-nums">
                            <template x-if="Number(days) > 0">
                                <span class="inline-flex items-baseline gap-0.5">
                                    <span x-text="days" class="inline-block w-[1.45em] text-center tabular-nums"></span><span class="text-[11px] text-muted uppercase">d</span>
                                </span>
                            </template>
                            <span class="inline-flex items-baseline gap-0.5">
                                <span x-text="hours" class="inline-block w-[1.45em] text-center tabular-nums"></span><span class="text-[11px] text-muted uppercase">h</span>
                            </span>
                            <span class="text-muted/60">:</span>
                            <span class="inline-flex items-baseline gap-0.5">
                                <span x-text="minutes" class="inline-block w-[1.45em] text-center tabular-nums"></span><span class="text-[11px] text-muted uppercase">m</span>
                            </span>
                            <span class="text-muted/60">:</span>
                            <span class="inline-flex items-baseline gap-0.5">
                                <span x-text="seconds" class="inline-block w-[1.45em] text-center tabular-nums"></span><span class="text-[11px] text-muted uppercase">s</span>
                            </span>
                        </div>
                    </div>
                @endif

                <a href="{{ url('/shop') }}" class="inline-flex items-center gap-1.5 text-xs uppercase tracking-[0.2em] font-medium text-forest hover:text-ink transition-colors border-b border-forest pb-0.5 group self-start sm:self-auto">
                    <span>VIEW ALL OFFERS</span>
                    <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <x-storefront.product-grid :products="$flashSaleProducts" columns="4" />
    </div>
</section>

<!-- 5. WHY SHOP WITH THE EVERBLOOM -->
<section class="py-10 lg:py-16 bg-ivory overflow-hidden">
    <div class="site-container">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
            
            <!-- Left Visual Column -->
            <div class="lg:col-span-6 relative">
                <div class="relative z-10 overflow-hidden aspect-[4/3] lg:aspect-[4/5] lg:max-h-[560px] w-full">
                    <x-storefront.image 
                        src="hero/hero-main.jpg" 
                        alt="Curated skincare and beauty essentials at The Everbloom" 
                        aspect-ratio="aspect-[4/3] lg:aspect-[4/5]"
                        width="800"
                        height="1000"
                        class="w-full h-full object-cover"
                    />
                </div>
            </div>

            <!-- Right Editorial Narrative Column -->
            <div class="lg:col-span-6 space-y-5">
                <span class="block text-xs uppercase tracking-[0.2em] font-medium text-forest">
                    THE EVERBLOOM EXPERIENCE
                </span>

                <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl text-ink font-normal leading-[1.15]">
                    Beauty Shopping <span class="italic text-forest">Made Better.</span>
                </h2>

                <p class="text-base text-muted leading-relaxed">
                    Discover your favorite beauty brands, explore carefully selected products and enjoy a convenient shopping experience.
                </p>

                <!-- Four Benefits Grid -->
                <div class="pt-2 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5 border-t border-[#ECEEE6]">
                    <div class="pt-3">
                        <h3 class="font-serif text-lg text-ink font-normal">Authentic Products.</h3>
                        <p class="text-sm text-muted mt-1 leading-relaxed">
                            Carefully selected beauty and skincare essentials from trusted brands.
                        </p>
                    </div>
                    <div class="pt-3">
                        <h3 class="font-serif text-lg text-ink font-normal">Multiple Beauty Brands.</h3>
                        <p class="text-sm text-muted mt-1 leading-relaxed">
                            Explore everyday skincare, body care, and self-care favorites in one place.
                        </p>
                    </div>
                    <div class="pt-1">
                        <h3 class="font-serif text-lg text-ink font-normal">Secure Shopping Experience.</h3>
                        <p class="text-sm text-muted mt-1 leading-relaxed">
                            Clear product information and a smooth, reliable checkout flow.
                        </p>
                    </div>
                    <div class="pt-1">
                        <h3 class="font-serif text-lg text-ink font-normal">Customer Support.</h3>
                        <p class="text-sm text-muted mt-1 leading-relaxed">
                            Dedicated assistance whenever you have questions about your order.
                        </p>
                    </div>
                </div>

                <div class="pt-2">
                    <x-storefront.button href="/shop" variant="secondary">
                        Explore Collection
                    </x-storefront.button>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 6. BEST SELLERS (4 distinct best_seller products) -->
<section class="py-10 lg:py-16 bg-sage-light/30 border-t border-[#ECEEE6]">
    <div class="site-container">
        <x-storefront.section-heading 
            eyebrow="BEST SELLERS" 
            title="Customer Favorites." 
            description="Explore the beauty and skincare products customers love most."
            link-text="VIEW ALL BEST SELLERS"
            link-url="/shop?sort=best_selling"
        />

        <x-storefront.product-grid :products="$bestsellerProducts" columns="4" />
    </div>
</section>

<!-- 7. NEW ARRIVALS (4 recently added products) -->
<section class="py-10 lg:py-16 bg-beige/25 border-t border-[#ECEEE6]">
    <div class="site-container">
        <x-storefront.section-heading 
            eyebrow="JUST ARRIVED" 
            title="Fresh Finds for Your Beauty Routine." 
            description="Explore the latest additions to our skincare and beauty collection."
            link-text="VIEW ALL NEW ARRIVALS"
            link-url="/shop?sort=newest"
        />

        <x-storefront.product-grid :products="$newArrivalsProducts" columns="4" />
    </div>
</section>

<!-- 8. TESTIMONIALS (1 large featured quote left + 2 smaller quotes right divided by thin lines, initials circles) -->
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
                        @if(!empty($featuredQuote['verified']))
                            <span class="text-xs uppercase tracking-[0.2em] text-muted border border-[#E3E6DD] bg-white px-2.5 py-0.5">
                                Verified Purchase
                            </span>
                        @endif
                    </div>

                    <blockquote class="font-serif italic text-2xl text-ink leading-relaxed">
                        "{{ $featuredQuote['quote'] }}"
                    </blockquote>

                    <div class="pt-4 border-t border-[#ECEEE6] flex items-center gap-3">
                        @if(!empty($featuredQuote['avatar']))
                            <img
                                src="{{ $featuredQuote['avatar'] }}"
                                alt="{{ $featuredQuote['author'] }}"
                                class="w-9 h-9 rounded-full object-cover border border-[#E3E6DD] shrink-0"
                                loading="lazy"
                            />
                        @else
                            <div class="w-9 h-9 rounded-full bg-sage-light border border-[#E3E6DD] flex items-center justify-center shrink-0 text-xs font-medium text-forest" aria-hidden="true">
                                {{ $getInitials($featuredQuote['author']) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="font-sans text-sm font-medium text-ink">
                                {{ $featuredQuote['author'] }}
                            </p>
                            <p class="text-[13px] text-muted">
                                {{ $featuredQuote['location'] ?? 'Daily Ritual' }}
                                @if(!empty($featuredQuote['product']))
                                    &middot;
                                    @if(!empty($featuredQuote['product_slug']))
                                        <a href="{{ url('/shop/product/' . $featuredQuote['product_slug']) }}" class="text-forest hover:underline">
                                            {{ $featuredQuote['product'] }}
                                        </a>
                                    @else
                                        <span class="text-forest">{{ $featuredQuote['product'] }}</span>
                                    @endif
                                @endif
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
                            @if(!empty($review['verified']))
                                <span class="text-xs uppercase tracking-[0.2em] text-muted border border-[#E3E6DD] bg-white px-2.5 py-0.5">
                                    Verified Purchase
                                </span>
                            @endif
                        </div>

                        <blockquote class="font-serif italic text-lg text-ink leading-relaxed">
                            "{{ $review['quote'] }}"
                        </blockquote>

                        <div class="pt-2 flex items-center gap-3">
                            @if(!empty($review['avatar']))
                                <img
                                    src="{{ $review['avatar'] }}"
                                    alt="{{ $review['author'] }}"
                                    class="w-9 h-9 rounded-full object-cover border border-[#E3E6DD] shrink-0"
                                    loading="lazy"
                                />
                            @else
                                <div class="w-9 h-9 rounded-full bg-sage-light border border-[#E3E6DD] flex items-center justify-center shrink-0 text-xs font-medium text-forest" aria-hidden="true">
                                    {{ $getInitials($review['author']) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-sans text-sm font-medium text-ink">
                                    {{ $review['author'] }}
                                </p>
                                <p class="text-[13px] text-muted">
                                    {{ $review['location'] ?? 'Daily Ritual' }}
                                    @if(!empty($review['product']))
                                        &middot;
                                        @if(!empty($review['product_slug']))
                                            <a href="{{ url('/shop/product/' . $review['product_slug']) }}" class="text-forest hover:underline">
                                                {{ $review['product'] }}
                                            </a>
                                        @else
                                            <span class="text-forest">{{ $review['product'] }}</span>
                                        @endif
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- 8. SHOP BY BRAND (3-column grid of 6 tiles on desktop, 2 columns on mobile) -->
<section class="py-10 lg:py-16 bg-ivory border-t border-[#ECEEE6]">
    <div class="site-container">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-6 lg:mb-8 gap-4">
            <div>
                <span class="block text-xs uppercase tracking-[0.2em] font-medium text-forest mb-1.5">
                    EXPLORE YOUR FAVORITE BRANDS
                </span>
                <h2 class="font-serif text-2xl sm:text-3xl text-ink font-normal">
                    Shop by Brand.
                </h2>
                <p class="mt-2 text-sm md:text-base text-muted max-w-xl leading-relaxed">
                    Discover skincare and beauty essentials from brands you know and love.
                </p>
            </div>
            <a href="{{ url('/shop') }}" class="text-xs uppercase tracking-[0.2em] font-medium text-forest hover:text-ink border-b border-forest pb-0.5 self-start sm:self-auto transition-colors shrink-0">
                View All Brands &rarr;
            </a>
        </div>

        @php
            $brands = $brands ?? [];
        @endphp

        @if(count($brands) > 0)
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($brands as $brand)
                    <a href="{{ $brand['url'] ?? url('/shop?brand=' . urlencode($brand['name'])) }}" class="group relative overflow-hidden aspect-square border border-[#E3E6DD] block bg-sage-light/30">
                        <x-storefront.image 
                            :src="$brand['image'] ?? 'products/placeholder.jpg'" 
                            :alt="$brand['name']" 
                            aspect-ratio="aspect-square"
                            width="640"
                            height="640"
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                        />
                        <div class="absolute inset-0 pointer-events-none" style="background: linear-gradient(to top, rgba(41,45,41,0.72) 0%, rgba(41,45,41,0.18) 48%, transparent 100%);"></div>
                        <div class="absolute inset-x-0 bottom-0 p-4 sm:p-5 flex items-end justify-between gap-2 pointer-events-none">
                            <div class="min-w-0">
                                <span class="block font-serif text-lg sm:text-xl text-ivory font-normal tracking-tight truncate">
                                    {{ $brand['name'] }}
                                </span>
                                @if(!empty($brand['count']))
                                    <span class="block text-[11px] uppercase tracking-[0.18em] text-ivory/85 mt-0.5">
                                        {{ $brand['count'] }} {{ \Illuminate\Support\Str::plural('Product', $brand['count']) }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-ivory/90 text-sm transition-transform duration-300 group-hover:translate-x-1 shrink-0" aria-hidden="true">&rarr;</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
