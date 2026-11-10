@props([
    'items' => config('demo.navigation.main', []),
    'categories' => config('demo.navigation.categories', []),
])

<!-- Mobile Menu Drawer (Controlled via $dispatch('toggle-mobile-menu')) -->
<div 
    x-data="{ open: false }" 
    @toggle-mobile-menu.window="open = !open; open ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')"
    @keydown.escape.window="if(open) { open = false; document.body.classList.remove('overflow-hidden'); }"
    class="lg:hidden"
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
        @click="open = false; document.body.classList.remove('overflow-hidden')" 
        class="fixed inset-0 bg-ink/40 backdrop-blur-xs z-50"
        x-cloak
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
        class="fixed inset-y-0 left-0 max-w-xs w-full bg-ivory shadow-drawer z-50 flex flex-col justify-between overflow-y-auto"
        role="dialog"
        aria-modal="true"
        aria-label="Mobile Navigation"
        x-cloak
    >
        <!-- Header in drawer -->
        <div class="p-6 border-b border-beige flex items-center justify-between">
            <span class="font-serif text-lg tracking-wide text-forest font-medium">
                THE EVERBLOOM
            </span>
            <button 
                type="button" 
                @click="open = false; document.body.classList.remove('overflow-hidden')" 
                class="p-2 text-ink/70 hover:text-ink focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-forest cursor-pointer"
                aria-label="Close menu"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Links List -->
        <div class="px-6 py-6 flex-1 space-y-6">
            <div>
                <p class="text-[10px] uppercase tracking-[0.25em] text-muted font-medium mb-3">
                    Collections
                </p>
                <div class="flex flex-col space-y-3">
                    <a href="/shop" class="text-sm font-sans tracking-wider uppercase font-medium text-forest hover:text-ink py-1">
                        All Botanical Products
                    </a>
                    @foreach($categories as $category)
                        <a href="{{ $category['url'] }}" class="text-sm font-sans text-ink hover:text-forest py-1 flex items-center justify-between">
                            <span>{{ $category['label'] }}</span>
                            <span class="text-xs text-muted">→</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="pt-6 border-t border-beige/60">
                <p class="text-[10px] uppercase tracking-[0.25em] text-muted font-medium mb-3">
                    The House
                </p>
                <div class="flex flex-col space-y-3">
                    <a href="/about" class="text-sm font-sans text-ink hover:text-forest py-1">Our Botanical Story</a>
                    <a href="/styleguide" class="text-sm font-sans text-ink hover:text-forest py-1">Design System & Tokens</a>
                    <a href="/faq" class="text-sm font-sans text-ink hover:text-forest py-1">Apothecary FAQs</a>
                    <a href="/contact" class="text-sm font-sans text-ink hover:text-forest py-1">Concierge & Contact</a>
                </div>
            </div>
        </div>

        <!-- Footer in drawer -->
        <div class="p-6 bg-sage-light/40 border-t border-beige text-xs text-muted space-y-3">
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
