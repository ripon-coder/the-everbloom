@extends('layouts.app')

@section('title', 'Order Details #' . ($id ?? 'EVB-8921') . ' — The Everbloom')

@section('content')
<div class="site-container py-12 md:py-16 space-y-8 max-w-4xl">
    <x-storefront.breadcrumbs :items="[
        ['label' => 'My Account', 'url' => '/account'],
        ['label' => 'Orders', 'url' => '/account/orders'],
        ['label' => 'Order #' . ($id ?? 'EVB-8921'), 'url' => '']
    ]" />

    <div class="border-b border-beige pb-6 flex items-baseline justify-between">
        <div>
            <span class="text-xs uppercase tracking-widest text-forest font-semibold block mb-1">Demonstration Record</span>
            <h1 class="font-serif text-3xl text-ink font-normal">Order #{{ $id ?? 'EVB-8921' }}</h1>
            <p class="text-xs text-muted mt-1">Confirmed October 28, 2026 · Shipped via Standard Ground</p>
        </div>
        <span class="px-3 py-1 bg-sage-light text-forest text-xs uppercase tracking-widest font-medium border border-sage/40">Delivered</span>
    </div>

    <div class="bg-white border border-beige p-6 space-y-6">
        <h2 class="font-serif text-lg text-ink font-medium border-b border-beige pb-3">Package Contents</h2>
        
        <div class="divide-y divide-beige/60">
            <div class="py-4 flex justify-between items-center text-sm">
                <div>
                    <p class="font-medium text-ink">Botanical Radiance Serum (30ml)</p>
                    <p class="text-xs text-muted">Quantity: 1</p>
                </div>
                <span class="font-semibold text-ink">$64.00</span>
            </div>
            <div class="py-4 flex justify-between items-center text-sm">
                <div>
                    <p class="font-medium text-ink">Camellia Restorative Moisturizer (50ml)</p>
                    <p class="text-xs text-muted">Quantity: 1</p>
                </div>
                <span class="font-semibold text-ink">$58.00</span>
            </div>
        </div>

        <div class="border-t border-beige pt-4 space-y-2 text-xs text-muted">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span class="text-ink font-medium">$122.00</span>
            </div>
            <div class="flex justify-between">
                <span>Shipping</span>
                <span class="text-forest font-medium">Complimentary</span>
            </div>
            <div class="flex justify-between font-serif text-base text-ink font-semibold pt-2 border-t border-beige/60">
                <span>Total</span>
                <span>$122.00</span>
            </div>
        </div>
    </div>
</div>
@endsection
