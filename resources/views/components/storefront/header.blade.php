<header class="sticky top-0 z-40 bg-ivory/95 backdrop-blur-md border-b border-beige/60 transition-all duration-200">
    <div class="site-container">
        <!-- Top Tier: Brand Logo & Actions -->
        <div class="flex items-center justify-between h-16 md:h-20">
            
            <!-- Left: Mobile Menu Trigger (Mobile) & Search Bar Trigger (Desktop) -->
            <div class="flex items-center gap-4 flex-1">
                <!-- Mobile Menu Button (lg:hidden) -->
                <button 
                    type="button" 
                    x-data
                    @click="$dispatch('toggle-mobile-menu')"
                    class="lg:hidden p-2 text-ink hover:text-forest focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest cursor-pointer -ml-2"
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
                    class="hidden lg:inline-flex items-center gap-2 text-xs uppercase tracking-[0.18em] text-muted hover:text-forest transition-colors py-1.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest cursor-pointer"
                    aria-label="Search botanical formulations"
                >
                    <svg class="w-4 h-4 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Search</span>
                    <kbd class="px-1.5 py-0.5 text-xs bg-beige/40 border border-beige/80 font-mono text-muted rounded">⌘K</kbd>
                </button>
            </div>

            <!-- Center: Brand Logo (Always Centered & Clear) -->
            <div class="shrink-0 text-center px-4">
                <a href="/" class="group block text-center focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest" aria-label="{{ config('demo.site.name') }} Homepage">
                    <span class="font-serif text-2xl sm:text-3xl lg:text-[2rem] font-medium tracking-tight text-ink group-hover:text-forest transition-colors block">
                        THE EVERBLOOM
                    </span>
                    <span class="text-xs uppercase tracking-[0.22em] text-muted -mt-0.5 group-hover:text-ink transition-colors font-sans block">
                        Botanical Skincare
                    </span>
                </a>
            </div>

            <!-- Right: Actions (Mobile Search, Wishlist, Account, Bag) -->
            <div class="flex items-center justify-end space-x-1 sm:space-x-3 flex-1">
                <!-- Mobile Search Icon Button -->
                <button 
                    type="button" 
                    x-data
                    @click="$dispatch('open-search')"
                    class="lg:hidden p-2 text-ink hover:text-forest rounded-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest cursor-pointer"
                    aria-label="Search botanical products"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>

                <!-- Wishlist Link with Live Counter -->
                <a 
                    href="/wishlist" 
                    x-data
                    class="p-2 text-ink hover:text-forest rounded-sm transition-colors relative focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest"
                    aria-label="View Saved Wishlist"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <!-- Wishlist Badge -->
                    <span 
                        x-show="$store.wishlist.count > 0" 
                        x-text="$store.wishlist.count" 
                        x-cloak
                        class="absolute top-1 right-1 min-w-[18px] h-[18px] px-1 bg-forest text-ivory text-xs font-sans font-medium flex items-center justify-center rounded-full leading-none"
                    ></span>
                </a>

                <!-- Account Icon (Between Wishlist and Bag) -->
                <a 
                    href="/account" 
                    class="p-2 text-ink hover:text-forest rounded-sm transition-colors relative focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest"
                    aria-label="My Account"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </a>

                <!-- Cart Drawer Trigger Button with Live Counter -->
                <button 
                    type="button" 
                    x-data
                    @click="$store.cart.openDrawer()"
                    class="p-2 text-ink hover:text-forest rounded-sm transition-colors relative focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest cursor-pointer flex items-center gap-2"
                    aria-label="Open Shopping Bag"
                >
                    <div class="relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <!-- Cart Count Bubble -->
                        <span 
                            x-show="$store.cart.count > 0" 
                            x-text="$store.cart.count" 
                            x-cloak
                            class="absolute -top-1.5 -right-2 min-w-[18px] h-[18px] px-1 bg-forest text-ivory text-xs font-sans font-medium flex items-center justify-center rounded-full leading-none"
                        ></span>
                    </div>
                    <span class="hidden sm:inline text-xs uppercase tracking-wider font-medium text-ink/90">
                        Bag (<span x-text="$store.cart.count">0</span>)
                    </span>
                </button>
            </div>
        </div>

        <!-- Bottom Tier: Centered Navigation (Desktop) -->
        <div class="hidden lg:flex items-center justify-center border-t border-beige/60 py-3">
            <x-storefront.navigation />
        </div>
    </div>
</header>
