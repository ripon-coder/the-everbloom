@extends('layouts.checkout')

@section('title', 'Checkout — ' . config('demo.site.name'))
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
            'free_threshold' => 75.00,
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

<div class="site-container py-8 md:py-12" x-data="checkoutComponent()">

    <!-- EMPTY STATE: When bag has 0 items and no order completed -->
    <div x-show="cartIsEmpty && !orderPlaced" x-cloak class="py-16 px-6 bg-white border border-[#E3E6DD] text-center max-w-xl mx-auto space-y-5">
        <div class="w-16 h-16 rounded-full bg-sage-light/60 flex items-center justify-center mx-auto text-forest/70">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
        </div>
        <div class="space-y-2">
            <h1 class="font-serif text-2xl sm:text-3xl text-ink font-normal">
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
    <div x-show="orderPlaced" x-cloak class="py-16 px-6 bg-white border border-[#E3E6DD] text-center max-w-xl mx-auto space-y-5" role="status" aria-live="polite">
        <div class="w-14 h-14 rounded-full bg-forest text-ivory flex items-center justify-center mx-auto">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <div class="space-y-2">
            <h1 class="font-serif text-2xl sm:text-3xl text-ink font-normal" x-text="placedOrderNumber ? 'Thank you. Order #' + placedOrderNumber + ' confirmed.' : 'Thank you. Your order has been confirmed.'">
                Thank you. Your order has been confirmed.
            </h1>
            <p class="text-sm text-muted max-w-md mx-auto leading-relaxed">
                Thank you for your order with The Everbloom. Your botanical selections are being prepared for dispatch.
            </p>
        </div>
        <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
            <template x-if="placedOrderUrl">
                <a :href="placedOrderUrl" class="btn-primary inline-flex items-center justify-center">
                    View Order Details
                </a>
            </template>
            <x-storefront.button href="/shop" variant="secondary">
                Continue Shopping
            </x-storefront.button>
        </div>
    </div>

    <!-- CHECKOUT MAIN GRID (When items present and not completed) -->
    <div x-show="!cartIsEmpty && !orderPlaced" class="space-y-6">
        
        <!-- Semantic H1 for Checkout -->
        <div class="flex items-baseline justify-between border-b border-[#ECEEE6] pb-4">
            <h1 class="font-serif text-2xl sm:text-3xl text-ink font-normal">Checkout</h1>
            <a href="/cart" class="text-[13px] text-muted hover:text-forest underline underline-offset-4 transition-colors">
                Return to bag
            </a>
        </div>

        <!-- MOBILE COLLAPSIBLE ORDER SUMMARY BAR (lg:hidden, not sticky) -->
        <div class="lg:hidden border border-[#E3E6DD] bg-white">
            <button 
                type="button" 
                @click="showMobileSummary = !showMobileSummary" 
                class="w-full flex items-center justify-between p-4 text-left font-medium text-ink min-h-[48px] cursor-pointer"
                :aria-expanded="showMobileSummary ? 'true' : 'false'"
                aria-controls="mobile-order-summary-panel"
            >
                <div class="flex items-center gap-2 text-xs uppercase tracking-[0.2em] text-forest font-semibold">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span x-text="showMobileSummary ? 'Hide order summary' : 'Show order summary'">Show order summary</span>
                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': showMobileSummary }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <span class="price text-base text-ink" x-text="'{{ $currencySymbol }}' + calculatedTotal.toFixed(2)"></span>
            </button>

            <!-- Collapsible Summary Content on Mobile -->
            <div id="mobile-order-summary-panel" x-show="showMobileSummary" x-collapse x-cloak class="px-4 pb-5 pt-3 border-t border-[#ECEEE6] bg-ivory/40 space-y-4">
                
                <!-- Items List & Edit Bag Link -->
                <div>
                    <div class="divide-y divide-[#ECEEE6]">
                        <template x-for="item in cartLines" :key="item.key || (item.productId + '-' + (item.variantId || ''))">
                            <div class="py-2.5 flex items-center gap-4">
                                <!-- Thumbnail: relative, 64x64, shrink-0 with 20px badge at -top-2 -right-2, overflow visible -->
                                <div class="relative w-16 h-16 shrink-0 overflow-visible bg-white border border-[#E3E6DD]">
                                    <x-storefront.image 
                                        ::src="item.image ? ((item.image.startsWith('http') || item.image.startsWith('/')) ? item.image : '/images/' + item.image) : ''" 
                                        ::alt="item.name || 'Botanical formulation'" 
                                        aspect-ratio="aspect-square" 
                                        class="w-full h-full object-cover" 
                                    />
                                    <span 
                                        class="price absolute -top-2 -right-2 w-5 h-5 bg-forest text-ivory text-xs font-medium rounded-full flex items-center justify-center pointer-events-none z-10" 
                                        x-text="item.qty || item.quantity"
                                    ></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-serif text-sm font-medium text-ink truncate leading-snug" x-text="item.name"></h3>
                                    <p class="text-xs text-muted mt-0.5 truncate" x-show="item.size || (item.variant && item.variant.size)" x-text="item.size || (item.variant ? item.variant.size : '')"></p>
                                </div>
                                <div class="shrink-0 text-right pl-3">
                                    <span class="price text-sm font-medium text-ink" x-text="'{{ $currencySymbol }}' + (item.lineTotal !== undefined ? item.lineTotal : (item.price * item.qty)).toFixed(2)"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Edit bag link sitting directly under the row -->
                    <div class="mt-2 text-right">
                        <a href="/cart" class="text-xs text-muted hover:text-forest transition-colors font-medium underline underline-offset-4">
                            Edit bag
                        </a>
                    </div>
                </div>

                <!-- Mobile Promo Code Input & Apply Button -->
                <div class="pt-3 border-t border-[#ECEEE6] space-y-2" aria-live="polite">
                    <label for="mobile-discount-code" class="sr-only">Discount code</label>
                    <div class="flex gap-2 items-center">
                        <input 
                            id="mobile-discount-code"
                            type="text" 
                            x-model="discountInput" 
                            placeholder="Discount code" 
                            class="input-field flex-1"
                            @keydown.enter.prevent="applyDiscountCode()"
                        >
                        <button 
                            type="button" 
                            @click="applyDiscountCode()" 
                            class="btn-secondary !h-12 !min-h-[48px] !px-5 shrink-0 cursor-pointer"
                        >
                            Apply
                        </button>
                    </div>
                    <p x-show="$store.cart.discountError" x-cloak class="mt-1 text-[13px] text-[#B3453A] font-medium" x-text="$store.cart.discountError" role="alert"></p>
                    <template x-if="$store.cart.discountCode">
                        <div class="flex items-center justify-between text-xs bg-sage-light/60 px-3 py-2 border border-[#E3E6DD] text-forest">
                            <span class="font-medium price" x-text="'Applied: ' + $store.cart.discountCode + ($store.cart.discount ? ' (' + $store.cart.discount.label + ')' : '')"></span>
                            <button type="button" @click="$store.cart.removeDiscount()" class="text-xs uppercase tracking-wider hover:underline text-ink font-semibold ml-2 cursor-pointer">
                                Remove
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Mobile Subtotal & Totals with shared price class -->
                <div class="pt-3 border-t border-[#ECEEE6] space-y-2 text-sm">
                    <div class="flex justify-between text-muted">
                        <span>Subtotal</span>
                        <span class="price font-medium text-ink" x-text="'{{ $currencySymbol }}' + ($store.cart.subtotal || 0).toFixed(2)"></span>
                    </div>

                    <div x-show="$store.cart.discountAmount > 0" x-cloak class="flex justify-between text-forest font-medium">
                        <span>Discount (<span x-text="$store.cart.discountCode"></span>)</span>
                        <span class="price" x-text="'-{{ $currencySymbol }}' + ($store.cart.discountAmount || 0).toFixed(2)"></span>
                    </div>

                    <div class="flex justify-between text-muted">
                        <span x-text="'Shipping (' + (selectedShippingRate ? selectedShippingRate.name : 'Standard') + ')'"></span>
                        <span :class="calculatedShippingCost === 0 ? 'text-forest font-medium' : 'price text-ink font-medium'" 
                              x-text="calculatedShippingCost === 0 ? 'Complimentary' : '{{ $currencySymbol }}' + calculatedShippingCost.toFixed(2)"></span>
                    </div>

                    <div class="border-t border-[#ECEEE6] pt-3 flex justify-between text-base font-medium text-ink">
                        <span class="font-serif">Total Due</span>
                        <span class="price text-base text-ink" x-text="'{{ $currencySymbol }}' + calculatedTotal.toFixed(2)"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2-COLUMN LAYOUT ON DESKTOP: Form 7/12, Summary 5/12, gap-12 -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            <!-- LEFT COLUMN: ONE WHITE PANEL WITH DIVIDERS (lg:col-span-7) -->
            <div class="lg:col-span-7 bg-white border border-[#E3E6DD] divide-y divide-[#ECEEE6]">
                
                <!-- SECTION 1: CONTACT -->
                <section class="px-6 sm:px-8 pt-8 pb-6 space-y-4" aria-labelledby="section-contact-heading">
                    <div class="flex items-center justify-between">
                        <h2 id="section-contact-heading" class="font-serif text-lg sm:text-xl text-ink font-medium">
                            1. Contact Information
                        </h2>
                        <span class="text-xs text-muted">* Required</span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-xs uppercase tracking-[0.2em] text-muted mb-1.5 font-medium">
                                Email Address *
                            </label>
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                autocomplete="email" 
                                x-model="form.email" 
                                @input="clearError('email')"
                                :aria-invalid="errors.email ? 'true' : 'false'"
                                :aria-describedby="errors.email ? 'email-error' : null"
                                placeholder="name@example.com" 
                                class="input-field"
                                :class="{ 'input-error': errors.email }"
                            >
                            <p id="email-error" x-show="errors.email" x-cloak class="mt-1 text-[13px] text-[#B3453A] font-medium" x-text="errors.email" role="alert"></p>
                        </div>

                        <div>
                            <label for="phone" class="block text-xs uppercase tracking-[0.2em] text-muted mb-1.5 font-medium">
                                Phone (optional)
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
                                placeholder="Phone number" 
                                class="input-field"
                                :class="{ 'input-error': errors.phone }"
                            >
                            <p id="phone-error" x-show="errors.phone" x-cloak class="mt-1 text-[13px] text-[#B3453A] font-medium" x-text="errors.phone" role="alert"></p>
                        </div>
                    </div>
                </section>

                <!-- SECTION 2: SHIPPING ADDRESS -->
                <section class="px-6 sm:px-8 pt-8 pb-6 space-y-4" aria-labelledby="section-shipping-heading">
                    <h2 id="section-shipping-heading" class="font-serif text-lg sm:text-xl text-ink font-medium">
                        2. Shipping Address
                    </h2>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-xs uppercase tracking-[0.2em] text-muted mb-1.5 font-medium">
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
                                    placeholder="First name"
                                    class="input-field"
                                    :class="{ 'input-error': errors.firstName }"
                                >
                                <p id="first_name-error" x-show="errors.firstName" x-cloak class="mt-1 text-[13px] text-[#B3453A] font-medium" x-text="errors.firstName" role="alert"></p>
                            </div>

                            <div>
                                <label for="last_name" class="block text-xs uppercase tracking-[0.2em] text-muted mb-1.5 font-medium">
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
                                    placeholder="Last name"
                                    class="input-field"
                                    :class="{ 'input-error': errors.lastName }"
                                >
                                <p id="last_name-error" x-show="errors.lastName" x-cloak class="mt-1 text-[13px] text-[#B3453A] font-medium" x-text="errors.lastName" role="alert"></p>
                            </div>
                        </div>

                        <!-- Country (Fixed: Bangladesh) -->
                        <div>
                            <label for="country" class="block text-xs uppercase tracking-[0.2em] text-muted mb-1.5 font-medium">
                                Country / Region *
                            </label>
                            <input 
                                id="country" 
                                name="country" 
                                type="text" 
                                x-model="form.country" 
                                readonly 
                                class="input-field bg-ivory/60 cursor-not-allowed"
                            >
                        </div>

                        <!-- District, Thana, Area (Bangladesh Cascading Selects) -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="district_id" class="block text-xs uppercase tracking-[0.2em] text-muted mb-1.5 font-medium">
                                    District *
                                </label>
                                <div class="relative">
                                    <select 
                                        id="district_id" 
                                        name="district_id" 
                                        x-model="form.district_id" 
                                        @change="onDistrictChange()"
                                        :aria-invalid="errors.district_id ? 'true' : 'false'"
                                        :aria-describedby="errors.district_id ? 'district_id-error' : null"
                                        class="input-field pr-10 appearance-none cursor-pointer"
                                        :class="{ 'input-error': errors.district_id }"
                                    >
                                        <option value="">Select District</option>
                                        <template x-for="d in districts" :key="d.id">
                                            <option :value="String(d.id)" x-text="d.name"></option>
                                        </template>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-forest">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                <p id="district_id-error" x-show="errors.district_id" x-cloak class="mt-1 text-[13px] text-[#B3453A] font-medium" x-text="errors.district_id" role="alert"></p>
                            </div>

                            <div>
                                <label for="thana_id" class="block text-xs uppercase tracking-[0.2em] text-muted mb-1.5 font-medium">
                                    Thana / Upazila *
                                </label>
                                <div class="relative">
                                    <select 
                                        id="thana_id" 
                                        name="thana_id" 
                                        x-model="form.thana_id" 
                                        @change="onThanaChange()"
                                        :disabled="!form.district_id"
                                        :aria-invalid="errors.thana_id ? 'true' : 'false'"
                                        :aria-describedby="errors.thana_id ? 'thana_id-error' : null"
                                        class="input-field pr-10 appearance-none cursor-pointer disabled:bg-ivory/60 disabled:cursor-not-allowed"
                                        :class="{ 'input-error': errors.thana_id }"
                                    >
                                        <option value="">Select Thana</option>
                                        <template x-for="t in thanas" :key="t.id">
                                            <option :value="String(t.id)" x-text="t.name"></option>
                                        </template>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-forest">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                <p id="thana_id-error" x-show="errors.thana_id" x-cloak class="mt-1 text-[13px] text-[#B3453A] font-medium" x-text="errors.thana_id" role="alert"></p>
                            </div>

                            <div>
                                <label for="area_id" class="block text-xs uppercase tracking-[0.2em] text-muted mb-1.5 font-medium">
                                    Area / Locality *
                                </label>
                                <div class="relative">
                                    <select 
                                        id="area_id" 
                                        name="area_id" 
                                        x-model="form.area_id" 
                                        @change="onAreaChange()"
                                        :disabled="!form.thana_id"
                                        :aria-invalid="errors.area_id ? 'true' : 'false'"
                                        :aria-describedby="errors.area_id ? 'area_id-error' : null"
                                        class="input-field pr-10 appearance-none cursor-pointer disabled:bg-ivory/60 disabled:cursor-not-allowed"
                                        :class="{ 'input-error': errors.area_id }"
                                    >
                                        <option value="">Select Area</option>
                                        <template x-for="a in areas" :key="a.id">
                                            <option :value="String(a.id)" x-text="a.name"></option>
                                        </template>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-forest">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                <p id="area_id-error" x-show="errors.area_id" x-cloak class="mt-1 text-[13px] text-[#B3453A] font-medium" x-text="errors.area_id" role="alert"></p>
                            </div>
                        </div>

                        <!-- Street Address -->
                        <div>
                            <label for="address" class="block text-xs uppercase tracking-[0.2em] text-muted mb-1.5 font-medium">
                                House / Road / Block *
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
                                placeholder="House, road, block" 
                                class="input-field"
                                :class="{ 'input-error': errors.address }"
                            >
                            <p id="address-error" x-show="errors.address" x-cloak class="mt-1 text-[13px] text-[#B3453A] font-medium" x-text="errors.address" role="alert"></p>
                        </div>

                        <!-- Apartment / Suite & Postal Code -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="apartment" class="block text-xs uppercase tracking-[0.2em] text-muted mb-1.5 font-medium">
                                    Apartment, suite, floor (optional)
                                </label>
                                <input 
                                    id="apartment" 
                                    name="apartment" 
                                    type="text" 
                                    autocomplete="address-line2" 
                                    x-model="form.apartment" 
                                    placeholder="Apartment, suite, floor (optional)" 
                                    class="input-field"
                                >
                            </div>

                            <div>
                                <label for="postal_code" class="block text-xs uppercase tracking-[0.2em] text-muted mb-1.5 font-medium">
                                    Postal Code (optional)
                                </label>
                                <input 
                                    id="postal_code" 
                                    name="postal_code" 
                                    type="text" 
                                    autocomplete="postal-code" 
                                    x-model="form.zip" 
                                    @input="clearError('zip')"
                                    placeholder="Postal Code"
                                    class="input-field"
                                >
                            </div>
                        </div>
                    </div>
                </section>

                <!-- SECTION 3: DELIVERY / SHIPPING METHOD RADIOS -->
                <section class="px-6 sm:px-8 pt-8 pb-6 space-y-4" aria-labelledby="section-delivery-heading">
                    <div>
                        <h2 id="section-delivery-heading" class="font-serif text-lg sm:text-xl text-ink font-medium">
                            3. Delivery Method
                        </h2>
                        <p class="text-xs text-muted mt-0.5">Selection automatically updates your shipping fee and total.</p>
                    </div>

                    <div class="space-y-3" role="radiogroup" aria-label="Shipping Method">
                        @foreach($shippingRates as $rateKey => $rate)
                        <label 
                            class="relative flex items-center justify-between p-4 rounded-none cursor-pointer transition-colors min-h-[48px] border"
                            :class="shippingMethod === '{{ $rateKey }}' ? 'border-[#304C3D] bg-[#E8EEE5]' : 'border-[#D5DBCF] bg-white hover:border-[#A8B7A0]'"
                        >
                            <div class="flex items-center gap-3">
                                <input 
                                    type="radio" 
                                    name="shipping_method" 
                                    value="{{ $rateKey }}" 
                                    x-model="shippingMethod" 
                                    class="w-4 h-4 text-forest accent-[#304C3D] cursor-pointer"
                                >
                                <div>
                                    <span class="block text-sm font-medium text-ink">{{ $rate['name'] }}</span>
                                    <span class="block text-xs text-muted mt-0.5">{{ $rate['time'] }}</span>
                                </div>
                            </div>
                            <span class="price text-sm font-medium text-ink shrink-0 ml-3" x-text="getShippingRatePrice('{{ $rateKey }}')">
                                {{ $currencySymbol }}{{ number_format($rate['cost'], 2) }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </section>

                <!-- SECTION 4: PAYMENT METHOD -->
                <section class="px-6 sm:px-8 pt-8 pb-6 space-y-5" aria-labelledby="section-payment-heading">
                    <div>
                        <h2 id="section-payment-heading" class="font-serif text-lg sm:text-xl text-ink font-medium">
                            4. Payment Method
                        </h2>
                    </div>

                    <div class="space-y-3" role="radiogroup" aria-label="Payment Method">
                        <label 
                            class="relative flex items-center justify-between p-4 rounded-none cursor-pointer transition-colors min-h-[48px] border"
                            :class="paymentMethod === 'card_demo' ? 'border-[#304C3D] bg-[#E8EEE5]' : 'border-[#D5DBCF] bg-white hover:border-[#A8B7A0]'"
                        >
                            <div class="flex items-center gap-3">
                                <input 
                                    type="radio" 
                                    name="payment_method" 
                                    value="card_demo" 
                                    x-model="paymentMethod" 
                                    class="w-4 h-4 text-forest accent-[#304C3D] cursor-pointer"
                                >
                                <div>
                                    <span class="block text-sm font-medium text-ink">Credit / Debit Card</span>
                                    <span class="block text-xs text-muted mt-0.5">Pay securely with your credit or debit card.</span>
                                </div>
                            </div>
                        </label>

                        <label 
                            class="relative flex items-center justify-between p-4 rounded-none cursor-pointer transition-colors min-h-[48px] border"
                            :class="paymentMethod === 'digital_wallet_demo' ? 'border-[#304C3D] bg-[#E8EEE5]' : 'border-[#D5DBCF] bg-white hover:border-[#A8B7A0]'"
                        >
                            <div class="flex items-center gap-3">
                                <input 
                                    type="radio" 
                                    name="payment_method" 
                                    value="digital_wallet_demo" 
                                    x-model="paymentMethod" 
                                    class="w-4 h-4 text-forest accent-[#304C3D] cursor-pointer"
                                >
                                <div>
                                    <span class="block text-sm font-medium text-ink">Digital Wallet / Cash on Delivery</span>
                                    <span class="block text-xs text-muted mt-0.5">Fast, convenient payment upon confirmation.</span>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="pt-1">
                        <x-storefront.button 
                            type="button" 
                            @click="submitDemoOrder()" 
                            variant="primary" 
                            size="lg" 
                            class="w-full !min-h-[48px]"
                        >
                            Place Order
                        </x-storefront.button>
                    </div>

                    <!-- DEMO PLACEHOLDER: replace before launch -->
                    <div class="pt-3 text-center text-[13px] text-muted space-y-1 font-sans">
                        <p>{{ config('demo.site.trust_badges.returns_policy', '30-Day Returns on Unopened Items') }}</p>
                        <p>{{ config('demo.site.trust_badges.support_contact', 'Client Care: ' . config('demo.site.contact.email', 'concierge@the-everbloom.com')) }}</p>
                    </div>
                </section>

            </div>

            <!-- RIGHT COLUMN: STICKY ORDER SUMMARY ON DESKTOP (lg:col-span-5 lg:sticky lg:top-6 h-fit) -->
            <aside class="hidden lg:block lg:col-span-5 lg:sticky lg:top-6 h-fit" aria-label="Order Summary">
                <div class="bg-white p-6 lg:p-7 border border-[#E3E6DD] space-y-6" aria-live="polite">
                    
                    <div class="border-b border-[#ECEEE6] pb-4 flex items-baseline justify-between">
                        <h2 class="font-serif text-xl text-ink font-normal pb-0.5">
                            Order Summary
                        </h2>
                        <span class="text-xs text-muted" x-text="($store.cart.count === 1) ? '1 item' : (($store.cart.count || 0) + ' items')"></span>
                    </div>

                    <!-- Items Table and Edit bag link -->
                    <div>
                        <div class="divide-y divide-[#ECEEE6]">
                            <template x-for="item in cartLines" :key="item.key || (item.productId + '-' + (item.variantId || ''))">
                                <div class="py-2.5 flex items-center gap-4">
                                    <!-- Thumbnail wrapper: relative, 64x64, shrink-0, badge at -top-2 -right-2 (20px forest circle, ivory text), overflow visible -->
                                    <div class="relative w-16 h-16 shrink-0 overflow-visible bg-white border border-[#E3E6DD]">
                                        <x-storefront.image 
                                            ::src="item.image ? ((item.image.startsWith('http') || item.image.startsWith('/')) ? item.image : '/images/' + item.image) : ''" 
                                            ::alt="item.name || 'Botanical product'" 
                                            aspect-ratio="aspect-square" 
                                            class="w-full h-full object-cover" 
                                        />
                                        <span 
                                            class="price absolute -top-2 -right-2 w-5 h-5 bg-forest text-ivory text-xs font-medium rounded-full flex items-center justify-center pointer-events-none z-10" 
                                            x-text="item.qty || item.quantity"
                                        ></span>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-serif text-sm font-medium text-ink truncate leading-snug" x-text="item.name"></h3>
                                        <p class="text-xs text-muted mt-0.5 truncate" x-show="item.size || (item.variant && item.variant.size)" x-text="item.size || (item.variant ? item.variant.size : '')"></p>
                                    </div>

                                    <div class="shrink-0 text-right pl-3">
                                        <span class="price text-sm font-medium text-ink" x-text="'{{ $currencySymbol }}' + (item.lineTotal !== undefined ? item.lineTotal : (item.price * item.qty)).toFixed(2)"></span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="mt-2 text-right">
                            <a href="/cart" class="text-xs text-muted hover:text-forest transition-colors font-medium underline underline-offset-4">
                                Edit bag
                            </a>
                        </div>
                    </div>

                    <!-- Discount Code Field with Apply Button -->
                    <div class="pt-4 border-t border-[#ECEEE6] space-y-2">
                        <label for="desktop-discount-code" class="sr-only">Discount code</label>
                        <div class="flex gap-2 items-center">
                            <input 
                                id="desktop-discount-code"
                                type="text" 
                                x-model="discountInput" 
                                placeholder="Discount code" 
                                class="input-field flex-1"
                                @keydown.enter.prevent="applyDiscountCode()"
                            >
                            <button 
                                type="button" 
                                @click="applyDiscountCode()" 
                                class="btn-secondary !h-12 !min-h-[48px] !px-5 shrink-0 cursor-pointer"
                            >
                                Apply
                            </button>
                        </div>
                        <p x-show="$store.cart.discountError" x-cloak class="mt-1 text-[13px] text-[#B3453A] font-medium" x-text="$store.cart.discountError" role="alert"></p>
                        <template x-if="$store.cart.discountCode">
                            <div class="flex items-center justify-between text-xs bg-sage-light/60 px-3 py-2 border border-[#E3E6DD] text-forest">
                                <span class="font-medium price" x-text="'Applied: ' + $store.cart.discountCode + ($store.cart.discount ? ' (' + $store.cart.discount.label + ')' : '')"></span>
                                <button type="button" @click="$store.cart.removeDiscount()" class="text-xs uppercase tracking-wider hover:underline text-ink font-semibold ml-2 cursor-pointer">
                                    Remove
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Subtotal, Discount, Shipping, Total with shared price class -->
                    <div class="pt-4 border-t border-[#ECEEE6] space-y-2.5 text-sm">
                        <div class="flex justify-between text-muted">
                            <span>Subtotal</span>
                            <span class="price font-medium text-ink" x-text="'{{ $currencySymbol }}' + ($store.cart.subtotal || 0).toFixed(2)"></span>
                        </div>

                        <!-- Discount (only when applied) -->
                        <div x-show="$store.cart.discountAmount > 0" x-cloak class="flex justify-between text-forest font-medium">
                            <span>Discount (<span x-text="$store.cart.discountCode"></span>)</span>
                            <span class="price" x-text="'-{{ $currencySymbol }}' + ($store.cart.discountAmount || 0).toFixed(2)"></span>
                        </div>

                        <!-- Shipping Row -->
                        <div class="flex justify-between text-muted">
                            <span x-text="'Shipping (' + (selectedShippingRate ? selectedShippingRate.name : 'Standard') + ')'"></span>
                            <span :class="calculatedShippingCost === 0 ? 'text-forest font-medium' : 'price text-ink font-medium'" 
                                  x-text="calculatedShippingCost === 0 ? 'Complimentary' : '{{ $currencySymbol }}' + calculatedShippingCost.toFixed(2)"></span>
                        </div>

                        <!-- Total -->
                        <div class="border-t border-[#ECEEE6] pt-3.5 flex justify-between text-lg font-medium text-ink">
                            <span class="font-serif">Total Due</span>
                            <span class="price text-lg" x-text="'{{ $currencySymbol }}' + calculatedTotal.toFixed(2)"></span>
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
        placedOrderNumber: '',
        placedOrderUrl: '',
        submitting: false,
        showMobileSummary: false,
        discountInput: '',
        shippingMethod: 'standard',
        paymentMethod: 'card_demo',
        shippingRates: @js($shippingRates),
        districts: @js($districts ?? []),
        thanas: [],
        areas: [],
        form: @js($prefillForm ?? [
            'email' => '',
            'phone' => '',
            'firstName' => '',
            'lastName' => '',
            'country' => 'Bangladesh',
            'district_id' => '',
            'thana_id' => '',
            'area_id' => '',
            'address' => '',
            'apartment' => '',
            'zip' => '',
        ]),
        errors: {},
        fieldIdMap: {
            email: 'email',
            phone: 'phone',
            firstName: 'first_name',
            lastName: 'last_name',
            district_id: 'district_id',
            thana_id: 'thana_id',
            area_id: 'area_id',
            address: 'address',
            zip: 'postal_code',
        },

        async init() {
            if (this.$store && this.$store.cart && typeof this.$store.cart.cleanAndValidate === 'function') {
                this.$store.cart.cleanAndValidate();
            }
            if (this.form.district_id) {
                await this.loadThanas(this.form.district_id);
                if (this.form.thana_id) {
                    await this.loadAreas(this.form.thana_id);
                }
            }
        },

        async loadThanas(districtId) {
            if (!districtId) {
                this.thanas = [];
                return;
            }
            try {
                const res = await fetch(`/locations/districts/${districtId}/thanas`);
                const data = await res.json();
                this.thanas = data.thanas || [];
            } catch (e) {
                this.thanas = [];
            }
        },

        async loadAreas(thanaId) {
            if (!thanaId) {
                this.areas = [];
                return;
            }
            try {
                const res = await fetch(`/locations/thanas/${thanaId}/areas`);
                const data = await res.json();
                this.areas = data.areas || [];
            } catch (e) {
                this.areas = [];
            }
        },

        async onDistrictChange() {
            this.clearError('district_id');
            this.form.thana_id = '';
            this.form.area_id = '';
            this.areas = [];
            await this.loadThanas(this.form.district_id);
        },

        async onThanaChange() {
            this.clearError('thana_id');
            this.form.area_id = '';
            await this.loadAreas(this.form.thana_id);
        },

        onAreaChange() {
            this.clearError('area_id');
            const selected = this.areas.find(a => String(a.id) === String(this.form.area_id));
            if (selected && selected.postal_code) {
                this.form.zip = selected.postal_code;
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
            return '{{ $currencySymbol }}' + parseFloat(rate.cost).toFixed(2);
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

            if (!this.form.firstName.trim()) {
                errs.firstName = 'Please enter your first name.';
            }

            if (!this.form.lastName.trim()) {
                errs.lastName = 'Please enter your last name.';
            }

            if (!this.form.district_id) {
                errs.district_id = 'Please select a district.';
            }

            if (!this.form.thana_id) {
                errs.thana_id = 'Please select a thana.';
            }

            if (!this.form.area_id) {
                errs.area_id = 'Please select an area.';
            }

            if (!this.form.address.trim()) {
                errs.address = 'Please enter your house, road, or block.';
            }

            this.errors = errs;
            return Object.keys(errs).length === 0;
        },

        async submitDemoOrder() {
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

            if (this.submitting) return;
            this.submitting = true;

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const payload = {
                    ...this.form,
                    shippingMethod: this.shippingMethod,
                    paymentMethod: this.paymentMethod,
                    discountCode: this.$store?.cart?.discount?.code || null,
                    items: (this.$store?.cart?.items || []).map(item => ({
                        productId: item.productId,
                        variantId: item.variantId,
                        qty: item.qty,
                    })),
                };

                const response = await fetch(@js(route('checkout.store')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    if (data.errors) {
                        Object.entries(data.errors).forEach(([k, v]) => {
                            this.errors[k] = Array.isArray(v) ? v[0] : v;
                        });
                    }
                    this.submitting = false;
                    return;
                }

                this.placedOrderNumber = data.order_number || '';
                this.placedOrderUrl = data.redirect_url || '';
                if (this.$store && this.$store.cart && typeof this.$store.cart.clear === 'function') {
                    this.$store.cart.clear();
                }
                this.orderPlaced = true;
                this.$nextTick(() => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            } catch (e) {
                this.errors.email = 'Unable to place order right now. Please try again.';
            } finally {
                this.submitting = false;
            }
        }
    };
}
</script>
@endsection
