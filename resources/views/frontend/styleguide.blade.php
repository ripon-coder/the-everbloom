@extends('layouts.app')

@section('title', 'Design System & Component Styleguide — The Everbloom')
@section('meta_description', 'Living styleguide and design tokens for The Everbloom luxury botanical frontend.')

@section('content')
<div class="site-container py-12 md:py-16 space-y-16">

    <!-- Header Section -->
    <div class="border-b border-beige pb-8">
        <span class="text-xs uppercase tracking-[0.25em] font-medium text-forest block mb-2">
            Design Tokens & Component Library
        </span>
        <h1 class="font-serif text-3xl md:text-5xl text-ink font-normal tracking-tight">
            The Everbloom Design System
        </h1>
        <p class="mt-3 text-muted max-w-2xl leading-relaxed">
            A comprehensive, conversion-focused frontend system crafted with Tailwind CSS v3.4 tokens, Playfair Display and Inter typography, and accessible Alpine.js components.
        </p>
    </div>

    <!-- 1. Color Palette Tokens -->
    <section class="space-y-6">
        <div>
            <span class="text-xs uppercase tracking-[0.2em] font-medium text-muted block mb-1">01 / Palette</span>
            <h2 class="font-serif text-2xl text-ink font-normal">Brand Color Tokens</h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-8 gap-4">
            <!-- Forest -->
            <div class="space-y-2">
                <div class="h-24 bg-forest rounded-sm border border-forest-700/20 shadow-sm flex items-end p-2 text-ivory text-xs font-mono">
                    #304C3D
                </div>
                <div>
                    <p class="text-xs font-medium text-ink">Forest</p>
                    <p class="text-[10px] text-muted">Primary Brand / CTAs</p>
                </div>
            </div>

            <!-- Sage -->
            <div class="space-y-2">
                <div class="h-24 bg-sage rounded-sm border border-sage-dark/20 shadow-sm flex items-end p-2 text-ink text-xs font-mono">
                    #A8B7A0
                </div>
                <div>
                    <p class="text-xs font-medium text-ink">Sage</p>
                    <p class="text-[10px] text-muted">Accent / Botanical</p>
                </div>
            </div>

            <!-- Sage Light -->
            <div class="space-y-2">
                <div class="h-24 bg-sage-light rounded-sm border border-beige shadow-sm flex items-end p-2 text-ink text-xs font-mono">
                    #E8EEE5
                </div>
                <div>
                    <p class="text-xs font-medium text-ink">Sage Light</p>
                    <p class="text-[10px] text-muted">Bands & Placeholders</p>
                </div>
            </div>

            <!-- Ivory -->
            <div class="space-y-2">
                <div class="h-24 bg-ivory rounded-sm border border-beige shadow-sm flex items-end p-2 text-ink text-xs font-mono">
                    #F8F6F0
                </div>
                <div>
                    <p class="text-xs font-medium text-ink">Ivory</p>
                    <p class="text-[10px] text-muted">Canvas / Surface</p>
                </div>
            </div>

            <!-- Beige -->
            <div class="space-y-2">
                <div class="h-24 bg-beige rounded-sm border border-beige-dark/20 shadow-sm flex items-end p-2 text-ink text-xs font-mono">
                    #E8DFD0
                </div>
                <div>
                    <p class="text-xs font-medium text-ink">Beige</p>
                    <p class="text-[10px] text-muted">Borders & Dividing</p>
                </div>
            </div>

            <!-- Ink -->
            <div class="space-y-2">
                <div class="h-24 bg-ink rounded-sm border border-ink/40 shadow-sm flex items-end p-2 text-ivory text-xs font-mono">
                    #292D29
                </div>
                <div>
                    <p class="text-xs font-medium text-ink">Ink</p>
                    <p class="text-[10px] text-muted">Primary Copy</p>
                </div>
            </div>

            <!-- Muted -->
            <div class="space-y-2">
                <div class="h-24 bg-muted rounded-sm border border-muted-dark/20 shadow-sm flex items-end p-2 text-ivory text-xs font-mono">
                    #777D74
                </div>
                <div>
                    <p class="text-xs font-medium text-ink">Muted</p>
                    <p class="text-[10px] text-muted">Secondary Metadata</p>
                </div>
            </div>

            <!-- White -->
            <div class="space-y-2">
                <div class="h-24 bg-white rounded-sm border border-beige shadow-sm flex items-end p-2 text-ink text-xs font-mono">
                    #FFFFFF
                </div>
                <div>
                    <p class="text-xs font-medium text-ink">Pure White</p>
                    <p class="text-[10px] text-muted">Card Highlights</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Typography Scale -->
    <section class="space-y-6 pt-8 border-t border-beige">
        <div>
            <span class="text-xs uppercase tracking-[0.2em] font-medium text-muted block mb-1">02 / Typography</span>
            <h2 class="font-serif text-2xl text-ink font-normal">Playfair Display & Inter Type Hierarchy</h2>
        </div>

        <div class="space-y-6 bg-white p-6 md:p-8 border border-beige">
            <div>
                <span class="text-[10px] font-mono text-muted uppercase">Display Heading — Playfair Display clamp(2.5rem, 5vw, 4.25rem)</span>
                <p class="type-display mt-1">Naturally Beautiful. Always Blooming.</p>
            </div>

            <div class="border-t border-beige/60 pt-4">
                <span class="text-[10px] font-mono text-muted uppercase">Heading 1 — Playfair Display clamp(2rem, 3.5vw, 3rem)</span>
                <p class="type-h1 mt-1">Conscious Botanical Formulations</p>
            </div>

            <div class="border-t border-beige/60 pt-4">
                <span class="text-[10px] font-mono text-muted uppercase">Heading 2 — Playfair Display clamp(1.6rem, 2.5vw, 2.25rem)</span>
                <p class="type-h2 mt-1">Nurtured by Earth, Proven by Pure Science</p>
            </div>

            <div class="border-t border-beige/60 pt-4">
                <span class="text-[10px] font-mono text-muted uppercase">Heading 3 — Playfair Display clamp(1.25rem, 1.8vw, 1.625rem)</span>
                <p class="type-h3 mt-1">Wild Elderberry & Cold-Pressed Botanicals</p>
            </div>

            <div class="border-t border-beige/60 pt-4">
                <span class="text-[10px] font-mono text-muted uppercase">Heading 4 — Playfair Display 1.125rem</span>
                <p class="type-h4 mt-1">Active Botanical Ingredients & Application Notes</p>
            </div>

            <div class="border-t border-beige/60 pt-4">
                <span class="text-[10px] font-mono text-muted uppercase">Body — Inter 0.9375rem / 1.65 line-height</span>
                <p class="type-body mt-1 max-w-2xl">
                    Our apothecary marries age-old European herbalism with precision botanical lipid extraction. Every formulation respects your skin's protective moisture mantle, creating long-term resilience without harsh surfactants.
                </p>
            </div>

            <div class="border-t border-beige/60 pt-4">
                <span class="text-[10px] font-mono text-muted uppercase">Small & Metadata — Inter 0.8125rem</span>
                <p class="type-small mt-1">
                    Standard Ground Botanical Shipping (3–5 business days) · Complimentary on orders above $65.00.
                </p>
            </div>

            <div class="border-t border-beige/60 pt-4">
                <span class="text-[10px] font-mono text-muted uppercase">Uppercase Label — Inter 0.75rem / tracking-[0.2em] font-medium</span>
                <p class="type-label mt-1 text-forest">
                    Limited Seasonal Harvest · Batch No. 048
                </p>
            </div>
        </div>
    </section>

    <!-- 3. Button Styles -->
    <section class="space-y-6 pt-8 border-t border-beige">
        <div>
            <span class="text-xs uppercase tracking-[0.2em] font-medium text-muted block mb-1">03 / Actions</span>
            <h2 class="font-serif text-2xl text-ink font-normal">Button Variants & Sizing</h2>
        </div>

        <div class="bg-white p-6 md:p-8 border border-beige space-y-6">
            <div class="flex flex-wrap items-center gap-4">
                <x-storefront.button variant="primary">
                    Primary Button
                </x-storefront.button>

                <x-storefront.button variant="secondary">
                    Secondary Outline
                </x-storefront.button>

                <x-storefront.button variant="tertiary">
                    Tertiary Link
                </x-storefront.button>

                <x-storefront.button variant="dark">
                    Dark Accent
                </x-storefront.button>

                <x-storefront.button variant="primary" disabled>
                    Disabled State
                </x-storefront.button>
            </div>

            <!-- Sizes -->
            <div class="border-t border-beige/60 pt-4 flex flex-wrap items-center gap-4">
                <x-storefront.button size="sm">
                    Small Button
                </x-storefront.button>
                <x-storefront.button size="md">
                    Standard 44px Touch
                </x-storefront.button>
                <x-storefront.button size="lg">
                    Large Hero CTA
                </x-storefront.button>
            </div>
        </div>
    </section>

    <!-- 4. Interactive Components Showcase -->
    <section class="space-y-6 pt-8 border-t border-beige">
        <div>
            <span class="text-xs uppercase tracking-[0.2em] font-medium text-muted block mb-1">04 / Components</span>
            <h2 class="font-serif text-2xl text-ink font-normal">Core Storefront Components</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Sample Product Card -->
            <div class="bg-white p-5 border border-beige">
                <span class="text-[10px] font-mono text-muted uppercase block mb-3">Product Card Component</span>
                @php
                    $sampleProduct = config('demo.products')[0];
                @endphp
                <x-storefront.product-card :product="$sampleProduct" />
            </div>

            <!-- Rating & Price & Quantity Controls -->
            <div class="bg-white p-5 border border-beige space-y-6">
                <span class="text-[10px] font-mono text-muted uppercase block">Micro Components</span>
                
                <div class="space-y-2">
                    <p class="text-xs font-medium text-ink">Rating Stars:</p>
                    <x-storefront.rating rating="4.9" count="128" />
                </div>

                <div class="space-y-2">
                    <p class="text-xs font-medium text-ink">Price Display:</p>
                    <x-storefront.price-display price="64.00" compare-at-price="78.00" size="lg" />
                </div>

                <div class="space-y-2">
                    <p class="text-xs font-medium text-ink">Quantity Selector:</p>
                    <x-storefront.quantity-selector />
                </div>

                <div class="space-y-2">
                    <p class="text-xs font-medium text-ink">Wishlist Toggle Button:</p>
                    <div class="flex items-center gap-3">
                        <x-storefront.wishlist-button :product="$sampleProduct" size="sm" />
                        <x-storefront.wishlist-button :product="$sampleProduct" size="md" />
                        <x-storefront.wishlist-button :product="$sampleProduct" size="lg" />
                    </div>
                </div>
            </div>

            <!-- Accordion Component -->
            <div class="bg-white p-5 border border-beige space-y-4">
                <span class="text-[10px] font-mono text-muted uppercase block">Collapsible Accordion</span>
                <x-storefront.accordion :items="[
                    ['title' => 'Botanical Sourcing Policy', 'content' => 'All plants are grown responsibly without synthetic chemical pesticides and cold-extracted to protect lipid integrity.'],
                    ['title' => 'Packaging Recyclability', 'content' => 'Amber glass containers are 100% recyclable, printed with organic soy inks.'],
                    ['title' => 'Skin Sensitivity Guarantee', 'content' => 'Formulations are free of synthetic fragrance, artificial colors, and harsh sulfates.']
                ]" />
            </div>
        </div>
    </section>

    <!-- 5. Breadcrumbs and Placeholders -->
    <section class="space-y-6 pt-8 border-t border-beige">
        <div>
            <span class="text-xs uppercase tracking-[0.2em] font-medium text-muted block mb-1">05 / Navigation & Media</span>
            <h2 class="font-serif text-2xl text-ink font-normal">Breadcrumbs & Editorial Image Placeholder</h2>
        </div>

        <div class="bg-white p-6 border border-beige space-y-6">
            <x-storefront.breadcrumbs :items="[
                ['label' => 'Shop All', 'url' => '/shop'],
                ['label' => 'Concentrated Serums', 'url' => '/shop/category/serums'],
                ['label' => 'Botanical Radiance Serum', 'url' => '']
            ]" />

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-4">
                <div>
                    <span class="text-[10px] font-mono text-muted uppercase block mb-2">4:5 Aspect Ratio</span>
                    <x-storefront.image src="products/sample-bottle-01.jpg" alt="Botanical Glass Bottle" aspect-ratio="aspect-[4/5]" />
                </div>
                <div>
                    <span class="text-[10px] font-mono text-muted uppercase block mb-2">1:1 Aspect Ratio</span>
                    <x-storefront.image src="categories/sample-category-01.jpg" alt="Cold Pressed Camellia" aspect-ratio="aspect-square" />
                </div>
                <div>
                    <span class="text-[10px] font-mono text-muted uppercase block mb-2">16:9 Editorial</span>
                    <x-storefront.image src="editorial/harvest-field.jpg" alt="Botanical Harvest Garden" aspect-ratio="aspect-[16/9]" />
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
