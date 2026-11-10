@extends('layouts.app')

@section('title', 'Shopping Bag — The Everbloom')
@section('meta_description', 'Review your selected botanical skincare formulations in your shopping bag.')

@section('content')
<div class="site-container py-8 md:py-12 space-y-8" x-data>
    <x-storefront.breadcrumbs :items="[
        ['label' => 'Shopping Bag', 'url' => '']
    ]" />

    <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal">Your Botanical Bag</h1>

    <!-- Empty State -->
    <template x-if="$store.cart.items.length === 0">
        <div class="py-16 text-center border border-dashed border-beige bg-white p-8">
            <svg class="w-12 h-12 mx-auto text-muted mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <p class="font-serif text-xl text-ink font-normal">Your shopping bag is currently empty</p>
            <p class="text-xs text-muted uppercase tracking-widest mt-1 mb-6">Discover our cold-pressed botanical essentials</p>
            <x-storefront.button href="/shop" size="sm">
                Explore The Collection
            </x-storefront.button>
        </div>
    </template>

    <!-- Cart Items Table / Summary -->
    <template x-if="$store.cart.items.length > 0">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
            <!-- Items List -->
            <div class="lg:col-span-8 divide-y divide-beige/80 border-t border-b border-beige/80">
                <template x-for="item in $store.cart.items" :key="item.key">
                    <div class="py-6 flex gap-6 items-center">
                        <div class="w-20 h-24 bg-sage-light/40 border border-beige shrink-0 overflow-hidden">
                            <template x-if="item.image">
                                <img :src="'/images/' + item.image" :alt="item.name" class="w-full h-full object-cover">
                            </template>
                        </div>
                        <div class="flex-1">
                            <span class="text-[10px] uppercase tracking-widest text-muted block" x-text="item.category"></span>
                            <a :href="'/shop/product/' + item.slug" class="font-serif text-base text-ink hover:text-forest" x-text="item.name"></a>
                            <p class="text-xs text-muted" x-show="item.size" x-text="item.size"></p>
                            <p class="text-xs font-semibold text-ink mt-1" x-text="'$' + parseFloat(item.price).toFixed(2)"></p>
                        </div>
                        <div class="inline-flex items-center border border-beige bg-ivory">
                            <button type="button" @click="$store.cart.updateQty(item.key, item.quantity - 1)" class="w-8 h-8 flex items-center justify-center text-ink/70 hover:text-forest">-</button>
                            <span class="w-8 text-center text-xs font-medium" x-text="item.quantity"></span>
                            <button type="button" @click="$store.cart.updateQty(item.key, item.quantity + 1)" class="w-8 h-8 flex items-center justify-center text-ink/70 hover:text-forest">+</button>
                        </div>
                        <div class="text-right w-24">
                            <span class="text-sm font-semibold text-forest block" x-text="'$' + (item.price * item.quantity).toFixed(2)"></span>
                            <button type="button" @click="$store.cart.remove(item.key)" class="text-xs text-muted hover:text-ink underline mt-1">Remove</button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Summary Column -->
            <div class="lg:col-span-4 bg-white p-6 border border-beige space-y-6">
                <h2 class="font-serif text-lg text-ink font-medium border-b border-beige pb-3">Order Summary</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-muted">
                        <span>Bag Subtotal</span>
                        <span class="font-semibold text-ink" x-text="'$' + $store.cart.subtotal.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-muted">
                        <span>Standard Shipping</span>
                        <span class="text-forest font-medium" x-text="$store.cart.subtotal >= 65 ? 'Complimentary' : '$6.00'"></span>
                    </div>
                    <div class="border-t border-beige pt-3 flex justify-between font-serif text-lg font-semibold text-ink">
                        <span>Estimated Total</span>
                        <span x-text="'$' + ($store.cart.subtotal + ($store.cart.subtotal >= 65 ? 0 : 6)).toFixed(2)"></span>
                    </div>
                </div>

                <div class="pt-2 space-y-3">
                    <x-storefront.button href="/checkout" variant="primary" class="w-full">
                        Proceed to Checkout
                    </x-storefront.button>
                    <x-storefront.button href="/shop" variant="secondary" class="w-full">
                        Continue Shopping
                    </x-storefront.button>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
