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
        <div class="p-4 bg-amber-50 border border-amber-200 text-xs text-amber-900 rounded-sm">
            <strong>Notice to Website Purchaser:</strong> [Replace with your legal text] where indicated prior to commercial operation.
        </div>

        <section class="space-y-3">
            <h2 class="font-serif text-xl text-ink">1. Acceptance of Terms</h2>
            <p>[Replace with your legal text: State conditions regarding website access, intellectual property, product ordering, and compliance with local retail regulations.]</p>
        </section>

        <section class="space-y-3">
            <h2 class="font-serif text-xl text-ink">2. Commercial Purchases & Pricing</h2>
            <p>[Replace with your legal text: Outline currency denomination (USD), sales tax collection, cancellation policies, and inventory availability adjustments.]</p>
        </section>

        <section class="space-y-3" id="accessibility">
            <h2 class="font-serif text-xl text-ink">3. Accessibility Statement</h2>
            <p>The Everbloom is dedicated to digital accessibility and adherence to WCAG 2.1 AA standards. We continuously enhance screen-reader compatibility and keyboard navigability across all storefront pages.</p>
        </section>

        <section class="space-y-3">
            <h2 class="font-serif text-xl text-ink">4. Limitation of Liability</h2>
            <p>[Replace with your legal text: Standard limitation of liability clauses, topical cosmetics usage disclaimers, and patch-testing recommendations.]</p>
        </section>
    </div>
</div>
@endsection
