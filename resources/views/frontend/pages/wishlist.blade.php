@extends('layouts.app')

@section('title', 'Your Saved Formulations — The Everbloom')
@section('meta_description', 'View your saved wishlist of botanical skincare products.')

@section('content')
<div class="site-container py-12 md:py-16 space-y-8" x-data>
    <x-storefront.breadcrumbs :items="[['label' => 'Wishlist', 'url' => '']]" />

    <div class="border-b border-beige pb-6 flex items-baseline justify-between">
        <div>
            <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal">Saved Botanical Rituals</h1>
            <p class="text-sm text-muted mt-1">Formulations you have saved to your private curation list.</p>
        </div>
        <span class="text-xs uppercase tracking-widest text-muted" x-text="$store.wishlist.count + ' Saved'"></span>
    </div>

    <!-- Empty State -->
    <div x-show="$store.wishlist.count === 0" class="py-16 text-center border border-dashed border-beige bg-white p-8">
        <svg class="w-12 h-12 mx-auto text-muted mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
        <p class="font-serif text-lg text-ink">No saved formulations yet</p>
        <p class="text-xs text-muted max-w-xs mx-auto mt-1 mb-6">Click the heart icon on any product to curate your personal collection.</p>
        <x-storefront.button href="/shop" size="sm">
            Browse Formulations
        </x-storefront.button>
    </div>

    <!-- Wishlist Items Grid -->
    <div x-show="$store.wishlist.count > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <template x-for="item in $store.wishlist.items" :key="item.id || item">
            <div class="bg-white border border-beige p-4 flex flex-col justify-between">
                <div>
                    <div class="aspect-[4/5] bg-sage-light/40 overflow-hidden mb-3 relative">
                        <template x-if="item.image">
                            <img :src="'/images/' + item.image" :alt="item.name" class="w-full h-full object-cover">
                        </template>
                    </div>
                    <span class="text-[10px] uppercase tracking-widest text-muted block" x-text="item.category"></span>
                    <a :href="'/shop/product/' + item.slug" class="font-serif text-base text-ink hover:text-forest transition-colors line-clamp-1" x-text="item.name"></a>
                    <p class="text-xs font-semibold text-ink mt-1" x-text="'$' + parseFloat(item.price || 0).toFixed(2)"></p>
                </div>
                <div class="mt-4 pt-3 border-t border-beige flex items-center justify-between">
                    <button type="button" @click="$store.wishlist.toggle(item)" class="text-xs text-rose-800 hover:underline">Remove</button>
                    <a :href="'/shop/product/' + item.slug" class="text-xs uppercase tracking-widest text-forest font-medium hover:underline">View →</a>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection
