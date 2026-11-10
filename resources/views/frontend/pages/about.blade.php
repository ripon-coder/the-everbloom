@extends('layouts.app')

@section('title', 'Our Botanical Story — The Everbloom')
@section('meta_description', 'Discover the apothecary origins, ethical harvest principles, and botanical extraction methods behind The Everbloom.')

@section('content')
<div class="site-container py-12 md:py-16 space-y-16">
    <x-storefront.breadcrumbs :items="[['label' => 'Our Story', 'url' => '']]" />

    <div class="max-w-3xl mx-auto text-center space-y-4">
        <span class="text-xs uppercase tracking-[0.25em] font-medium text-forest block">The Apothecary Heritage</span>
        <h1 class="font-serif text-3xl sm:text-5xl text-ink font-normal leading-tight">Naturally Beautiful.<br><span class="italic">Always Blooming.</span></h1>
        <p class="text-base sm:text-lg text-muted leading-relaxed pt-2">
            The Everbloom was founded with a single botanical aspiration: to reconnect modern skincare with the pure restorative vitality of uncompromised, cold-extracted plants.
        </p>
    </div>

    <div class="aspect-[16/9] max-w-4xl mx-auto bg-sage-light/40 border border-beige">
        <x-storefront.image src="editorial/about-apothecary.jpg" alt="Botanical Still Life and Herbs" aspect-ratio="aspect-[16/9]" />
    </div>

    <div class="max-w-2xl mx-auto space-y-8 text-sm sm:text-base text-ink/90 leading-relaxed font-sans">
        <h2 class="font-serif text-2xl sm:text-3xl text-ink font-normal">Reverence for Plant Vitality</h2>
        <p>
            Every bloom, berry, and seed harbors a delicate lipid and nutrient matrix designed by nature to protect itself from extreme elements. We harvest at peak physiological vitality, employing slow cold-pressing and low-temperature hydrosol distillation.
        </p>
        <p>
            We formulate without synthetic fragrances, parabens, phthalates, artificial colorants, or aggressive detergents. What remains is a pure, skin-harmonious botanical concentrate that comforts your skin barrier with every drop.
        </p>
    </div>
</div>
@endsection
