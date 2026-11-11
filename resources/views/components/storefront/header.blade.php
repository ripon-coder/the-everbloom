<header class="static z-40 bg-[#F8F6F0] border-b border-[#ECEEE6]">
    <div class="site-container">
        <!-- Top Tier: Brand Logo & Actions -->
        <div class="flex items-center justify-between h-16 md:h-20">
            
            <!-- Left: Mobile Hamburger Trigger (Mobile) & Search Bar Trigger (Desktop) -->
            <div class="flex items-center gap-4 flex-1">
                <!-- Mobile Menu Button (lg:hidden, 44x44px touch target) -->
                <button 
                    type="button" 
                    x-data
                    @click="$dispatch('toggle-mobile-menu')"
                    class="lg:hidden w-11 h-11 -ml-2 inline-flex items-center justify-center text-ink hover:text-forest transition-colors cursor-pointer"
                    aria-label="Open mobile navigation menu"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Desktop Search Trigger -->
                <button 
                    type="button" 
                    x-data
                    @click="$dispatch('open-search')"
                    class="hidden lg:inline-flex items-center gap-2 min-h-[44px] text-xs uppercase tracking-[0.2em] text-muted hover:text-forest transition-colors py-1.5 cursor-pointer"
                    aria-label="Search botanical formulations"
                >
                    <svg class="w-4 h-4 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Search</span>
                    <kbd class="px-1.5 py-0.5 text-xs bg-beige/40 border border-[#E3E6DD] font-mono text-muted rounded-sm">⌘K</kbd>
                </button>
            </div>

            <!-- Center: Identical Brand Lockup (THE EVERBLOOM / BOTANICAL SKINCARE) -->
            <div class="shrink-0 text-center px-2 sm:px-4">
                <a href="/" class="group block text-center" aria-label="{{ config('demo.site.name') }} Homepage">
                    <span class="font-serif text-xl sm:text-3xl lg:text-[2rem] font-medium tracking-tight text-ink group-hover:text-forest transition-colors block">
                        THE EVERBLOOM
                    </span>
                    <span class="text-xs uppercase tracking-[0.2em] text-muted -mt-0.5 group-hover:text-ink transition-colors font-sans block">
                        Botanical Skincare
                    </span>
                </a>
            </div>

            <!-- Right: Actions (Mobile: Account only; Desktop: Wishlist, Account, Bag) -->
            <div class="flex items-center justify-end space-x-1 sm:space-x-3 flex-1">
                <!-- Wishlist Link with Live Counter (Desktop Only - in bottom nav on mobile) -->
                <a 
                    href="/wishlist" 
                    x-data
                    class="hidden lg:inline-flex w-11 h-11 items-center justify-center text-ink hover:text-forest rounded-sm transition-colors relative"
                    aria-label="View Saved Wishlist"
                    @if(request()->routeIs('wishlist')) aria-current="page" @endif
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <!-- Wishlist Badge -->
                    <span 
                        x-show="$store.wishlist.count > 0" 
                        x-text="$store.wishlist.count" 
                        x-cloak
                        class="price absolute top-1 right-1 min-w-[18px] h-[18px] px-1 bg-forest text-ivory text-xs font-sans font-medium flex items-center justify-center rounded-full leading-none"
                    ></span>
                </a>

                <!-- Account Icon (Visible on Mobile & Desktop, 44x44px touch target) -->
                <a 
                    href="/account" 
                    class="inline-flex w-11 h-11 -mr-2 lg:mr-0 items-center justify-center text-ink hover:text-forest rounded-sm transition-colors relative"
                    aria-label="My Account"
                    @if(request()->is('account*')) aria-current="page" @endif
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </a>

                <!-- Cart Drawer Trigger Button with Item-Count Badge (Desktop Only: Search and bag live in bottom bar on mobile) -->
                <button 
                    type="button" 
                    x-data
                    @click="$store.cart.openDrawer()"
                    class="hidden lg:inline-flex min-h-[44px] px-2 text-ink hover:text-forest rounded-sm transition-colors relative cursor-pointer items-center gap-2"
                    aria-label="Open Shopping Bag"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="text-xs uppercase tracking-wider font-medium text-ink">
                        Bag (<span class="price" x-text="$store.cart.count">0</span>)
                    </span>
                </button>
            </div>
        </div>

        <!-- Bottom Tier: Centered Navigation (Desktop) -->
        <div class="hidden lg:flex items-center justify-center border-t border-[#ECEEE6] py-3">
            <x-storefront.navigation />
        </div>
    </div>
</header>
