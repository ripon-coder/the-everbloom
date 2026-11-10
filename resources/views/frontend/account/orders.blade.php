@extends('layouts.app')

@section('title', 'Order History — The Everbloom')

@section('content')
<div class="site-container py-12 md:py-16 space-y-8">
    <x-storefront.breadcrumbs :items="[['label' => 'My Account', 'url' => '/account'], ['label' => 'Orders', 'url' => '']]" />

    <div class="border-b border-beige pb-6">
        <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal">Order History</h1>
        <p class="text-xs uppercase tracking-widest text-muted mt-1">Demonstration Client Archive (Demo Data)</p>
    </div>

    <div class="bg-white border border-beige divide-y divide-beige/80">
        <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="text-xs uppercase tracking-widest text-forest font-semibold block">Order #EVB-8921</span>
                <p class="text-xs text-muted">Placed on October 28, 2026 · 2 Formulations</p>
                <p class="font-serif text-base font-medium text-ink">$122.00 · Delivered</p>
            </div>
            <div>
                <x-storefront.button href="/account/orders/EVB-8921" variant="secondary" size="sm">
                    View Details
                </x-storefront.button>
            </div>
        </div>

        <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="text-xs uppercase tracking-widest text-forest font-semibold block">Order #EVB-7402</span>
                <p class="text-xs text-muted">Placed on September 15, 2026 · 1 Formulation</p>
                <p class="font-serif text-base font-medium text-ink">$46.00 · Delivered</p>
            </div>
            <div>
                <x-storefront.button href="/account/orders/EVB-7402" variant="secondary" size="sm">
                    View Details
                </x-storefront.button>
            </div>
        </div>
    </div>
</div>
@endsection
