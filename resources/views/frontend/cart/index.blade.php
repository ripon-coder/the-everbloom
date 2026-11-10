@extends('layouts.app')

@section('title', 'Shopping Bag — The Everbloom')
@section('meta_description', 'Review your selected botanical skincare formulations in your shopping bag.')
@section('hide_newsletter', true)

@section('content')
@php
    $freeShippingThreshold = config('demo.site.shipping_rates.standard.free_threshold', 65.00);
    $standardShippingCost = config('demo.site.shipping_rates.standard.cost', 6.00);
    $allProducts = config('demo.products', []);

    // 4 Best-selling products for empty state
    $bestSellers = array_values(array_filter($allProducts, fn($p) => ($p['badge'] ?? '') === 'Bestseller' || ($p['is_featured'] ?? false)));
    if (count($bestSellers) < 4) {
        $bestSellers = array_slice($allProducts, 0, 4);
    } else {
        $bestSellers = array_slice($bestSellers, 0, 4);
    }

    // 2-4 Complementary products for "Pairs thoughtfully with"
    $pairingProducts = array_values(array_filter($allProducts, fn($p) => ($p['badge'] ?? '') === 'Daily Essential' || ($p['badge'] ?? '') === 'Signature' || in_array($p['id'], [2, 4, 7, 10])));
    if (count($pairingProducts) < 4) {
        $pairingProducts = array_slice($allProducts, 2, 4);
    } else {
        $pairingProducts = array_slice($pairingProducts, 0, 4);
    }
@endphp

