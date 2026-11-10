@extends('layouts.checkout')

@section('title', 'Demo Checkout — ' . config('demo.site.name'))
@section('hide_newsletter', true)

@section('content')
@php
    $countries = config('demo.site.countries', [
        'US' => 'United States',
        'CA' => 'Canada',
        'GB' => 'United Kingdom',
        'AU' => 'Australia',
        'DE' => 'Germany',
        'FR' => 'France',
    ]);

    $shippingRates = config('demo.site.shipping_rates', [
        'standard' => [
            'id' => 'standard',
            'name' => 'Standard Ground Botanical Shipping',
            'time' => '3–5 business days',
            'cost' => 6.00,
            'free_threshold' => 65.00,
        ],
        'express' => [
            'id' => 'express',
            'name' => 'Express Priority Courier',
            'time' => '1–2 business days',
            'cost' => 15.00,
            'free_threshold' => 150.00,
        ],
    ]);

    $site = config('demo.site');
@endphp

<!-- Data Payloads for Store Products & Discounts Synchronization -->
<script id="everbloom-products-data" type="application/json">
{!! json_encode(config('demo.products', []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script id="everbloom-discounts-data" type="application/json">
{!! json_encode(config('demo.site.demo_discounts', []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script>
    window.__everbloomProducts = {!! json_encode(config('demo.products', []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};
    window.__everbloomDiscounts = {!! json_encode(config('demo.site.demo_discounts', []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};
</script>

<!-- Width referenced directly from cart/index.blade.php (.site-container py-8 md:py-12) -->
<div class="site-container py-8 md:py-12" x-data="checkoutComponent()">
    
    <!-- Demo Notice Banner (Single demo notice at top) -->
    <div class="mb-8 p-4 bg-sage-light/70 border border-forest/20 text-xs sm:text-sm text-forest font-medium flex items-center gap-3">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>
            <strong>Demonstration Mode:</strong> This is a front-end checkout flow simulation. No monetary transactions or real orders are processed.
        </span>
    </div>

    <!-- EMPTY STATE: When bag has 0 items and no order completed -->
    <div x-show="cartIsEmpty && !orderPlaced" x-cloak class="py-16 px-6 bg-white border border-beige text-center max-w-xl mx-auto space-y-5">
        <div class="w-16 h-16 rounded-full bg-sage-light/60 flex items-center justify-center mx-auto text-forest/70">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
        </div>
        <div class="space-y-2">
            <h1 class="font-serif text-2xl sm:text-3xl text-ink font-normal lining-nums">
                Your shopping bag is empty
            </h1>
            <p class="text-sm text-muted max-w-md mx-auto">
                There are no botanical formulations in your shopping bag. Explore our collection to begin your skincare ritual.
            </p>
        </div>
        <div class="pt-2">
            <x-storefront.button href="/shop" variant="primary">
                Continue Shopping
            </x-storefront.button>
        </div>
    </div>

    <!-- ORDER SUCCESS FEEDBACK: Shows ONLY the required demo message -->
    <div x-show="orderPlaced" x-cloak class="py-16 px-6 bg-white border border-beige text-center max-w-xl mx-auto space-y-5">
        <div class="w-14 h-14 rounded-full bg-forest text-ivory flex items-center justify-center mx-auto shadow-sm">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <div class="space-y-2">
            <h1 class="font-serif text-2xl sm:text-3xl text-ink font-normal lining-nums">
                Demo order. No payment was processed and no order was placed.
            </h1>
            <p class="text-sm text-muted max-w-md mx-auto leading-relaxed">
                Thank you for reviewing the botanical checkout flow. Your selections remain safely stored in your local session.
            </p>
        </div>
        <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
            <x-storefront.button href="/shop" variant="primary">
                Continue Shopping
            </x-storefront.button>
            <x-storefront.button href="/cart" variant="secondary">
                Return to bag
            </x-storefront.button>
        </div>
    </div>

    <!-- CHECKOUT MAIN GRID (When items present and not completed) -->
    <div x-show="!cartIsEmpty && !orderPlaced" class="space-y-6">
        
        <!-- MOBILE COLLAPSIBLE ORDER SUMMARY BAR (lg:hidden) -->
        <div class="lg:hidden border border-[#A8B7A0] bg-white">
            <button 
                type="button" 
                @click="showMobileSummary = !showMobileSummary" 
                class="w-full flex items-center justify-between p-4 text-left font-medium text-ink focus:outline-none min-h-[48px]"
                :aria-expanded="showMobileSummary ? 'true' : 'false'"
                aria-controls="mobile-order-summary-panel"
            >
                <div class="flex items-center gap-2 text-xs uppercase tracking-wider text-forest font-semibold">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span x-text="showMobileSummary ? 'Hide order summary' : 'Show order summary'">Show order summary</span>
                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': showMobileSummary }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <span class="font-serif font-semibold text-base text-ink lining-nums" x-text="'$' + calculatedTotal.toFixed(2)"></span>
            </button>

            <!-- Collapsible Summary Content on Mobile: Plain flow with no max-height or scrollbar -->
            <div id="mobile-order-summary-panel" x-show="showMobileSummary" x-collapse x-cloak class="px-4 pb-5 pt-3 border-t border-[#A8B7A0]/60 bg-ivory/40 space-y-4">
                
                <!-- 2. Mobile Items List: plain flow with no max-height or scrollbar -->
                <div class="divide-y divide-beige/80">
                    <template x-for="item in cartLines" :key="item.key || (item.productId + '-' + (item.variantId || ''))">
                        <div class="py-3.5 flex items-center gap-4">
                            <!-- Thumbnail: relative, 64x64, shrink-0 with 20px badge at -top-2 -right-2 -->
                            <div class="relative shrink-0 w-16 h-16 bg-white border border-[#A8B7A0]">
                                <x-storefront.image 
                                    ::src="'/images/' + (item.image || '')" 
                                    ::alt="item.name || 'Botanical formulation'" 
                                    aspect-ratio="aspect-square" 
                                    class="w-full h-full object-cover" 
                                />
                                <span 
                                    class="absolute -top-2 -right-2 w-5 h-5 bg-forest text-ivory text-[11px] font-medium rounded-full flex items-center justify-center shadow-sm pointer-events-none z-10 lining-nums" 
                                    x-text="item.qty || item.quantity"
                                ></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-serif text-sm font-medium text-ink truncate leading-snug lining-nums" x-text="item.name"></h3>
                                <p class="text-xs text-muted mt-0.5 truncate lining-nums" x-show="item.size || (item.variant && item.variant.size)" x-text="item.size || (item.variant ? item.variant.size : '')"></p>
                            </div>
                            <div class="shrink-0 text-right pl-3">
                                <span class="text-sm font-medium text-ink font-serif lining-nums" x-text="'$' + (item.lineTotal !== undefined ? item.lineTotal : (item.price * item.qty)).toFixed(2)"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- One small Edit bag link under rows -->
                <div class="pt-1 text-right">
                    <a href="/cart" class="text-xs uppercase tracking-widest text-muted hover:text-forest transition-colors font-medium underline">
                        Edit bag
                    </a>
                </div>

                <!-- Mobile Promo Code Input & Apply Button -->
                <div class="pt-3 border-t border-[#A8B7A0]/60 space-y-2">
                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            x-model="discountInput" 
                            placeholder="Discount code" 
                            class="flex-1 px-3.5 py-3 text-[16px] bg-white border border-[#A8B7A0] rounded-sm min-h-[48px] placeholder-[#5C6656] focus:outline-none focus:ring-2 focus:ring-forest focus:border-forest"
                            @keydown.enter.prevent="applyDiscountCode()"
                        >
                        <x-storefront.button 
                            type="button" 
                            @click="applyDiscountCode()" 
                            variant="secondary" 
                            size="md" 
                            class="shrink-0 !min-h-[48px] !px-5"
                        >
                            Apply
                        </x-storefront.button>
                    </div>
                    <p x-show="$store.cart.discountError" x-cloak class="text-xs text-rose-700 font-medium" x-text="$store.cart.discountError"></p>
                    <template x-if="$store.cart.discountCode">
                        <div class="flex items-center justify-between text-xs bg-sage-light/60 px-3 py-2 border border-forest/20 text-forest">
                            <span class="font-medium lining-nums" x-text="'Applied: ' + $store.cart.discountCode + ($store.cart.discount ? ' (' + $store.cart.discount.label + ')' : '')"></span>
                            <button type="button" @click="$store.cart.removeDiscount()" class="text-xs uppercase tracking-wider hover:underline text-ink font-semibold ml-2">
                                Remove
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Mobile Subtotal & Totals with lining-nums -->
                <div class="pt-3 border-t border-[#A8B7A0]/60 space-y-2 text-sm">
                    <div class="flex justify-between text-muted">
                        <span>Subtotal</span>
                        <span class="font-medium text-ink font-serif lining-nums" x-text="'$' + ($store.cart.subtotal || 0).toFixed(2)"></span>
                    </div>

                    <div x-show="$store.cart.discountAmount > 0" x-cloak class="flex justify-between text-forest font-medium">
                        <span>Discount (<span x-text="$store.cart.discountCode"></span>)</span>
                        <span class="font-serif lining-nums" x-text="'-$' + ($store.cart.discountAmount || 0).toFixed(2)"></span>
                    </div>

                    <div class="flex justify-between text-muted">
                        <span x-text="'Shipping (' + (selectedShippingRate ? selectedShippingRate.name : 'Standard') + ')'"></span>
                        <span :class="calculatedShippingCost === 0 ? 'text-forest font-medium' : 'text-ink font-medium font-serif lining-nums'" 
                              x-text="calculatedShippingCost === 0 ? 'Complimentary' : '$' + calculatedShippingCost.toFixed(2)"></span>
                    </div>

                    <div class="border-t border-[#A8B7A0]/60 pt-3 flex justify-between font-serif text-base font-semibold text-ink">
                        <span>Total Due</span>
                        <span class="lining-nums" x-text="'$' + calculatedTotal.toFixed(2)"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. 2-COLUMN LAYOUT ON DESKTOP: Form 7/12, Summary 5/12, gap-12 -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            <!-- LEFT COLUMN: ONE WHITE PANEL WITH DIVIDERS (lg:col-span-7) -->
            <div class="lg:col-span-7 bg-white border border-[#A8B7A0] divide-y divide-beige/80 shadow-xs">
                
                <!-- SECTION 1: CONTACT (consistent pt-8 pb-6) -->
                <section class="px-6 sm:px-8 pt-8 pb-6 space-y-4" aria-labelledby="section-contact-heading">
                    <div class="flex items-center justify-between">
                        <h2 id="section-contact-heading" class="font-serif text-lg sm:text-xl text-ink font-medium lining-nums">
                            1. Contact Information
                        </h2>
                        <span class="text-xs text-muted">* Required</span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-xs uppercase tracking-widest text-muted mb-1.5 font-medium">
                                Email Address *
                            </label>
                            <!-- 3. INPUT: White bg, 1px sage border, rounded-sm, 16px text, placeholder 4.5:1, min-h-[48px] -->
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                autocomplete="email" 
                                x-model="form.email" 
                                @input="clearError('email')"
                                :aria-invalid="errors.email ? 'true' : 'false'"
                                :aria-describedby="errors.email ? 'email-error' : null"
                                placeholder="you@domain.com" 
                                class="w-full px-3.5 py-3 text-[16px] bg-white border rounded-sm min-h-[48px] placeholder-[#5C6656] focus:outline-none focus:ring-2 focus:ring-forest focus:border-forest"
                                :class="errors.email ? 'border-rose-600 focus:border-rose-600 focus:ring-rose-600' : 'border-[#A8B7A0] focus:border-forest focus:ring-forest'"
                            >
                            <p id="email-error" x-show="errors.email" x-cloak class="mt-1.5 text-xs text-rose-700 font-medium" x-text="errors.email" role="alert"></p>
                        </div>

                        <div>
                            <label for="phone" class="block text-xs uppercase tracking-widest text-muted mb-1.5 font-medium">
                                Phone Number *
                            </label>
                            <input 
                                id="phone" 
                                name="phone" 
                                type="tel" 
                                autocomplete="tel" 
                                x-model="form.phone" 
                                @input="clearError('phone')"
                                :aria-invalid="errors.phone ? 'true' : 'false'"
                                :aria-describedby="errors.phone ? 'phone-error' : null"
                                placeholder="(555) 000-0000" 
                                class="w-full px-3.5 py-3 text-[16px] bg-white border rounded-sm min-h-[48px] placeholder-[#5C6656] focus:outline-none focus:ring-2 focus:ring-forest focus:border-forest"
                                :class="errors.phone ? 'border-rose-600 focus:border-rose-600 focus:ring-rose-600' : 'border-[#A8B7A0] focus:border-forest focus:ring-forest'"
                            >
                            <p id="phone-error" x-show="errors.phone" x-cloak class="mt-1.5 text-xs text-rose-700 font-medium" x-text="errors.phone" role="alert"></p>
                        </div>
                    </div>
                </section>

                <!-- SECTION 2: SHIPPING ADDRESS (consistent pt-8 pb-6) -->
                <section class="px-6 sm:px-8 pt-8 pb-6 space-y-4" aria-labelledby="section-shipping-heading">
                    <h2 id="section-shipping-heading" class="font-serif text-lg sm:text-xl text-ink font-medium lining-nums">
                        2. Shipping Address
                    </h2>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-xs uppercase tracking-widest text-muted mb-1.5 font-medium">
                                    First Name *
                                </label>
                                <input 
                                    id="first_name" 
                                    name="first_name" 
                                    type="text" 
                                    autocomplete="given-name" 
                                    x-model="form.firstName" 
                                    @input="clearError('firstName')"
                                    :aria-invalid="errors.firstName ? 'true' : 'false'"
                                    :aria-describedby="errors.firstName ? 'first_name-error' : null"
                                    class="w-full px-3.5 py-3 text-[16px] bg-white border rounded-sm min-h-[48px] placeholder-[#5C6656] focus:outline-none focus:ring-2 focus:ring-forest focus:border-forest"
                                    :class="errors.firstName ? 'border-rose-600 focus:border-rose-600 focus:ring-rose-600' : 'border-[#A8B7A0] focus:border-forest focus:ring-forest'"
                                >
                                <p id="first_name-error" x-show="errors.firstName" x-cloak class="mt-1.5 text-xs text-rose-700 font-medium" x-text="errors.firstName" role="alert"></p>
                            </div>

                            <div>
                                <label for="last_name" class="block text-xs uppercase tracking-widest text-muted mb-1.5 font-medium">
                                    Last Name *
                                </label>
                                <input 
                                    id="last_name" 
                                    name="last_name" 
                                    type="text" 
                                    autocomplete="family-name" 
                                    x-model="form.lastName" 
                                    @input="clearError('lastName')"
                                    :aria-invalid="errors.lastName ? 'true' : 'false'"
                                    :aria-describedby="errors.lastName ? 'last_name-error' : null"
                                    class="w-full px-3.5 py-3 text-[16px] bg-white border rounded-sm min-h-[48px] placeholder-[#5C6656] focus:outline-none focus:ring-2 focus:ring-forest focus:border-forest"
                                    :class="errors.lastName ? 'border-rose-600 focus:border-rose-600 focus:ring-rose-600' : 'border-[#A8B7A0] focus:border-forest focus:ring-forest'"
                                >
                                <p id="last_name-error" x-show="errors.lastName" x-cloak class="mt-1.5 text-xs text-rose-700 font-medium" x-text="errors.lastName" role="alert"></p>
                            </div>
                        </div>

                        <!-- 3. Country Select: styled exactly like text inputs with custom chevron -->
                        <div>
                            <label for="country" class="block text-xs uppercase tracking-widest text-muted mb-1.5 font-medium">
                                Country / Region *
                            </label>
                            <div class="relative">
                                <select 
                                    id="country" 
                                    name="country" 
                                    autocomplete="country" 
                                    x-model="form.country" 
                                    @change="clearError('country')"
                                    :aria-invalid="errors.country ? 'true' : 'false'"
                                    :aria-describedby="errors.country ? 'country-error' : null"
                                    class="w-full px-3.5 py-3 pr-10 text-[16px] bg-white border rounded-sm min-h-[48px] appearance-none focus:outline-none focus:ring-2 focus:ring-forest focus:border-forest cursor-pointer"
                                    :class="errors.country ? 'border-rose-600 focus:border-rose-600 focus:ring-rose-600' : 'border-[#A8B7A0] focus:border-forest focus:ring-forest'"
                                >
                                    @foreach($countries as $code => $countryName)
                                        <option value="{{ $code }}">{{ $countryName }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-forest">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            <p id="country-error" x-show="errors.country" x-cloak class="mt-1.5 text-xs text-rose-700 font-medium" x-text="errors.country" role="alert"></p>
                        </div>

                        <!-- Street Address with required placeholder -->
                        <div>
                            <label for="address" class="block text-xs uppercase tracking-widest text-muted mb-1.5 font-medium">
                                Street Address *
                            </label>
                            <input 
                                id="address" 
                                name="address" 
                                type="text" 
                                autocomplete="address-line1" 
                                x-model="form.address" 
                                @input="clearError('address')"
                                :aria-invalid="errors.address ? 'true' : 'false'"
                                :aria-describedby="errors.address ? 'address-error' : null"
                                placeholder="Street address" 
                                class="w-full px-3.5 py-3 text-[16px] bg-white border rounded-sm min-h-[48px] placeholder-[#5C6656] focus:outline-none focus:ring-2 focus:ring-forest focus:border-forest"
                                :class="errors.address ? 'border-rose-600 focus:border-rose-600 focus:ring-rose-600' : 'border-[#A8B7A0] focus:border-forest focus:ring-forest'"
                            >
                            <p id="address-error" x-show="errors.address" x-cloak class="mt-1.5 text-xs text-rose-700 font-medium" x-text="errors.address" role="alert"></p>
                        </div>

                        <!-- Apartment / Suite -->
                        <div>
                            <label for="apartment" class="block text-xs uppercase tracking-widest text-muted mb-1.5 font-medium">
                                Apartment, suite, unit, etc. (optional)
                            </label>
                            <input 
                                id="apartment" 
                                name="apartment" 
                                type="text" 
                                autocomplete="address-line2" 
                                x-model="form.apartment" 
                                placeholder="Apartment, suite, unit, etc. (optional)" 
                                class="w-full px-3.5 py-3 text-[16px] bg-white border border-[#A8B7A0] rounded-sm min-h-[48px] placeholder-[#5C6656] focus:outline-none focus:ring-2 focus:ring-forest focus:border-forest"
                            >
                        </div>

                        <!-- City, Adaptive State/Province, Adaptive ZIP/Postal Code -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="city" class="block text-xs uppercase tracking-widest text-muted mb-1.5 font-medium">
                                    City *
                                </label>
                                <input 
                                    id="city" 
                                    name="city" 
                                    type="text" 
                                    autocomplete="address-level2" 
                                    x-model="form.city" 
                                    @input="clearError('city')"
                                    :aria-invalid="errors.city ? 'true' : 'false'"
                                    :aria-describedby="errors.city ? 'city-error' : null"
                                    class="w-full px-3.5 py-3 text-[16px] bg-white border rounded-sm min-h-[48px] placeholder-[#5C6656] focus:outline-none focus:ring-2 focus:ring-forest focus:border-forest"
                                    :class="errors.city ? 'border-rose-600 focus:border-rose-600 focus:ring-rose-600' : 'border-[#A8B7A0] focus:border-forest focus:ring-forest'"
                                >
                                <p id="city-error" x-show="errors.city" x-cloak class="mt-1.5 text-xs text-rose-700 font-medium" x-text="errors.city" role="alert"></p>
                            </div>

                            <div>
                                <label for="state" class="block text-xs uppercase tracking-widest text-muted mb-1.5 font-medium">
                                    <span x-text="stateLabel">State / Province</span> *
                                </label>
                                <input 
                                    id="state" 
                                    name="state" 
                                    type="text" 
                                    autocomplete="address-level1" 
                                    x-model="form.state" 
                                    @input="clearError('state')"
                                    :aria-invalid="errors.state ? 'true' : 'false'"
                                    :aria-describedby="errors.state ? 'state-error' : null"
                                    class="w-full px-3.5 py-3 text-[16px] bg-white border rounded-sm min-h-[48px] placeholder-[#5C6656] focus:outline-none focus:ring-2 focus:ring-forest focus:border-forest"
                                    :class="errors.state ? 'border-rose-600 focus:border-rose-600 focus:ring-rose-600' : 'border-[#A8B7A0] focus:border-forest focus:ring-forest'"
                                >
                                <p id="state-error" x-show="errors.state" x-cloak class="mt-1.5 text-xs text-rose-700 font-medium" x-text="errors.state" role="alert"></p>
                            </div>

                            <div>
                                <label for="postal_code" class="block text-xs uppercase tracking-widest text-muted mb-1.5 font-medium">
                                    <span x-text="postalCodeLabel">Postal Code</span> *
                                </label>
                                <input 
                                    id="postal_code" 
                                    name="postal_code" 
                                    type="text" 
                                    autocomplete="postal-code" 
                                    x-model="form.zip" 
                                    @input="clearError('zip')"
                                    :aria-invalid="errors.zip ? 'true' : 'false'"
                                    :aria-describedby="errors.zip ? 'postal_code-error' : null"
                                    class="w-full px-3.5 py-3 text-[16px] bg-white border rounded-sm min-h-[48px] placeholder-[#5C6656] focus:outline-none focus:ring-2 focus:ring-forest focus:border-forest"
                                    :class="errors.zip ? 'border-rose-600 focus:border-rose-600 focus:ring-rose-600' : 'border-[#A8B7A0] focus:border-forest focus:ring-forest'"
                                >
                                <p id="postal_code-error" x-show="errors.zip" x-cloak class="mt-1.5 text-xs text-rose-700 font-medium" x-text="errors.zip" role="alert"></p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- SECTION 3: DELIVERY / SHIPPING METHOD RADIOS (consistent pt-8 pb-6) -->
                <section class="px-6 sm:px-8 pt-8 pb-6 space-y-4" aria-labelledby="section-delivery-heading">
                    <div>
                        <h2 id="section-delivery-heading" class="font-serif text-lg sm:text-xl text-ink font-medium lining-nums">
                            3. Delivery Method
                        </h2>
                        <p class="text-xs text-muted mt-0.5">Selection automatically updates your shipping fee and total.</p>
                    </div>

                    <!-- 4. RADIO OPTIONS: Unselected = 1px sage border, white bg. Selected = 1px forest border, bg-[#E8EEE5], no thick ring -->
                    <div class="space-y-3" role="radiogroup" aria-label="Shipping Method">
                        @foreach($shippingRates as $rateKey => $rate)
                        <label 
                            class="relative flex items-center justify-between p-4 rounded-sm cursor-pointer transition-all min-h-[48px] border"
                            :class="shippingMethod === '{{ $rateKey }}' ? 'border-forest bg-[#E8EEE5]' : 'border-[#A8B7A0] bg-white hover:border-forest/60'"
                        >
                            <div class="flex items-center gap-3">
                                <input 
                                    type="radio" 
                                    name="shipping_method" 
                                    value="{{ $rateKey }}" 
                                    x-model="shippingMethod" 
                                    class="w-4 h-4 text-forest accent-[#304C3D] focus:ring-2 focus:ring-forest focus:ring-offset-2 border-beige cursor-pointer"
                                >
                                <div>
                                    <span class="block text-sm font-medium text-ink">{{ $rate['name'] }}</span>
                                    <span class="block text-xs text-muted mt-0.5">{{ $rate['time'] }}</span>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-ink shrink-0 ml-3 font-serif lining-nums" x-text="getShippingRatePrice('{{ $rateKey }}')">
                                ${{ number_format($rate['cost'], 2) }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </section>

                <!-- SECTION 4: PAYMENT METHOD (consistent pt-8 pb-6) -->
                <section class="px-6 sm:px-8 pt-8 pb-6 space-y-5" aria-labelledby="section-payment-heading">
                    <div>
                        <h2 id="section-payment-heading" class="font-serif text-lg sm:text-xl text-ink font-medium lining-nums">
                            4. Payment Method
                        </h2>
                    </div>

                    <!-- 1 & 4. PAYMENT OPTIONS: Card (demo) & Digital wallet (demo) -->
                    <div class="space-y-3" role="radiogroup" aria-label="Demo Payment Method">
                        <label 
                            class="relative flex items-center justify-between p-4 rounded-sm cursor-pointer transition-all min-h-[48px] border"
                            :class="paymentMethod === 'card_demo' ? 'border-forest bg-[#E8EEE5]' : 'border-[#A8B7A0] bg-white hover:border-forest/60'"
                        >
                            <div class="flex items-center gap-3">
                                <input 
                                    type="radio" 
                                    name="payment_method" 
                                    value="card_demo" 
                                    x-model="paymentMethod" 
                                    class="w-4 h-4 text-forest accent-[#304C3D] focus:ring-2 focus:ring-forest focus:ring-offset-2 border-beige cursor-pointer"
                                >
                                <div>
                                    <span class="block text-sm font-medium text-ink">Credit / Debit Card (demo)</span>
                                    <span class="block text-xs text-muted mt-0.5">No card details needed in this demo.</span>
                                </div>
                            </div>
                        </label>

                        <label 
                            class="relative flex items-center justify-between p-4 rounded-sm cursor-pointer transition-all min-h-[48px] border"
                            :class="paymentMethod === 'digital_wallet_demo' ? 'border-forest bg-[#E8EEE5]' : 'border-[#A8B7A0] bg-white hover:border-forest/60'"
                        >
                            <div class="flex items-center gap-3">
                                <input 
                                    type="radio" 
                                    name="payment_method" 
                                    value="digital_wallet_demo" 
                                    x-model="paymentMethod" 
                                    class="w-4 h-4 text-forest accent-[#304C3D] focus:ring-2 focus:ring-forest focus:ring-offset-2 border-beige cursor-pointer"
                                >
                                <div>
                                    <span class="block text-sm font-medium text-ink">Digital wallet (demo)</span>
                                    <span class="block text-xs text-muted mt-0.5">No card details needed in this demo.</span>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- 1. Small 13px Helper Line -->
                    <p class="text-[13px] text-muted text-center font-sans">
                        This is a demo. No payment is taken and no order is placed.
                    </p>

                    <!-- 5. BUTTON: Standard storefront button component -->
                    <div class="pt-1">
                        <x-storefront.button 
                            type="button" 
                            @click="submitDemoOrder()" 
                            variant="primary" 
                            size="lg" 
                            class="w-full !min-h-[48px]"
                        >
                            Place Demo Order
                        </x-storefront.button>
                    </div>

                    <!-- Trust line at 13px with higher contrast -->
                    <!-- DEMO PLACEHOLDER: replace before launch -->
                    <div class="pt-3 text-center text-[13px] text-ink/80 space-y-1 font-sans">
                        <p>{{ config('demo.site.trust_badges.returns_policy', '30-Day Complimentary Botanical Returns') }}</p>
                        <p>{{ config('demo.site.trust_badges.support_contact', 'Concierge Support: ' . config('demo.site.contact.email', 'concierge@the-everbloom.com')) }}</p>
                    </div>
                </section>

            </div>

            <!-- 2, 3 & 7. RIGHT COLUMN: STICKY ORDER SUMMARY ON DESKTOP (lg:col-span-5 lg:sticky lg:top-24) -->
            <aside class="hidden lg:block lg:col-span-5 lg:sticky lg:top-24" aria-label="Order Summary">
                <div class="bg-white p-6 lg:p-7 border border-[#A8B7A0] space-y-6 shadow-xs">
                    
                    <!-- Heading with spacing under Order Summary so it does not touch the divider -->
                    <div class="border-b border-beige/80 pb-4 mb-2 flex items-baseline justify-between">
                        <h2 class="font-serif text-xl text-ink font-normal pb-0.5 lining-nums">
                            Order Summary
                        </h2>
                        <span class="text-xs uppercase tracking-widest text-muted lining-nums" x-text="'(' + ($store.cart.count || 0) + ' items)'"></span>
                    </div>

                    <!-- 2. Items Table: Plain flow with no max-height or scrollbar -->
                    <div class="divide-y divide-beige/80">
                        <template x-for="item in cartLines" :key="item.key || (item.productId + '-' + (item.variantId || ''))">
                            <div class="py-3.5 flex items-center gap-4">
                                <!-- Thumbnail: relative, 64x64, shrink-0 with quantity badge at -top-2 -right-2 (20px) -->
                                <div class="relative shrink-0 w-16 h-16 bg-white border border-[#A8B7A0]">
                                    <x-storefront.image 
                                        ::src="'/images/' + (item.image || '')" 
                                        ::alt="item.name || 'Botanical product'" 
                                        aspect-ratio="aspect-square" 
                                        class="w-full h-full object-cover" 
                                    />
                                    <span 
                                        class="absolute -top-2 -right-2 w-5 h-5 bg-forest text-ivory text-[11px] font-medium rounded-full flex items-center justify-center shadow-sm pointer-events-none z-10 lining-nums" 
                                        x-text="item.qty || item.quantity"
                                    ></span>
                                </div>

                                <!-- Gap-4 between thumbnail and name, name in flex-1 min-w-0 -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-serif text-sm font-medium text-ink truncate leading-snug lining-nums" x-text="item.name"></h3>
                                    <p class="text-xs text-muted mt-0.5 truncate lining-nums" x-show="item.size || (item.variant && item.variant.size)" x-text="item.size || (item.variant ? item.variant.size : '')"></p>
                                </div>

                                <!-- Price right-aligned with shrink-0 and padding-left -->
                                <div class="shrink-0 text-right pl-3">
                                    <span class="text-sm font-medium text-ink font-serif lining-nums" x-text="'$' + (item.lineTotal !== undefined ? item.lineTotal : (item.price * item.qty)).toFixed(2)"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- 2. One small Edit bag link under the rows -->
                    <div class="pt-1 text-right">
                        <a href="/cart" class="text-xs uppercase tracking-widest text-muted hover:text-forest transition-colors font-medium underline">
                            Edit bag
                        </a>
                    </div>

                    <!-- 3 & 5. Discount Code Field with Apply Button Component -->
                    <div class="pt-4 border-t border-beige/80 space-y-2">
                        <div class="flex gap-2">
                            <input 
                                type="text" 
                                x-model="discountInput" 
                                placeholder="Discount code" 
                                class="flex-1 px-3.5 py-3 text-[16px] bg-white border border-[#A8B7A0] rounded-sm min-h-[48px] placeholder-[#5C6656] focus:outline-none focus:ring-2 focus:ring-forest focus:border-forest"
                                @keydown.enter.prevent="applyDiscountCode()"
                            >
                            <x-storefront.button 
                                type="button" 
                                @click="applyDiscountCode()" 
                                variant="secondary" 
                                size="md" 
                                class="shrink-0 !min-h-[48px] !px-5"
                            >
                                Apply
                            </x-storefront.button>
                        </div>
                        <p x-show="$store.cart.discountError" x-cloak class="text-xs text-rose-700 font-medium" x-text="$store.cart.discountError"></p>
                        <template x-if="$store.cart.discountCode">
                            <div class="flex items-center justify-between text-xs bg-sage-light/60 px-3 py-2 border border-forest/20 text-forest">
                                <span class="font-medium lining-nums" x-text="'Applied: ' + $store.cart.discountCode + ($store.cart.discount ? ' (' + $store.cart.discount.label + ')' : '')"></span>
                                <button type="button" @click="$store.cart.removeDiscount()" class="text-xs uppercase tracking-wider hover:underline text-ink font-semibold ml-2">
                                    Remove
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Subtotal, Discount, Shipping, Total with lining-nums -->
                    <div class="pt-4 border-t border-beige/80 space-y-2.5 text-sm">
                        <div class="flex justify-between text-muted">
                            <span>Subtotal</span>
                            <span class="font-medium text-ink font-serif lining-nums" x-text="'$' + ($store.cart.subtotal || 0).toFixed(2)"></span>
                        </div>

                        <!-- Discount (only when applied) -->
                        <div x-show="$store.cart.discountAmount > 0" x-cloak class="flex justify-between text-forest font-medium">
                            <span>Discount (<span x-text="$store.cart.discountCode"></span>)</span>
                            <span class="font-serif lining-nums" x-text="'-$' + ($store.cart.discountAmount || 0).toFixed(2)"></span>
                        </div>

                        <!-- Shipping Row: Reflects selected method with consistent wording -->
                        <div class="flex justify-between text-muted">
                            <span x-text="'Shipping (' + (selectedShippingRate ? selectedShippingRate.name : 'Standard') + ')'"></span>
                            <span :class="calculatedShippingCost === 0 ? 'text-forest font-medium' : 'text-ink font-medium font-serif lining-nums'" 
                                  x-text="calculatedShippingCost === 0 ? 'Complimentary' : '$' + calculatedShippingCost.toFixed(2)"></span>
                        </div>

                        <!-- Total -->
                        <div class="border-t border-beige pt-3.5 flex justify-between font-serif text-lg font-semibold text-ink">
                            <span>Total Due</span>
                            <span class="lining-nums" x-text="'$' + calculatedTotal.toFixed(2)"></span>
                        </div>
                    </div>

                </div>
            </aside>

        </div>
    </div>
</div>

<script>
function checkoutComponent() {
    return {
        orderPlaced: false,
        showMobileSummary: false,
        discountInput: '',
        shippingMethod: 'standard',
        paymentMethod: 'card_demo',
        shippingRates: @js($shippingRates),
        form: {
            email: '',
            phone: '',
            firstName: '',
            lastName: '',
            country: 'US',
            address: '',
            apartment: '',
            city: '',
            state: '',
            zip: '',
        },
        errors: {},
        fieldIdMap: {
            email: 'email',
            phone: 'phone',
            firstName: 'first_name',
            lastName: 'last_name',
            country: 'country',
            address: 'address',
            city: 'city',
            state: 'state',
            zip: 'postal_code',
        },

        init() {
            // Re-validate against catalog upon component initialization
            if (this.$store && this.$store.cart && typeof this.$store.cart.cleanAndValidate === 'function') {
                this.$store.cart.cleanAndValidate();
            }
        },

        get cartLines() {
            if (this.$store && this.$store.cart) {
                return this.$store.cart.lines || this.$store.cart.items || [];
            }
            return [];
        },

        get cartIsEmpty() {
            return this.cartLines.length === 0;
        },

        get stateLabel() {
            if (this.form.country === 'US') return 'State';
            if (this.form.country === 'CA') return 'Province';
            if (this.form.country === 'GB') return 'County / Region';
            if (this.form.country === 'AU') return 'State / Territory';
            return 'State / Province';
        },

        get postalCodeLabel() {
            if (this.form.country === 'US') return 'ZIP Code';
            if (this.form.country === 'GB') return 'Postcode';
            return 'Postal Code';
        },

        get selectedShippingRate() {
            return this.shippingRates[this.shippingMethod] || this.shippingRates['standard'] || null;
        },

        get calculatedShippingCost() {
            const rate = this.selectedShippingRate;
            if (!rate || !this.$store || !this.$store.cart) return 0;
            const subtotal = this.$store.cart.subtotal || 0;
            const discount = this.$store.cart.discount;
            if (discount && discount.type === 'shipping') return 0;
            if (rate.free_threshold && subtotal >= rate.free_threshold) return 0;
            return parseFloat(rate.cost) || 0;
        },

        get calculatedTotal() {
            if (!this.$store || !this.$store.cart) return 0;
            const subtotal = this.$store.cart.subtotal || 0;
            const discountAmt = this.$store.cart.discountAmount || 0;
            return Math.max(0, subtotal - discountAmt + this.calculatedShippingCost);
        },

        getShippingRatePrice(key) {
            const rate = this.shippingRates[key];
            if (!rate || !this.$store || !this.$store.cart) return '';
            const subtotal = this.$store.cart.subtotal || 0;
            const discount = this.$store.cart.discount;
            if (discount && discount.type === 'shipping') return 'Complimentary';
            if (rate.free_threshold && subtotal >= rate.free_threshold) {
                return 'Complimentary';
            }
            return '$' + parseFloat(rate.cost).toFixed(2);
        },

        applyDiscountCode() {
            if (!this.discountInput.trim()) return;
            const success = this.$store.cart.applyDiscount(this.discountInput);
            if (success) {
                this.discountInput = '';
            }
        },

        clearError(field) {
            if (this.errors[field]) {
                delete this.errors[field];
            }
        },

        validate() {
            const errs = {};
            if (!this.form.email.trim()) {
                errs.email = 'Please enter your email address.';
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email.trim())) {
                errs.email = 'Please enter a valid email address.';
            }

            if (!this.form.phone.trim()) {
                errs.phone = 'Please enter your phone number.';
            }

            if (!this.form.firstName.trim()) {
                errs.firstName = 'Please enter your first name.';
            }

            if (!this.form.lastName.trim()) {
                errs.lastName = 'Please enter your last name.';
            }

            if (!this.form.country.trim()) {
                errs.country = 'Please select a country.';
            }

            if (!this.form.address.trim()) {
                errs.address = 'Please enter your street address.';
            }

            if (!this.form.city.trim()) {
                errs.city = 'Please enter your city.';
            }

            if (!this.form.state.trim()) {
                errs.state = 'Please enter your state or province.';
            }

            if (!this.form.zip.trim()) {
                errs.zip = 'Please enter your postal or ZIP code.';
            }

            this.errors = errs;
            return Object.keys(errs).length === 0;
        },

        submitDemoOrder() {
            if (!this.validate()) {
                const firstKey = Object.keys(this.errors)[0];
                const targetId = this.fieldIdMap[firstKey];
                if (targetId) {
                    this.$nextTick(() => {
                        const el = document.getElementById(targetId);
                        if (el) {
                            el.focus();
                            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    });
                }
                return;
            }

            this.orderPlaced = true;
            this.$nextTick(() => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    };
}
</script>
@endsection
