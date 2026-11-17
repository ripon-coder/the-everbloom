@extends('layouts.checkout')

@section('title', 'Checkout — ' . config('demo.site.name'))
@section('hide_newsletter', true)

@section('content')
@php
    $shippingCfg = config('demo.site.shipping', []);
    $freeShippingThreshold = (float) ($shippingCfg['free_shipping_threshold'] ?? 500);
    $standardFee = (float) ($shippingCfg['standard_fee'] ?? 60);
    $expressFee = (float) ($shippingCfg['express_fee'] ?? 120);
    $standardDays = $shippingCfg['standard_days'] ?? '2–3 business days';
    $expressDays = $shippingCfg['express_days'] ?? '1–2 business days';

    $shippingRates = [
        'standard' => [
            'id' => 'standard',
            'name' => 'Standard Delivery',
            'time' => $standardDays,
            'cost' => $standardFee,
            'free_threshold' => $freeShippingThreshold,
        ],
        'express' => [
            'id' => 'express',
            'name' => 'Express Courier',
            'time' => $expressDays,
            'cost' => $expressFee,
            'free_threshold' => 0,
        ],
    ];
@endphp

<div class="site-container py-8 md:py-12" x-data="checkoutComponent()">

    <!-- EMPTY STATE: When bag has 0 items and no demo submit completed -->
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

    <!-- DEMO SUBMIT FEEDBACK (Never shows an order number) -->
    <div x-show="orderPlaced" x-cloak class="py-16 px-6 bg-white border border-[#E3E6DD] text-center max-w-xl mx-auto space-y-5" role="status" aria-live="polite">
        <div class="w-14 h-14 rounded-full bg-forest text-ivory flex items-center justify-center mx-auto">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <p class="font-serif text-2xl sm:text-3xl text-ink font-normal">
            Demo only. No order was placed.
        </p>
        <div class="pt-2">
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
                <span class="price lining-nums text-base text-ink" x-text="formatMoney(calculatedTotal)"></span>
            </button>

            <!-- Collapsible Summary Content on Mobile -->
            <div id="mobile-order-summary-panel" x-show="showMobileSummary" x-collapse x-cloak class="px-4 pb-5 pt-3 border-t border-[#ECEEE6] bg-ivory/40 space-y-4">

                <!-- Items List & Edit Bag Link -->
                <div>
                    <div class="divide-y divide-[#ECEEE6]">
                        <template x-for="item in cartLines" :key="item.key || (item.productId + '-' + (item.variantId || ''))">
                            <div class="py-2.5 flex items-center gap-4">
                                <div class="relative w-16 h-16 shrink-0 overflow-visible bg-white border border-[#E3E6DD]">
                                    <x-storefront.image 
                                        ::src="item.image ? ((item.image.startsWith('http') || item.image.startsWith('/')) ? item.image : '/images/' + item.image) : ''" 
                                        ::alt="item.name || 'Botanical formulation'" 
                                        aspect-ratio="aspect-square" 
                                        class="w-full h-full object-cover" 
                                    />
                                    <span 
                                        class="price lining-nums absolute -top-2 -right-2 w-5 h-5 bg-forest text-ivory text-xs font-medium rounded-full flex items-center justify-center pointer-events-none z-10" 
                                        x-text="item.qty || item.quantity"
                                    ></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-serif text-sm font-medium text-ink truncate leading-snug" x-text="item.name"></h3>
                                    <p class="text-xs text-muted mt-0.5 truncate" x-show="item.size || (item.variant && item.variant.size)" x-text="item.size || (item.variant ? item.variant.size : '')"></p>
                                </div>
                                <div class="shrink-0 text-right pl-3">
                                    <span class="price lining-nums text-sm font-medium text-ink" x-text="formatMoney(item.lineTotal !== undefined ? item.lineTotal : (item.price * item.qty))"></span>
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

                <!-- Mobile Discount Code Input & Apply Button -->
                <div class="pt-3 border-t border-[#ECEEE6] space-y-2" aria-live="polite">
                    <label for="mobile-discount-code" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium">
                        Discount code
                    </label>
                    <div class="flex gap-2 items-center">
                        <input 
                            id="mobile-discount-code"
                            type="text" 
                            placeholder="Enter code"
                            x-model="discountInput" 
                            class="input-field !h-12 !min-h-[48px] text-base flex-1"
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
                    <template x-if="$store.cart.discountError">
                        <p class="mt-1 text-[13px] text-[#B3453A] font-medium" x-text="$store.cart.discountError" role="alert"></p>
                    </template>
                    <template x-if="$store.cart.discountCode">
                        <div class="flex items-center justify-between text-xs bg-sage-light/60 px-3 py-2 border border-[#E3E6DD] text-forest">
                            <span class="font-medium price" x-text="'Applied: ' + $store.cart.discountCode + ($store.cart.discount ? ' (' + $store.cart.discount.label + ')' : '')"></span>
                            <button type="button" @click="$store.cart.removeDiscount()" class="text-xs uppercase tracking-wider hover:underline text-ink font-semibold ml-2 cursor-pointer">
                                Remove
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Mobile Subtotal & Totals -->
                <div class="pt-3 border-t border-[#ECEEE6] space-y-2 text-sm">
                    <div class="flex justify-between text-muted">
                        <span>Subtotal</span>
                        <span class="price lining-nums font-medium text-ink" x-text="formatMoney($store.cart.subtotal || 0)"></span>
                    </div>

                    <div x-show="$store.cart.discountAmount > 0" x-cloak class="flex justify-between text-forest font-medium">
                        <span>Discount (<span x-text="$store.cart.discountCode"></span>)</span>
                        <span class="price lining-nums" x-text="'-' + formatMoney($store.cart.discountAmount || 0)"></span>
                    </div>

                    <div class="flex justify-between text-muted">
                        <span x-text="'Shipping (' + (selectedShippingRate ? selectedShippingRate.name : 'Standard Delivery') + ')'"></span>
                        <span :class="calculatedShippingCost === 0 ? 'text-forest font-medium' : 'price lining-nums text-ink font-medium'" 
                              x-text="calculatedShippingCost === 0 ? 'Complimentary' : formatMoney(calculatedShippingCost)"></span>
                    </div>

                    <div class="border-t border-[#ECEEE6] pt-3 flex justify-between text-base font-medium text-ink">
                        <span class="font-serif">Total Due</span>
                        <span class="price lining-nums text-base text-ink" x-text="formatMoney(calculatedTotal)"></span>
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
                            <label for="phone" class="block text-xs uppercase tracking-[0.2em] text-muted mb-1.5 font-medium">
                                Phone *
                            </label>
                            <input 
                                id="phone" 
                                name="phone" 
                                type="tel" 
                                inputmode="tel"
                                autocomplete="tel" 
                                placeholder="01XXXXXXXXX"
                                required
                                x-model="form.phone" 
                                @input="clearError('phone')"
                                :aria-invalid="errors.phone ? 'true' : 'false'"
                                :aria-describedby="errors.phone ? 'phone-error' : null"
                                class="input-field !h-12 !min-h-[48px] text-base"
                                :class="errors.phone ? 'input-error !border-[#B3453A]' : ''"
                            >
                            <p id="phone-error" x-show="errors.phone" x-cloak class="mt-1 text-[13px] text-[#B3453A] font-medium" x-text="errors.phone" role="alert"></p>
                        </div>

                        <div>
                            <label for="email" class="block text-xs uppercase tracking-[0.2em] text-muted mb-1.5 font-medium">
                                Email (optional)
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
                                class="input-field !h-12 !min-h-[48px] text-base"
                                :class="errors.email ? 'input-error !border-[#B3453A]' : ''"
                            >
                            <p id="email-error" x-show="errors.email" x-cloak class="mt-1 text-[13px] text-[#B3453A] font-medium" x-text="errors.email" role="alert"></p>
                        </div>
                    </div>
                </section>

                <!-- SECTION 2: SHIPPING ADDRESS -->
                <section class="px-6 sm:px-8 pt-8 pb-6 space-y-5" aria-labelledby="section-shipping-heading">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h2 id="section-shipping-heading" class="font-serif text-lg sm:text-xl text-ink font-medium">
                            2. Shipping Address
                        </h2>

                        @if($isGuest ?? true)
                            <a href="{{ route('login') }}" class="text-xs uppercase tracking-[0.16em] font-medium text-forest hover:text-ink underline underline-offset-4 transition-colors">
                                Sign in for faster checkout
                            </a>
                        @endif
                    </div>

                    <!-- SAVED ADDRESSES RADIOGROUP (Only shown when savedAddresses.length > 0) -->
                    <template x-if="savedAddresses.length > 0">
                        <div class="space-y-3">
                            <span id="deliver-to-label" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium">
                                Deliver to
                            </span>

                            <div 
                                role="radiogroup" 
                                aria-labelledby="deliver-to-label" 
                                class="space-y-2.5"
                            >
                                <template x-for="(addr, idx) in savedAddresses" :key="addr.id">
                                    <div 
                                        role="radio"
                                        tabindex="0"
                                        :aria-checked="selectedAddressId === addr.id ? 'true' : 'false'"
                                        @click="selectSavedAddress(addr.id)"
                                        @keydown.space.prevent="selectSavedAddress(addr.id)"
                                        @keydown.enter.prevent="selectSavedAddress(addr.id)"
                                        @keydown.arrow-down.prevent="focusAddressOption(idx + 1)"
                                        @keydown.arrow-right.prevent="focusAddressOption(idx + 1)"
                                        @keydown.arrow-up.prevent="focusAddressOption(idx - 1)"
                                        @keydown.arrow-left.prevent="focusAddressOption(idx - 1)"
                                        :data-addr-option="idx"
                                        class="w-full min-h-[44px] p-4 border transition-colors cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-1"
                                        :class="selectedAddressId === addr.id ? 'border-[#304C3D] bg-[#E8EEE5]' : 'border-[#D5DBCF] bg-white hover:border-[#A8B7A0]'"
                                    >
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-start gap-3 min-w-0">
                                                <span 
                                                    class="mt-0.5 w-4 h-4 rounded-full border flex items-center justify-center shrink-0"
                                                    :class="selectedAddressId === addr.id ? 'border-[#304C3D]' : 'border-[#A8B7A0]'"
                                                    aria-hidden="true"
                                                >
                                                    <span x-show="selectedAddressId === addr.id" class="w-2 h-2 rounded-full bg-[#304C3D]"></span>
                                                </span>

                                                <div class="min-w-0 space-y-1">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="text-sm font-medium text-ink" x-text="addr.label"></span>
                                                        <template x-if="addr.is_default">
                                                            <span class="text-[11px] uppercase tracking-[0.16em] font-medium text-forest bg-white/80 border border-[#304C3D]/30 px-2 py-0.5">
                                                                Default
                                                            </span>
                                                        </template>
                                                    </div>
                                                    <p class="text-sm text-ink" x-text="addr.recipient_name"></p>
                                                    <p class="text-xs text-muted leading-relaxed" x-text="[addr.address, addr.area_name, addr.thana_name, addr.district_name].filter(Boolean).join(', ')"></p>
                                                    <p class="text-xs text-muted lining-nums" x-text="addr.phone"></p>
                                                </div>
                                            </div>

                                            <button 
                                                type="button" 
                                                @click.stop="editSavedAddress(addr)"
                                                class="text-xs uppercase tracking-[0.16em] font-medium text-forest hover:text-ink underline underline-offset-4 shrink-0 py-1 px-2 -mr-1 cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D]"
                                            >
                                                Edit
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <!-- Final Card: Use a different address -->
                                <div 
                                    role="radio"
                                    tabindex="0"
                                    :aria-checked="selectedAddressId === 'new' ? 'true' : 'false'"
                                    @click="selectNewAddress()"
                                    @keydown.space.prevent="selectNewAddress()"
                                    @keydown.enter.prevent="selectNewAddress()"
                                    @keydown.arrow-down.prevent="focusAddressOption(0)"
                                    @keydown.arrow-right.prevent="focusAddressOption(0)"
                                    @keydown.arrow-up.prevent="focusAddressOption(savedAddresses.length - 1)"
                                    @keydown.arrow-left.prevent="focusAddressOption(savedAddresses.length - 1)"
                                    :data-addr-option="savedAddresses.length"
                                    class="w-full min-h-[44px] p-4 border transition-colors cursor-pointer flex items-center gap-3 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#304C3D] focus-visible:ring-offset-1"
                                    :class="selectedAddressId === 'new' ? 'border-[#304C3D] bg-[#E8EEE5]' : 'border-[#D5DBCF] bg-white hover:border-[#A8B7A0]'"
                                >
                                    <span 
                                        class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0"
                                        :class="selectedAddressId === 'new' ? 'border-[#304C3D]' : 'border-[#A8B7A0]'"
                                        aria-hidden="true"
                                    >
                                        <span x-show="selectedAddressId === 'new'" class="w-2 h-2 rounded-full bg-[#304C3D]"></span>
                                    </span>
                                    <span class="text-sm font-medium text-ink">Use a different address</span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- ADDRESS FORM (Always shown if guest/no saved addresses; collapsible when saved addresses exist) -->
                    <div x-show="savedAddresses.length === 0 || showAddressForm" x-collapse class="space-y-4 pt-1">
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
                                    class="input-field !h-12 !min-h-[48px] text-base"
                                    :class="errors.firstName ? 'input-error !border-[#B3453A]' : ''"
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
                                    class="input-field !h-12 !min-h-[48px] text-base"
                                    :class="errors.lastName ? 'input-error !border-[#B3453A]' : ''"
                                >
                                <p id="last_name-error" x-show="errors.lastName" x-cloak class="mt-1 text-[13px] text-[#B3453A] font-medium" x-text="errors.lastName" role="alert"></p>
                            </div>
                        </div>

                        <!-- Country (White select style matching District / Thana / Area) -->
                        <div>
                            <label for="country" class="block text-xs uppercase tracking-[0.2em] text-muted mb-1.5 font-medium">
                                Country / Region *
                            </label>
                            <div class="relative">
                                <select 
                                    id="country" 
                                    name="country" 
                                    x-model="form.country" 
                                    class="input-field !h-12 !min-h-[48px] text-base pr-10 appearance-none cursor-pointer bg-white"
                                >
                                    <option value="Bangladesh">Bangladesh</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-forest">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
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
                                        class="input-field !h-12 !min-h-[48px] text-base pr-10 appearance-none cursor-pointer bg-white"
                                        :class="errors.district_id ? 'input-error !border-[#B3453A]' : ''"
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
                                        class="input-field !h-12 !min-h-[48px] text-base pr-10 appearance-none cursor-pointer bg-white disabled:bg-ivory/60 disabled:cursor-not-allowed"
                                        :class="errors.thana_id ? 'input-error !border-[#B3453A]' : ''"
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
                                        class="input-field !h-12 !min-h-[48px] text-base pr-10 appearance-none cursor-pointer bg-white disabled:bg-ivory/60 disabled:cursor-not-allowed"
                                        :class="errors.area_id ? 'input-error !border-[#B3453A]' : ''"
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

                        <!-- Address Details -->
                        <div>
                            <label for="address" class="block text-xs uppercase tracking-[0.2em] text-muted mb-1.5 font-medium">
                                Address Details *
                            </label>
                            <textarea 
                                id="address" 
                                name="address" 
                                rows="3" 
                                autocomplete="street-address" 
                                x-model="form.address" 
                                @input="clearError('address')"
                                :aria-invalid="errors.address ? 'true' : 'false'"
                                :aria-describedby="errors.address ? 'address-error' : null"
                                class="input-field !h-auto py-3 text-base resize-y"
                                :class="errors.address ? 'input-error !border-[#B3453A]' : ''"
                            ></textarea>
                            <p id="address-error" x-show="errors.address" x-cloak class="mt-1 text-[13px] text-[#B3453A] font-medium" x-text="errors.address" role="alert"></p>
                        </div>

                        <!-- Save Address Checkbox (Shown when "Use a different address" is selected) -->
                        <template x-if="selectedAddressId === 'new'">
                            <label class="inline-flex items-center gap-2.5 pt-1 cursor-pointer select-none min-h-[44px]">
                                <input 
                                    type="checkbox" 
                                    x-model="form.saveAddress" 
                                    class="w-4 h-4 accent-[#304C3D] cursor-pointer"
                                >
                                <span class="text-sm text-ink">Save this address to my account (demo)</span>
                            </label>
                        </template>
                    </div>
                </section>

                <!-- SECTION 3: DELIVERY / SHIPPING METHOD RADIOS -->
                <section class="px-6 sm:px-8 pt-8 pb-6 space-y-4" aria-labelledby="section-delivery-heading">
                    <div>
                        <h2 id="section-delivery-heading" class="font-serif text-lg sm:text-xl text-ink font-medium">
                            3. Delivery Method
                        </h2>
                        <p class="text-xs text-muted mt-0.5">
                            Free standard delivery on orders {{ \App\Support\Currency::format($freeShippingThreshold) }} or more.
                        </p>
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
                                <span 
                                    class="price lining-nums text-sm font-medium text-ink" 
                                    x-text="getShippingRatePrice('{{ $rateKey }}')"
                                ></span>
                            </label>
                        @endforeach
                    </div>
                </section>

                <!-- SECTION 4: PAYMENT METHOD (DEMO ONLY - NO CARD INPUTS) -->
                <section class="px-6 sm:px-8 pt-8 pb-8 space-y-5" aria-labelledby="section-payment-heading">
                    <div>
                        <h2 id="section-payment-heading" class="font-serif text-lg sm:text-xl text-ink font-medium">
                            4. Payment Method (Demo)
                        </h2>
                        <p class="text-xs text-muted mt-0.5">Select a demonstration payment option below.</p>
                    </div>

                    <div class="space-y-3" role="radiogroup" aria-label="Payment Method">
                        <label 
                            class="relative flex items-center justify-between p-4 rounded-none cursor-pointer transition-colors min-h-[48px] border"
                            :class="paymentMethod === 'cod_demo' ? 'border-[#304C3D] bg-[#E8EEE5]' : 'border-[#D5DBCF] bg-white hover:border-[#A8B7A0]'"
                        >
                            <div class="flex items-center gap-3">
                                <input 
                                    type="radio" 
                                    name="payment_method" 
                                    value="cod_demo" 
                                    x-model="paymentMethod" 
                                    class="w-4 h-4 text-forest accent-[#304C3D] cursor-pointer"
                                >
                                <div>
                                    <span class="block text-sm font-medium text-ink">Cash on Delivery</span>
                                    <span class="block text-xs text-muted mt-0.5">Pay in cash when your order arrives. (Demo)</span>
                                </div>
                            </div>
                        </label>

                        <label 
                            class="relative flex items-center justify-between p-4 rounded-none cursor-pointer transition-colors min-h-[48px] border"
                            :class="paymentMethod === 'bkash_demo' ? 'border-[#304C3D] bg-[#E8EEE5]' : 'border-[#D5DBCF] bg-white hover:border-[#A8B7A0]'"
                        >
                            <div class="flex items-center gap-3">
                                <input 
                                    type="radio" 
                                    name="payment_method" 
                                    value="bkash_demo" 
                                    x-model="paymentMethod" 
                                    class="w-4 h-4 text-forest accent-[#304C3D] cursor-pointer"
                                >
                                <div>
                                    <span class="block text-sm font-medium text-ink">bKash</span>
                                    <span class="block text-xs text-muted mt-0.5">Pay with your bKash mobile wallet. (Demo)</span>
                                </div>
                            </div>
                        </label>

                        <label 
                            class="relative flex items-center justify-between p-4 rounded-none cursor-pointer transition-colors min-h-[48px] border"
                            :class="paymentMethod === 'nagad_demo' ? 'border-[#304C3D] bg-[#E8EEE5]' : 'border-[#D5DBCF] bg-white hover:border-[#A8B7A0]'"
                        >
                            <div class="flex items-center gap-3">
                                <input 
                                    type="radio" 
                                    name="payment_method" 
                                    value="nagad_demo" 
                                    x-model="paymentMethod" 
                                    class="w-4 h-4 text-forest accent-[#304C3D] cursor-pointer"
                                >
                                <div>
                                    <span class="block text-sm font-medium text-ink">Nagad</span>
                                    <span class="block text-xs text-muted mt-0.5">Pay with your Nagad mobile wallet. (Demo)</span>
                                </div>
                            </div>
                        </label>

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
                                    <span class="block text-sm font-medium text-ink">Card</span>
                                    <span class="block text-xs text-muted mt-0.5">Pay with Visa, Mastercard, or local bank card. (Demo)</span>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="pt-2 space-y-2.5">
                        <p class="text-xs text-muted text-center">
                            This is a demo. No payment is taken and no order is placed.
                        </p>
                        <x-storefront.button 
                            type="button" 
                            @click="submitDemoOrder()" 
                            variant="primary" 
                            size="lg" 
                            class="w-full !min-h-[48px]"
                        >
                            Place demo order
                        </x-storefront.button>
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
                                    <div class="relative w-16 h-16 shrink-0 overflow-visible bg-white border border-[#E3E6DD]">
                                        <x-storefront.image 
                                            ::src="item.image ? ((item.image.startsWith('http') || item.image.startsWith('/')) ? item.image : '/images/' + item.image) : ''" 
                                            ::alt="item.name || 'Botanical product'" 
                                            aspect-ratio="aspect-square" 
                                            class="w-full h-full object-cover" 
                                        />
                                        <span 
                                            class="price lining-nums absolute -top-2 -right-2 w-5 h-5 bg-forest text-ivory text-xs font-medium rounded-full flex items-center justify-center pointer-events-none z-10" 
                                            x-text="item.qty || item.quantity"
                                        ></span>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-serif text-sm font-medium text-ink truncate leading-snug" x-text="item.name"></h3>
                                        <p class="text-xs text-muted mt-0.5 truncate" x-show="item.size || (item.variant && item.variant.size)" x-text="item.size || (item.variant ? item.variant.size : '')"></p>
                                    </div>

                                    <div class="shrink-0 text-right pl-3">
                                        <span class="price lining-nums text-sm font-medium text-ink" x-text="formatMoney(item.lineTotal !== undefined ? item.lineTotal : (item.price * item.qty))"></span>
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
                        <label for="desktop-discount-code" class="block text-xs uppercase tracking-[0.2em] text-muted font-medium">
                            Discount code
                        </label>
                        <div class="flex gap-2 items-center">
                            <input 
                                id="desktop-discount-code"
                                type="text" 
                                placeholder="Enter code"
                                x-model="discountInput" 
                                class="input-field !h-12 !min-h-[48px] text-base flex-1"
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
                        <template x-if="$store.cart.discountError">
                            <p class="mt-1 text-[13px] text-[#B3453A] font-medium" x-text="$store.cart.discountError" role="alert"></p>
                        </template>
                        <template x-if="$store.cart.discountCode">
                            <div class="flex items-center justify-between text-xs bg-sage-light/60 px-3 py-2 border border-[#E3E6DD] text-forest">
                                <span class="font-medium price" x-text="'Applied: ' + $store.cart.discountCode + ($store.cart.discount ? ' (' + $store.cart.discount.label + ')' : '')"></span>
                                <button type="button" @click="$store.cart.removeDiscount()" class="text-xs uppercase tracking-wider hover:underline text-ink font-semibold ml-2 cursor-pointer">
                                    Remove
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Subtotal, Discount, Shipping, Total -->
                    <div class="pt-4 border-t border-[#ECEEE6] space-y-2.5 text-sm">
                        <div class="flex justify-between text-muted">
                            <span>Subtotal</span>
                            <span class="price lining-nums font-medium text-ink" x-text="formatMoney($store.cart.subtotal || 0)"></span>
                        </div>

                        <div x-show="$store.cart.discountAmount > 0" x-cloak class="flex justify-between text-forest font-medium">
                            <span>Discount (<span x-text="$store.cart.discountCode"></span>)</span>
                            <span class="price lining-nums" x-text="'-' + formatMoney($store.cart.discountAmount || 0)"></span>
                        </div>

                        <div class="flex justify-between text-muted">
                            <span x-text="'Shipping (' + (selectedShippingRate ? selectedShippingRate.name : 'Standard Delivery') + ')'"></span>
                            <span :class="calculatedShippingCost === 0 ? 'text-forest font-medium' : 'price lining-nums text-ink font-medium'" 
                                  x-text="calculatedShippingCost === 0 ? 'Complimentary' : formatMoney(calculatedShippingCost)"></span>
                        </div>

                        <div class="border-t border-[#ECEEE6] pt-3.5 flex justify-between text-lg font-medium text-ink">
                            <span class="font-serif">Total Due</span>
                            <span class="price lining-nums text-lg" x-text="formatMoney(calculatedTotal)"></span>
                        </div>
                    </div>

                </div>
            </aside>

        </div>
    </div>
