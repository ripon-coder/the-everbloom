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
                <div class="flex justify-between items-center py-1 border-b border-beige/60">
                    <span class="font-medium text-ink">Standard Ground Courier (3–5 business days)</span>
                    <span class="text-forest font-semibold">$6.00 (Free on orders over $65)</span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="font-medium text-ink">Express Priority Courier (1–2 business days)</span>
                    <span class="text-forest font-semibold">$15.00 (Free on orders over $150)</span>
                </div>
            </div>
        </section>

        <section class="space-y-4" id="returns">
            <h2 class="font-serif text-2xl text-ink">30-Day Gentle Return Guarantee</h2>
            <p>We want your skin to feel truly honored. If an Everbloom formula does not bring contentment to your daily ritual, contact our concierge within 30 days of receipt to arrange a complimentary return or product exchange.</p>
        </section>
    </div>
</div>
@endsection