<!-- Data Payloads for Store Products & Discounts Synchronization -->
<script id="everbloom-products-data" type="application/json">
{!! json_encode($allProducts, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script id="everbloom-discounts-data" type="application/json">
{!! json_encode(config('demo.site.demo_discounts', []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

<div class="site-container py-8 md:py-12 space-y-8" x-data>
    <x-storefront.breadcrumbs :items="[
        ['label' => 'Shopping Bag', 'url' => '']
    ]" />

    <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal">Your Botanical Bag</h1>

    <!-- 6. EMPTY STATE: Message, Continue Shopping button, 4 best-selling products -->
    <template x-if="$store.cart.items.length === 0">
        <div class="space-y-12">
            <div class="py-16 text-center border border-dashed border-beige bg-white p-8 max-w-2xl mx-auto space-y-4">
                <div class="w-16 h-16 mx-auto rounded-full bg-sage-light/50 flex items-center justify-center text-forest/70">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <p class="font-serif text-xl sm:text-2xl text-ink font-normal">Your shopping bag is currently empty</p>
                    <p class="text-xs text-muted uppercase tracking-widest">Discover our cold-pressed botanical essentials to begin your ritual</p>
                </div>
                <div class="pt-2">
                    <x-storefront.button href="/shop" size="sm">
                        Continue Shopping
                    </x-storefront.button>
                </div>
            </div>

            <!-- 4 Best-Selling Products in Empty State -->
            <div class="space-y-6">
                <div class="border-b border-beige/80 pb-3 flex items-baseline justify-between">
                    <div>
                        <span class="text-[11px] uppercase tracking-[0.2em] font-medium text-forest block">Apothecary Favorites</span>
                        <h2 class="font-serif text-xl sm:text-2xl text-ink font-normal mt-0.5">Best-Selling Botanical Formulations</h2>
                    </div>
                    <a href="/shop" class="text-xs uppercase tracking-widest text-muted hover:text-forest transition-colors font-medium">
                        Explore Full Apothecary &rarr;
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($bestSellers as $product)
                        <x-storefront.product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </div>
    </template>

    <!-- Cart Items Table / Summary Grid -->
    <template x-if="$store.cart.items.length > 0">
        <div class="space-y-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                
                <!-- Items List (8 Columns) -->
                <div class="lg:col-span-8 divide-y divide-beige/80 border-t border-b border-beige/80">
                    <template x-for="item in $store.cart.items" :key="item.key">
                        <div class="py-6 flex flex-col sm:flex-row gap-5 sm:gap-6 sm:items-center">
                            
                            {{-- 2. IMAGES: Use x-storefront.image (aspect-square, object-cover, fixed 96px size). Link to product page --}}
                            <a :href="'/shop/product/' + item.slug" class="w-24 h-24 shrink-0 block focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest">
                                <x-storefront.image 
                                    ::src="'/images/' + item.image" 
                                    ::alt="item.name" 
                                    aspect-ratio="aspect-square" 
                                    class="w-24 h-24 shrink-0 object-cover border border-beige" 
                                />
                            </a>

                            <!-- 3. ROW DETAILS: Product Name Link, Variant, "$84.10 each" under variant -->
                            <div class="flex-1 min-w-0 space-y-1">
                                <span class="text-[10px] uppercase tracking-widest text-muted block" x-text="item.category"></span>
                                <a 
                                    :href="'/shop/product/' + item.slug" 
                                    class="font-serif text-base text-ink hover:text-forest transition-colors block font-normal truncate" 
                                    x-text="item.name"
                                ></a>
                                <p class="text-xs text-muted" x-show="item.size" x-text="item.size"></p>
                                <p class="text-xs text-muted" x-text="'$' + parseFloat(item.price).toFixed(2) + ' each'"></p>
                            </div>

                            <!-- 3. Quantity Buttons: Proper minus icon, min 44x44px touch targets, clamped 1-10 -->
                            <div class="inline-flex items-center border border-beige bg-ivory shrink-0">
                                <button 
                                    type="button" 
                                    @click="$store.cart.updateQty(item.key, item.qty - 1)" 
                                    class="w-11 h-11 min-w-[44px] min-h-[44px] flex items-center justify-center text-ink/70 hover:text-forest transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-forest cursor-pointer"
                                    :aria-label="'Decrease quantity of ' + item.name + (item.size ? ' (' + item.size + ')' : '')"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="w-10 text-center text-xs font-medium text-ink select-none" x-text="item.qty"></span>
                                <button 
                                    type="button" 
                                    @click="$store.cart.updateQty(item.key, item.qty + 1)" 
                                    class="w-11 h-11 min-w-[44px] min-h-[44px] flex items-center justify-center text-ink/70 hover:text-forest transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-forest cursor-pointer"
                                    :disabled="item.qty >= 10"
                                    :aria-label="'Increase quantity of ' + item.name + (item.size ? ' (' + item.size + ')' : '')"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>

                            <!-- 3. Line total on right & 13px Remove link with product + variant in aria-label -->
                            <div class="text-right sm:w-28 shrink-0 flex sm:flex-col items-center sm:items-end justify-between sm:justify-center">
                                <span class="text-sm font-semibold text-forest block" x-text="'$' + (item.price * item.qty).toFixed(2)"></span>
                                <button 
                                    type="button" 
                                    @click="$store.cart.remove(item.key)" 
                                    class="text-[13px] text-muted hover:text-ink underline transition-colors cursor-pointer sm:mt-2"
                                    :aria-label="'Remove ' + item.name + (item.size ? ' (' + item.size + ')' : '') + ' from shopping bag'"
                                >
                                    Remove
                                </button>
                            </div>

                        </div>
                    </template>
                </div>

                <!-- 4. ORDER SUMMARY (Sticky on lg, Subtotal, Discount, Shipping, Total, Free Shipping Progress, Demo Codes) -->
                <div class="lg:col-span-4 bg-white p-6 border border-beige space-y-6 lg:sticky lg:top-24 h-fit">
                    <h2 class="font-serif text-lg text-ink font-medium border-b border-beige pb-3">Order Summary</h2>

                    <!-- Free-shipping progress message based on config threshold -->
                    <div class="bg-sage-light/50 p-4 border border-beige/60 space-y-2">
                        <template x-if="$store.cart.subtotal >= {{ $freeShippingThreshold }}">
                            <p class="text-xs text-forest font-medium flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-forest shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>You've unlocked free shipping</span>
                            </p>
                        </template>
                        <template x-if="$store.cart.subtotal < {{ $freeShippingThreshold }}">
                            <div class="space-y-1.5">
                                <p class="text-xs text-muted">
                                    Add <span class="font-semibold text-ink" x-text="'$' + ({{ $freeShippingThreshold }} - $store.cart.subtotal).toFixed(2)"></span> more for free shipping
                                </p>
                                <div class="w-full bg-beige/80 h-1.5 rounded-full overflow-hidden">
                                    <div 
                                        class="bg-forest h-full transition-all duration-300 rounded-full" 
                                        :style="'width: ' + Math.min(100, ($store.cart.subtotal / {{ $freeShippingThreshold }}) * 100) + '%'"
                                    ></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Line Totals breakdown -->
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-muted">
                            <span>Subtotal</span>
                            <span class="font-semibold text-ink" x-text="'$' + $store.cart.subtotal.toFixed(2)"></span>
                        </div>

                        <!-- Discount row: shown ONLY when a code is applied -->
                        <template x-if="$store.cart.discountCode && $store.cart.discountAmount > 0">
                            <div class="flex justify-between text-forest">
                                <span class="flex items-center gap-1.5">
                                    <span>Discount</span>
                                    <span class="text-xs uppercase tracking-wider font-mono font-medium" x-text="'(' + $store.cart.discountCode + ')'"></span>
                                </span>
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold" x-text="'-$' + $store.cart.discountAmount.toFixed(2)"></span>
                                    <button 
                                        type="button" 
                                        @click="$store.cart.removeDiscount()" 
                                        class="text-muted hover:text-ink text-xs transition-colors cursor-pointer"
                                        title="Remove discount"
                                        aria-label="Remove applied discount"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div class="flex justify-between text-muted">
                            <span>Shipping</span>
                            <span class="text-forest font-medium" x-text="$store.cart.subtotal >= {{ $freeShippingThreshold }} || ($store.cart.discount && $store.cart.discount.type === 'shipping') ? 'Complimentary' : '${{ number_format($standardShippingCost, 2) }}'"></span>
                        </div>

                        <div class="border-t border-beige pt-3 flex justify-between font-serif text-lg font-semibold text-ink">
                            <span>Estimated Total</span>
                            <span x-text="'$' + $store.cart.total.toFixed(2)"></span>
                        </div>
                    </div>

                    <p class="text-xs text-muted">Taxes calculated at checkout.</p>

                    <!-- Discount Code Field with Apply button & aria-live messages -->
                    <div x-data="{ codeInput: '' }" class="pt-2 border-t border-beige/60 space-y-2">
                        <label for="cart-discount-input" class="text-xs font-medium text-ink uppercase tracking-wider block">
                            Promotional Code
                        </label>
                        <form @submit.prevent="if(codeInput.trim()) { $store.cart.applyDiscount(codeInput.trim()); codeInput = ''; }" class="flex gap-2">
                            <input 
                                type="text" 
                                id="cart-discount-input"
                                x-model="codeInput" 
                                placeholder="Try WELCOME10" 
                                class="flex-1 bg-ivory border border-beige px-3 py-2 text-xs uppercase placeholder:normal-case placeholder:text-muted focus:outline-none focus:ring-1 focus:ring-forest focus:border-forest"
                                aria-label="Promotional discount code"
                            />
                            <button 
                                type="submit" 
                                class="btn-secondary !py-2 !px-4 !text-xs !tracking-wider uppercase whitespace-nowrap cursor-pointer"
                            >
                                Apply
                            </button>
                        </form>

                        <!-- Inline success and error messages (aria-live) -->
                        <div aria-live="polite" class="min-h-[1.25rem]">
                            <p x-show="$store.cart.discountSuccess" x-cloak class="text-xs text-forest flex items-center gap-1.5 font-medium">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span x-text="$store.cart.discountSuccess"></span>
                            </p>
                            <p x-show="$store.cart.discountError" x-cloak class="text-xs text-red-600 flex items-center gap-1.5 font-medium">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span x-text="$store.cart.discountError"></span>
                            </p>
                        </div>
                    </div>

                    <!-- Checkout & Shopping Action Buttons -->
                    <div class="space-y-3 pt-2">
                        <x-storefront.button href="/checkout" variant="primary" class="w-full">
                            Proceed to Checkout
                        </x-storefront.button>
                        <x-storefront.button href="/shop" variant="secondary" class="w-full">
                            Continue Shopping
                        </x-storefront.button>
                    </div>

                    <!-- 5. TRUST ROW under Proceed to Checkout (DEMO PLACEHOLDER from config/demo/site.php) -->
                    <div class="pt-4 border-t border-beige/60 space-y-3 text-xs text-muted">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-forest shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>{{ config('demo.site.trust_badges.secure_checkout', '256-Bit Encrypted Botanical Checkout') }}</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-forest shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>{{ config('demo.site.trust_badges.returns_policy', '30-Day Complimentary Botanical Returns') }}</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-forest shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>{{ config('demo.site.trust_badges.support_contact', 'Concierge Support: ' . config('demo.site.contact.email')) }}</span>
                        </div>
                    </div>

                </div>

            </div>

            <!-- 8. OPTIONAL: "Pairs thoughtfully with" row of 2-4 products below the cart -->
            <div class="pt-12 border-t border-beige/80 space-y-6">
                <div class="space-y-1">
                    <span class="text-[11px] uppercase tracking-[0.2em] font-medium text-forest block">Ritual Complements</span>
                    <h2 class="font-serif text-xl sm:text-2xl text-ink font-normal">Pairs Thoughtfully With</h2>
                    <p class="text-xs text-muted">Pure botanical formulations selected to enhance and balance your daily skincare regimen.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($pairingProducts as $pairedProduct)
                        <x-storefront.product-card :product="$pairedProduct" />
                    @endforeach
                </div>
            </div>

        </div>
    </template>
</div>
@endsection