</div>

<script>
function checkoutComponent() {
    const savedAddresses = @js($savedAddresses ?? []);
    const defaultAddr = savedAddresses.find(a => a.is_default) || savedAddresses[0] || null;

    return {
        orderPlaced: false,
        submitting: false,
        showMobileSummary: false,
        discountInput: '',
        shippingMethod: 'standard',
        paymentMethod: 'cod_demo',
        shippingRates: @js($shippingRates),
        savedAddresses: savedAddresses,
        selectedAddressId: defaultAddr ? defaultAddr.id : 'new',
        showAddressForm: savedAddresses.length === 0,
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
            'addressLine2' => '',
            'saveAddress' => false,
        ]),
        errors: {},
        fieldIdMap: {
            phone: 'phone',
            email: 'email',
            firstName: 'first_name',
            lastName: 'last_name',
            district_id: 'district_id',
            thana_id: 'thana_id',
            area_id: 'area_id',
            address: 'address',
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

        formatMoney(amount) {
            if (typeof window.formatCurrency === 'function') {
                return window.formatCurrency(amount);
            }
            return '৳' + Math.round(Number(amount) || 0).toLocaleString('en-US');
        },

        focusAddressOption(targetIdx) {
            const total = this.savedAddresses.length + 1;
            const wrapped = ((targetIdx % total) + total) % total;
            const el = document.querySelector(`[data-addr-option="${wrapped}"]`);
            if (el) {
                el.focus();
                if (wrapped < this.savedAddresses.length) {
                    this.selectSavedAddress(this.savedAddresses[wrapped].id);
                } else {
                    this.selectNewAddress();
                }
            }
        },

        async applySavedAddressFields(addr) {
            this.form.firstName = addr.first_name || '';
            this.form.lastName = addr.last_name || '';
            this.form.phone = addr.phone || this.form.phone || '';
            this.form.country = addr.country || 'Bangladesh';
            this.form.district_id = addr.district_id ? String(addr.district_id) : '';
            this.form.thana_id = '';
            this.form.area_id = '';
            this.form.address = addr.address || '';
            this.form.addressLine2 = addr.address_line2 || '';
            this.form.saveAddress = false;
            this.errors = {};

            if (this.form.district_id) {
                await this.loadThanas(this.form.district_id);
                this.form.thana_id = addr.thana_id ? String(addr.thana_id) : '';
                if (this.form.thana_id) {
                    await this.loadAreas(this.form.thana_id);
                    this.form.area_id = addr.area_id ? String(addr.area_id) : '';
                }
            } else {
                this.thanas = [];
                this.areas = [];
            }
        },

        async selectSavedAddress(id) {
            this.selectedAddressId = id;
            this.showAddressForm = false;
            const addr = this.savedAddresses.find(a => a.id === id);
            if (addr) {
                await this.applySavedAddressFields(addr);
            }
        },

        async editSavedAddress(addr) {
            this.selectedAddressId = addr.id;
            await this.applySavedAddressFields(addr);
            this.showAddressForm = true;
        },

        selectNewAddress() {
            this.selectedAddressId = 'new';
            this.showAddressForm = true;
            this.form.firstName = '';
            this.form.lastName = '';
            this.form.country = 'Bangladesh';
            this.form.district_id = '';
            this.form.thana_id = '';
            this.form.area_id = '';
            this.form.address = '';
            this.form.addressLine2 = '';
            this.form.saveAddress = false;
            this.thanas = [];
            this.areas = [];
            this.errors = {};
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

        get selectedShippingRate() {
            return this.shippingRates[this.shippingMethod] || this.shippingRates['standard'] || null;
        },

        get calculatedShippingCost() {
            const rate = this.selectedShippingRate;
            if (!rate || !this.$store || !this.$store.cart) return 0;
            const subtotal = this.$store.cart.subtotal || 0;
            const discount = this.$store.cart.discount;
            if (discount && discount.type === 'shipping') return 0;
            if (rate.free_threshold > 0 && subtotal >= rate.free_threshold) return 0;
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
            if (rate.free_threshold > 0 && subtotal >= rate.free_threshold) {
                return 'Complimentary';
            }
            return this.formatMoney(parseFloat(rate.cost) || 0);
        },

        async applyDiscountCode() {
            const raw = (this.discountInput || '').trim();
            if (!raw) return;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            try {
                const res = await fetch(@js(route('checkout.validateCoupon')), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({
                        code: raw,
                        subtotal: this.$store.cart.subtotal,
                    }),
                });
                const data = await res.json();
                if (res.ok && data.valid) {
                    this.$store.cart.discountCode = data.code;
                    this.$store.cart.discountError = '';
                    this.$store.cart.discountSuccess = `Code ${data.code} applied (${data.label})`;
                    try { localStorage.setItem('everbloom_discount', data.code); } catch (e) {}
                    this.$store.cart.refreshQuote();
                    this.discountInput = '';
                } else {
                    this.$store.cart.discountError = data.message || 'Invalid promotional code.';
                    this.$store.cart.discountSuccess = '';
                }
            } catch (e) {
                const success = this.$store.cart.applyDiscount(raw);
                if (success) this.discountInput = '';
            }
        },

        clearError(field) {
            if (this.errors[field]) {
                delete this.errors[field];
            }
        },

        validate() {
            const errs = {};
            const phoneDigits = (this.form.phone || '').replace(/\D/g, '');
            if (!this.form.phone || !this.form.phone.trim()) {
                errs.phone = 'Please enter your phone number.';
            } else if (!/^01\d{9}$/.test(phoneDigits) && (phoneDigits.length < 7 || phoneDigits.length > 15)) {
                errs.phone = 'Please enter a valid phone number (01XXXXXXXXX).';
            }

            if (this.form.email && this.form.email.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email.trim())) {
                errs.email = 'Please enter a valid email address.';
            }

            if (!this.form.firstName || !this.form.firstName.trim()) {
                errs.firstName = 'Please enter your first name.';
            }

            if (!this.form.lastName || !this.form.lastName.trim()) {
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

            if (!this.form.address || !this.form.address.trim()) {
                errs.address = 'Please enter your address details.';
            }

            this.errors = errs;
            return Object.keys(errs).length === 0;
        },

        async submitDemoOrder() {
            if (this.submitting) return;
            if (!this.validate()) {
                if (this.savedAddresses.length > 0 && !this.showAddressForm) {
                    const addrFields = ['firstName', 'lastName', 'district_id', 'thana_id', 'area_id', 'address'];
                    if (addrFields.some(f => this.errors[f])) {
                        this.showAddressForm = true;
                    }
                }
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

            this.submitting = true;
            this.errors = {};
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            try {
                const res = await fetch(@js(route('checkout.store')), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({
                        email: this.form.email || null,
                        phone: this.form.phone,
                        firstName: this.form.firstName,
                        lastName: this.form.lastName,
                        district_id: Number(this.form.district_id),
                        thana_id: Number(this.form.thana_id),
                        area_id: Number(this.form.area_id),
                        address: this.form.address,
                        addressLine2: this.form.addressLine2 || null,
                        saveAddress: Boolean(this.form.saveAddress),
                        shippingMethod: this.shippingMethod,
                        paymentMethod: this.paymentMethod,
                        discountCode: this.$store.cart.discountCode || null,
                        items: this.$store.cart.items.map(i => ({
                            productId: i.productId,
                            variantId: i.variantId,
                            qty: i.qty,
                        })),
                    }),
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    this.$store.cart.clear();
                    this.$store.cart.removeDiscount();
                    window.location.href = data.redirect_url || '/';
                    return;
                }

                if (res.status === 422 && data.errors) {
                    const mapped = {};
                    Object.keys(data.errors).forEach(k => {
                        mapped[k] = Array.isArray(data.errors[k]) ? data.errors[k][0] : data.errors[k];
                    });
                    this.errors = mapped;
                } else {
                    this.errors = { items: data.message || 'Unable to place your order. Please try again.' };
                }
            } catch (e) {
                this.errors = { items: 'Network error while placing your order. Please try again.' };
            } finally {
                this.submitting = false;
            }
        }
    };
}
</script>
@endsection
