@extends('layouts.app')

@section('title', 'My Account — The Everbloom')

@section('content')
<div class="site-container py-12 md:py-16 space-y-10">
    <x-storefront.breadcrumbs :items="[['label' => 'My Account', 'url' => '']]" />

    <div class="border-b border-beige pb-6 flex items-center justify-between">
        <div>
            <span class="text-xs uppercase tracking-[0.2em] font-medium text-forest block mb-1">Apothecary Member</span>
            <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal">Welcome, Eleanor</h1>
            <p class="text-xs text-muted mt-1">eleanor.vance@example.com · Client since October 2026</p>
        </div>
        <a href="/account/login" class="text-xs uppercase tracking-widest text-muted hover:text-forest underline">Sign Out</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 border border-beige space-y-3">
            <h2 class="font-serif text-lg text-ink font-medium">Order History</h2>
            <p class="text-xs text-muted leading-relaxed">View all active dispatches, tracking records, and archived purchases.</p>
            <x-storefront.button href="/account/orders" variant="secondary" size="sm" class="w-full">
                View Past Orders (Demo)
            </x-storefront.button>
        </div>

        <div class="bg-white p-6 border border-beige space-y-3">
            <h2 class="font-serif text-lg text-ink font-medium">Saved Formulations</h2>
            <p class="text-xs text-muted leading-relaxed">Quickly view or transfer items from your wishlist to your bag.</p>
            <x-storefront.button href="/wishlist" variant="secondary" size="sm" class="w-full">
                Open Wishlist
            </x-storefront.button>
        </div>

        <div class="bg-white p-6 border border-beige space-y-3">
            <h2 class="font-serif text-lg text-ink font-medium">Default Delivery Address</h2>
            <p class="text-xs text-muted leading-relaxed">740 Botanical Way, Suite 400<br>Portland, OR 97201</p>
            <span class="text-[10px] uppercase tracking-widest text-muted block pt-2">Primary Residential</span>
        </div>
    </div>
</div>
@endsection
