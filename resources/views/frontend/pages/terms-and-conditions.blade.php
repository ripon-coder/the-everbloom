@extends('layouts.app')

@section('title', 'Terms and Conditions — The Everbloom')
@section('meta_description', 'Terms and conditions governing use of The Everbloom website and apothecary purchases.')

@section('content')
<div class="site-container py-12 md:py-16 space-y-10 max-w-4xl">
    <x-storefront.breadcrumbs :items="[['label' => 'Terms & Conditions', 'url' => '']]" />

    <div class="border-b border-beige pb-6">
        <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal">Terms and Conditions of Service</h1>
        <p class="text-xs uppercase tracking-widest text-muted mt-2">Last Updated: November 10, 2026</p>
    </div>

    <div class="prose max-w-none text-sm text-ink/80 space-y-6 leading-relaxed font-sans">
        <section class="space-y-3">
            <h2 class="font-serif text-xl text-ink">1. Acceptance of Terms</h2>
            <p>By accessing and placing orders through The Everbloom storefront, you agree to abide by these terms of service, all applicable commercial regulations, and our published shipping, returns, and privacy policies. All brand imagery, formulation copy, and site design elements remain the intellectual property of The Everbloom.</p>
        </section>

        <section class="space-y-3">
            <h2 class="font-serif text-xl text-ink">2. Commercial Purchases & Pricing</h2>
            <p>All product prices, promotional offers, and shipping charges are displayed in the store's active currency prior to order confirmation. Product availability is subject to current apothecary inventory, and we reserve the right to update product pricing or discontinue formulations without prior notice.</p>
        </section>

        <section class="space-y-3" id="accessibility">
            <h2 class="font-serif text-xl text-ink">3. Accessibility Statement</h2>
            <p>The Everbloom is dedicated to digital accessibility and adherence to WCAG 2.1 AA standards. We continuously enhance screen-reader compatibility and keyboard navigability across all storefront pages.</p>
        </section>

        <section class="space-y-3">
            <h2 class="font-serif text-xl text-ink">4. Limitation of Liability</h2>
            <p>Our botanical skincare products are formulated for external cosmetic use only. We encourage reviewing the full ingredient list for known sensitivities and performing a patch test before initial use. To the fullest extent permitted by law, The Everbloom shall not be liable for indirect or consequential damages arising from product misuse.</p>
        </section>
    </div>
</div>
@endsection
