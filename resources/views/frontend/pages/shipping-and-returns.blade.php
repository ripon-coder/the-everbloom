@extends('layouts.app')

@section('title', 'Shipping & Returns — The Everbloom')
@section('meta_description', 'Information regarding delivery rates, dispatch timelines, and our 30-day gentle return policy.')

@section('content')
<div class="site-container py-12 md:py-16 space-y-12 max-w-4xl">
    <x-storefront.breadcrumbs :items="[['label' => 'Shipping & Returns', 'url' => '']]" />

    <div class="border-b border-beige pb-6">
        <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal">Shipping, Delivery & Returns</h1>
        <p class="text-sm text-muted mt-2">Our gentle care commitments from our dispensary to your doorstep.</p>
    </div>

    <div class="space-y-10 text-sm text-ink/80 leading-relaxed font-sans">
        <section class="space-y-4">
            <h2 class="font-serif text-2xl text-ink">Botanical Fulfillment & Dispatch</h2>
            <p>Every order is carefully inspected, packed in recyclable glass protection, and cushioned with biodegradable organic wood wool. Orders placed by 1:00 PM EST ship same day, Monday through Friday.</p>
            <div class="bg-white border border-beige p-5 space-y-3">
                @php
                    $stdRate = (float) config('demo.site.shipping.standard_rate', 6.00);
                    $freeThresh = (float) config('demo.site.shipping.free_shipping_threshold', 75.00);
                    $expRate = (float) config('demo.site.shipping.express_rate', 15.00);
                    $expThresh = (float) config('demo.site.shipping_rates.express.free_threshold', 150.00);
                    $retDays = (int) config('demo.site.returns.window_days', 30);
                @endphp
                <div class="flex justify-between items-center py-1 border-b border-beige/60">
                    <span class="font-medium text-ink">Standard Ground Courier ({{ config('demo.site.shipping.standard_days', '3–5 business days') }})</span>
                    <span class="text-forest font-semibold">{{ $currencySymbol }}{{ number_format($stdRate, 2) }} (Free on orders over {{ $currencySymbol }}{{ number_format($freeThresh, 0) }})</span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="font-medium text-ink">Express Priority Courier ({{ config('demo.site.shipping.express_days', '1–2 business days') }})</span>
                    <span class="text-forest font-semibold">{{ $currencySymbol }}{{ number_format($expRate, 2) }} (Free on orders over {{ $currencySymbol }}{{ number_format($expThresh, 0) }})</span>
                </div>
            </div>
        </section>

        <section class="space-y-4" id="returns">
            <h2 class="font-serif text-2xl text-ink">{{ $retDays }}-Day Gentle Return Guarantee</h2>
            <p>We want your skin to feel truly honored. If an Everbloom formula does not bring contentment to your daily ritual, contact our concierge within {{ $retDays }} days of receipt to arrange a complimentary return or product exchange.</p>
        </section>
    </div>
</div>
@endsection
