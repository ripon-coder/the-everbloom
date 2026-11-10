@extends('layouts.checkout')

@section('title', 'Demo Checkout — The Everbloom')

@section('content')
<div class="site-container" x-data="{
    submitted: false,
    firstName: '',
    lastName: '',
    email: '',
    address: '',
    city: '',
    state: '',
    zip: '',
    submitDemoOrder() {
        this.submitted = true;
    }
}">
    <!-- Notice Banner -->
    <div class="mb-8 p-4 bg-sage-light/70 border border-forest/20 text-xs sm:text-sm text-forest font-medium flex items-center gap-3">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>
            <strong>Demonstration Mode:</strong> This is a front-end checkout flow simulation. No monetary transactions or real orders are processed.
        </span>
    </div>

    <!-- Feedback Notice After Order Simulation -->
    <div x-show="submitted" x-cloak class="mb-8 p-6 bg-white border-2 border-forest text-center space-y-3">
        <div class="w-12 h-12 rounded-full bg-forest text-ivory flex items-center justify-center mx-auto">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h2 class="font-serif text-2xl text-ink font-normal">Demo Checkout Complete</h2>
        <p class="text-sm text-muted max-w-md mx-auto">
            Demo checkout: no payment was processed. Your selections remain safely stored in your local session.
        </p>
        <div class="pt-2">
            <x-storefront.button href="/shop" variant="secondary" size="sm">
                Return to Shop
            </x-storefront.button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14" x-show="!submitted">
        <!-- Form Fields -->
        <div class="lg:col-span-7 space-y-8">
            <!-- Contact -->
            <div class="bg-white p-6 border border-beige space-y-4">
                <h2 class="font-serif text-lg text-ink font-medium">1. Contact Information</h2>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-muted mb-1 font-medium">Email Address</label>
                    <input type="email" x-model="email" placeholder="you@domain.com" class="w-full px-3.5 py-2.5 text-sm border border-beige bg-ivory focus:outline-none focus:ring-1 focus:ring-forest">
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white p-6 border border-beige space-y-4">
                <h2 class="font-serif text-lg text-ink font-medium">2. Shipping Destination</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-muted mb-1 font-medium">First Name</label>
                        <input type="text" x-model="firstName" class="w-full px-3.5 py-2.5 text-sm border border-beige bg-ivory focus:outline-none focus:ring-1 focus:ring-forest">
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-muted mb-1 font-medium">Last Name</label>
                        <input type="text" x-model="lastName" class="w-full px-3.5 py-2.5 text-sm border border-beige bg-ivory focus:outline-none focus:ring-1 focus:ring-forest">
                    </div>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-muted mb-1 font-medium">Street Address</label>
                    <input type="text" x-model="address" placeholder="123 Botanical Ave" class="w-full px-3.5 py-2.5 text-sm border border-beige bg-ivory focus:outline-none focus:ring-1 focus:ring-forest">
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-muted mb-1 font-medium">City</label>
                        <input type="text" x-model="city" class="w-full px-3.5 py-2.5 text-sm border border-beige bg-ivory focus:outline-none focus:ring-1 focus:ring-forest">
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-muted mb-1 font-medium">State</label>
                        <input type="text" x-model="state" class="w-full px-3.5 py-2.5 text-sm border border-beige bg-ivory focus:outline-none focus:ring-1 focus:ring-forest">
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-muted mb-1 font-medium">Postal Code</label>
                        <input type="text" x-model="zip" class="w-full px-3.5 py-2.5 text-sm border border-beige bg-ivory focus:outline-none focus:ring-1 focus:ring-forest">
                    </div>
                </div>
            </div>

            <!-- Payment Simulation -->
            <div class="bg-white p-6 border border-beige space-y-4">
                <h2 class="font-serif text-lg text-ink font-medium">3. Payment Method (Demo Only)</h2>
                <div class="p-4 bg-sage-light/30 border border-beige text-xs text-muted leading-relaxed">
                    This turnkey site frontend simulates payment tokenization. No credit card information is required or saved.
                </div>
                <button type="button" @click="submitDemoOrder" class="btn-primary w-full !py-3.5">
                    Place Demo Order (No Payment Processed)
                </button>
            </div>
        </div>

        <!-- Sidebar Summary -->
        <div class="lg:col-span-5">
            <div class="bg-white p-6 border border-beige space-y-6 sticky top-28">
                <h2 class="font-serif text-lg text-ink font-medium border-b border-beige pb-3">Bag Review</h2>
                
                <div class="divide-y divide-beige/60 max-h-64 overflow-y-auto">
                    <template x-for="item in $store.cart.items" :key="item.key">
                        <div class="py-3 flex items-center justify-between text-xs">
                            <span class="font-medium text-ink truncate max-w-[180px]" x-text="item.name + ' × ' + item.quantity"></span>
                            <span class="text-forest font-semibold" x-text="'$' + (item.price * item.quantity).toFixed(2)"></span>
                        </div>
                    </template>
                </div>

                <div class="border-t border-beige pt-4 space-y-2 text-xs">
                    <div class="flex justify-between text-muted">
                        <span>Subtotal</span>
                        <span class="font-medium text-ink" x-text="'$' + $store.cart.subtotal.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-muted">
                        <span>Shipping</span>
                        <span class="font-medium text-forest">Complimentary</span>
                    </div>
                    <div class="border-t border-beige pt-3 flex justify-between font-serif text-base font-semibold text-ink">
                        <span>Total Due</span>
                        <span x-text="'$' + $store.cart.subtotal.toFixed(2)"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
