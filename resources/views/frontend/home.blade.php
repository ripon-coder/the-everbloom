@extends('layouts.app')

@section('title', config('demo.site.name') . ' — ' . config('demo.site.tagline'))
@section('meta_description', 'Conscious botanical skincare formulated with cold-pressed oils and active plant extracts. Naturally beautiful, always blooming.')

@section('content')
<div class="space-y-16 md:space-y-24 pb-16">
    <!-- Hero Section -->
    <section class="relative bg-sage-light/40 border-b border-beige py-16 md:py-24">
        <div class="site-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 space-y-6">
                    <span class="text-xs uppercase tracking-[0.25em] font-medium text-forest block">
                        Pure Botanical Apothecary
                    </span>
                    <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl text-ink font-normal tracking-tight leading-[1.15]">
                        Naturally Beautiful.<br>
                        <span class="italic font-normal">Always Blooming.</span>
                    </h1>
                    <p class="text-base sm:text-lg text-muted max-w-xl leading-relaxed">
                        Pure, cold-extracted botanicals and skin-identical lipids formulated to nurture moisture barrier harmony and lasting luminosity throughout every season.
                    </p>
                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <x-storefront.button href="/shop" variant="primary" size="lg">
                            Explore All Formulations
                        </x-storefront.button>
                        <x-storefront.button href="/about" variant="secondary" size="lg">
                            Our Philosophy
                        </x-storefront.button>
                    </div>
                </div>

                <div class="lg:col-span-5 relative">
                    <!-- Arch Shaped Hero Image -->
                    <div class="relative overflow-hidden rounded-t-[140px] rounded-b-none border border-forest/10 shadow-soft bg-white p-2">
                        <x-storefront.image 
                            src="hero/hero-botanical-arch.jpg" 
                            alt="The Everbloom Botanical Ritual" 
                            aspect-ratio="aspect-[4/5]" 
                            class="rounded-t-[136px]"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Botanical Categories Strip -->
    <section class="site-container">
        <x-storefront.section-heading 
            eyebrow="Targeted Rituals" 
            title="Curated by Skin Need" 
            link-text="View All Categories" 
            link-url="/shop"
        />

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 sm:gap-6">
            @foreach(config('demo.categories') as $cat)
                <a href="/shop/category/{{ $cat['slug'] }}" class="group block text-center">
                    <div class="aspect-square bg-sage-light/50 border border-beige/60 overflow-hidden mb-3">
                        <x-storefront.image 
                            :src="$cat['image']" 
                            :alt="$cat['name']" 
                            aspect-ratio="aspect-square" 
                            class="group-hover:scale-105 transition-transform duration-500"
                        />
                    </div>
                    <h3 class="font-serif text-sm font-medium text-ink group-hover:text-forest transition-colors">
                        {{ $cat['name'] }}
                    </h3>
                    <p class="text-[11px] text-muted uppercase tracking-wider mt-0.5">
                        {{ $cat['item_count'] }} Formulations
                    </p>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Featured Formulations -->
    <section class="site-container">
        <x-storefront.section-heading 
            eyebrow="The Essentials" 
            title="Signature Formulations" 
            description="Our most revered botanical oils, restorative creams, and daily shields."
            link-text="Shop Full Catalogue" 
            link-url="/shop"
        />

        @php
            $featured = array_filter(config('demo.products'), fn($p) => $p['is_featured'] ?? false);
            $featured = array_slice($featured, 0, 4);
        @endphp

        <x-storefront.product-grid :products="$featured" columns="4" />
    </section>

    <!-- Editorial Botanical Band -->
    <section class="bg-forest text-ivory py-16 md:py-24">
        <div class="site-container">
            <div class="max-w-2xl mx-auto text-center space-y-6">
                <span class="text-xs uppercase tracking-[0.25em] text-sage font-medium block">
                    Our Sourcing Creed
                </span>
                <h2 class="font-serif text-3xl sm:text-4xl md:text-5xl text-ivory font-normal tracking-tight">
                    Cold-Pressed. Single-Origin. Ethically Harvested.
                </h2>
                <p class="text-sm sm:text-base text-ivory/80 leading-relaxed">
                    We select organic seeds and wild blooms nurtured by dedicated farmers. Every extract is cold-processed within 24 hours of harvest to preserve delicate antioxidant phytonutrients.
                </p>
                <div class="pt-2">
                    <x-storefront.button href="/about" variant="secondary" class="!border-white !text-ivory hover:!bg-white hover:!text-forest">
                        Learn Our Process
                    </x-storefront.button>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="site-container" id="testimonials">
        <x-storefront.section-heading 
            eyebrow="Client Reviews" 
            title="Real Words from Our Community" 
            centered
        />

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
            @foreach(array_slice(config('demo.testimonials'), 0, 3) as $review)
                <div class="bg-white border border-beige p-6 sm:p-8 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <x-storefront.rating :rating="$review['rating']" :show-count="false" />
                            <span class="text-[10px] uppercase tracking-widest text-muted bg-ivory px-2 py-0.5 border border-beige">Sample review</span>
                        </div>
                        <p class="font-serif text-base text-ink italic leading-relaxed">
                            "{{ $review['quote'] }}"
                        </p>
                    </div>

                    <div class="pt-6 mt-6 border-t border-beige/60">
                        <p class="text-xs font-semibold text-ink font-sans">{{ $review['author'] }}</p>
                        <p class="text-[11px] text-muted">{{ $review['location'] }} · <span class="text-forest">{{ $review['product'] }}</span></p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
