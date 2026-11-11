@props([
    'items' => config('demo.navigation.main', []),
    'categories' => config('demo.navigation.categories', []),
])

<!-- Mobile Menu Drawer (Global Mount) -->
<div 
    x-data="mobileNavigation" 
    @toggle-mobile-menu.window="toggle()"
    @close-mobile-menu.window="close()"
    @keydown.escape.window="if(open) close()"
    class="relative z-50 lg:hidden"
    x-show="open"
    x-cloak
    role="dialog"
    aria-modal="true"
    aria-label="Mobile Navigation"
>
    <!-- Backdrop Overlay -->
    <div 
        x-show="open" 
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="close()" 
        class="fixed inset-0 bg-ink/50 backdrop-blur-xs"
        aria-hidden="true"
    ></div>

    <!-- Sliding Drawer Panel -->
    <div 
        x-show="open" 
        x-trap.noscroll="open"
        x-transition:enter="transition ease-in-out duration-300 transform"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-300 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 max-w-xs w-full bg-ivory shadow-drawer flex flex-col justify-between overflow-y-auto"
    >
        <!-- Header in drawer -->
        <div class="p-5 border-b border-beige/80 flex items-center justify-between bg-ivory sticky top-0 z-10">
            <div>
                <span class="font-serif text-lg tracking-wide text-forest font-medium block">
                    THE EVERBLOOM
                </span>
                <span class="text-[9px] uppercase tracking-[0.25em] text-muted block -mt-0.5">
                    Botanical Skincare
                </span>
            </div>
            <button 
                type="button" 
                @click="close()" 
                class="p-2 text-ink/70 hover:text-ink focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-forest cursor-pointer rounded"
                aria-label="Close menu"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Account & Wishlist moved into mobile drawer -->
        <div class="px-5 pt-4 pb-2 border-b border-beige/60 bg-sage-light/20">
            <div class="grid grid-cols-2 gap-2">
                <!-- My Account Link -->
                <a 
                    href="/account" 
                    @click="close()"
                    class="flex items-center gap-2 p-2.5 rounded bg-ivory border border-beige/80 hover:border-forest text-ink hover:text-forest transition-colors text-xs font-medium"
                >
                    <svg class="w-4 h-4 text-forest shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Account</span>
                </a>

                <!-- Saved Wishlist Link with Live Counter -->
                <a 
                    href="/wishlist" 
                    @click="close()"
                    class="flex items-center justify-between p-2.5 rounded bg-ivory border border-beige/80 hover:border-forest text-ink hover:text-forest transition-colors text-xs font-medium"
                >
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-forest shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span>Wishlist</span>
                    </span>
                    <span 
                        x-show="$store.wishlist.count > 0" 
                        x-text="$store.wishlist.count" 
                        x-cloak 
                        class="text-[10px] px-1.5 py-0.5 bg-forest text-ivory rounded-full leading-none font-semibold"
                    ></span>
                </a>
            </div>
            <div class="pt-2 pb-1 text-right">
                <a href="/account/login" @click="close()" class="text-[11px] text-muted hover:text-forest underline transition-colors">Sign out</a>
            </div>
        </div>

        <!-- Quick Search Trigger inside Mobile Drawer -->
        <div class="px-5 pt-3">
            <button 
                type="button" 
                @click="close(); $dispatch('open-search')"
                class="w-full flex items-center gap-2 px-3.5 py-2.5 bg-sage-light/50 border border-beige/80 text-xs text-muted hover:text-ink transition-colors text-left rounded"
            >
                <svg class="w-4 h-4 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span>Search botanicals...</span>
            </button>
        </div>

        <!-- Links List -->
        <div class="px-5 py-5 flex-1 space-y-6">
            <div>
                <p class="text-[10px] uppercase tracking-[0.25em] text-muted font-medium mb-3">
                    Botanical Collections
                </p>
                <div class="flex flex-col space-y-2">
                    <a 
                        href="/shop" 
                        @click="close()"
                        class="text-sm font-sans tracking-wider uppercase font-semibold text-forest hover:text-ink py-1 flex items-center justify-between"
                    >
                        <span>All Botanical Products</span>
                        <span class="text-xs">→</span>
                    </a>
                    @foreach($categories as $category)
                        <a 
                            href="{{ $category['url'] }}" 
                            @click="close()"
                            class="text-sm font-sans text-ink hover:text-forest py-1 flex items-center justify-between"
                        >
                            <span>{{ $category['label'] }}</span>
                            <span class="text-xs text-muted">→</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="pt-5 border-t border-beige/60">
                <p class="text-[10px] uppercase tracking-[0.25em] text-muted font-medium mb-3">
                    The House & Client Care
                </p>
                <div class="flex flex-col space-y-2">
                    <a href="/about" @click="close()" class="text-sm font-sans text-ink hover:text-forest py-1">Our Botanical Story</a>
                    <a href="/faq" @click="close()" class="text-sm font-sans text-ink hover:text-forest py-1">Apothecary FAQs</a>
                    <a href="/shipping-and-returns" @click="close()" class="text-sm font-sans text-ink hover:text-forest py-1">Shipping & Botanical Care</a>
                    <a href="/contact" @click="close()" class="text-sm font-sans text-ink hover:text-forest py-1">Concierge & Contact</a>
                </div>
            </div>
        </div>

        <!-- Footer in drawer -->
        <div class="p-5 bg-sage-light/40 border-t border-beige text-xs text-muted space-y-2">
            <div class="flex items-center justify-between">
                <span class="uppercase tracking-widest text-[10px]">Currency</span>
                <span class="font-medium text-ink">{{ config('demo.site.currency.code') }} ({{ config('demo.site.currency.symbol') }})</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="uppercase tracking-widest text-[10px]">Concierge</span>
                <a href="tel:{{ config('demo.site.contact.phone') }}" class="text-ink font-medium hover:text-forest">{{ config('demo.site.contact.phone') }}</a>
            </div>
        </div>
    </div>
</div>
