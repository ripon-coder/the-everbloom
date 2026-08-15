<script>
    window.initialCartSession = {!! \Illuminate\Support\Js::from(session('cart', [])) !!};
    window.initialBuyNowSession = {!! \Illuminate\Support\Js::from(session('buy_now_cart', [])) !!};
</script>
<header class="w-full bg-white font-sans border-b border-gray-200 relative z-[9999]" x-data="{ 
        isMobileMenuOpen: false,
        toggleMobileMenu() {
            this.isMobileMenuOpen = !this.isMobileMenuOpen;
            if (window.Alpine && window.Alpine.store('mobileMenu')) {
                window.Alpine.store('mobileMenu').isOpen = this.isMobileMenuOpen;
            }
        },
        closeMobileMenu() {
            this.isMobileMenuOpen = false;
            if (window.Alpine && window.Alpine.store('mobileMenu')) {
                window.Alpine.store('mobileMenu').isOpen = false;
            }
        },
        activeTab: 'categories', 
        isCartOpen: false,
        cart: [],
        wishlistIds: [],
        init() {
            this.loadCart();
            
            // Initial sync to ensure session matches local storage on first load
            this.syncWithServer();

            window.addEventListener('cart-updated', () => {
                this.loadCart();
                this.syncWithServer();
                this.isCartOpen = true; // Open drawer when added
            });
            window.addEventListener('cart-updated-internal', () => {
                this.loadCart();
            });
            window.addEventListener('open-cart-drawer', () => {
                this.loadCart();
                this.isCartOpen = true;
            });

            window.addEventListener('toggle-mobile-menu', () => {
                this.toggleMobileMenu();
            });
            window.addEventListener('close-mobile-menu', () => {
                this.closeMobileMenu();
            });
            window.addEventListener('open-mobile-menu', () => {
                this.isMobileMenuOpen = true;
                if (window.Alpine && window.Alpine.store('mobileMenu')) {
                    window.Alpine.store('mobileMenu').isOpen = true;
                }
            });

            window.addEventListener('wishlist-updated', () => {
                this.loadWishlist();
            });

            @auth
                this.loadWishlist();
            @endauth
        },
        loadWishlist() {
            fetch('/wishlist/ids')
                .then(res => res.json())
                .then(data => {
                    this.wishlistIds = data.wishlist_ids || [];
                })
                .catch(err => console.error('Wishlist load error:', err));
        },
        get wishlistCount() {
            return this.wishlistIds.length;
        },
        loadCart() {
            let localCart = localStorage.getItem('cart');
            let parsed = null;
            
            if (localCart !== null && localCart !== 'undefined') {
                try {
                    parsed = JSON.parse(localCart);
                } catch(e) {
                    parsed = null;
                }
            }

            // Seed localStorage from server session if localStorage cart is empty or missing
            if ((!parsed || !Array.isArray(parsed) || parsed.length === 0) && Array.isArray(window.initialCartSession) && window.initialCartSession.length > 0) {
                parsed = window.initialCartSession;
                try {
                    localStorage.setItem('cart', JSON.stringify(parsed));
                } catch(e) {}
            }

            // Seed buy_now_cart from server session if missing in localStorage
            let localBuyNow = localStorage.getItem('buy_now_cart');
            if (!localBuyNow && Array.isArray(window.initialBuyNowSession) && window.initialBuyNowSession.length > 0) {
                try {
                    localStorage.setItem('buy_now_cart', JSON.stringify(window.initialBuyNowSession));
                } catch(e) {}
            }

            this.cart = Array.isArray(parsed) ? parsed.filter(item => item && parseInt(item.quantity || 0) > 0) : [];
        },
        get cartCount() {
            return this.cart.reduce((total, item) => total + parseInt(item.quantity || 0), 0);
        },
        get cartTotal() {
            return this.cart.reduce((total, item) => {
                let price = parseFloat(item.unit_final_price !== undefined && item.unit_final_price !== null ? item.unit_final_price : (item.unit_base_price !== undefined && item.unit_base_price !== null ? item.unit_base_price : (item.price || 0)));
                return total + (price * parseInt(item.quantity || 0));
            }, 0);
        },
        updateQuantity(index, delta) {
            let currentTotalQty = this.cart.reduce((total, item) => total + parseInt(item.quantity || 0), 0);
            let newQuantity = parseInt(this.cart[index].quantity || 0) + delta;

            if (delta > 0 && currentTotalQty + delta > 30) {
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'You cannot add more than 30 products to your cart.', type: 'error' } }));
                return;
            }

            if (newQuantity > 0) {
                this.cart[index].quantity = newQuantity;
                this.cart[index].line_total = newQuantity * parseFloat(this.cart[index].unit_final_price || 0);
                this.saveCart();
            } else {
                this.removeItem(index);
            }
        },
        removeItem(index) {
            this.cart.splice(index, 1);
            this.saveCart();
        },
        saveCart() {
            this.cart = (this.cart || []).filter(item => item && parseInt(item.quantity || 0) > 0);
            localStorage.setItem('cart', JSON.stringify(this.cart));
            this.syncWithServer();
            window.dispatchEvent(new CustomEvent('cart-updated-internal'));
        },
        syncWithServer() {
            fetch('/cart/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ cart: this.cart, type: 'cart' })
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.cart && Array.isArray(data.cart)) {
                    this.cart = data.cart.filter(item => item && parseInt(item.quantity || 0) > 0);
                    localStorage.setItem('cart', JSON.stringify(this.cart));
                }
            })
            .catch(error => console.error('Error syncing cart:', error));
        },
        formatPrice(price) {
            return parseFloat(price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        searchQuery: '',
        searchResults: [],
        isSearching: false,
        showSearchResults: false,
        async fetchSearchResults() {
            if (this.searchQuery.length < 2) {
                this.searchResults = [];
                this.showSearchResults = false;
                return;
            }

            this.isSearching = true;
            try {
                const response = await fetch(`/search/live?query=${encodeURIComponent(this.searchQuery)}`);
                this.searchResults = await response.json();
                this.showSearchResults = true;
            } catch (error) {
                console.error('Search error:', error);
            } finally {
                this.isSearching = false;
            }
        }
    }"
    @open-cart-drawer.window="loadCart(); if (!window.location.pathname.includes('/checkout')) { if ($store.cartDrawer) $store.cartDrawer.open(); isCartOpen = true; }"
    @cart-updated.window="loadCart(); syncWithServer();">
    <!-- Mobile Header -->
    <div class="md:hidden bg-white text-gray-900 border-b border-gray-200 px-2 sm:px-4 py-2 flex items-center justify-between">
        <button type="button" @click="window.dispatchEvent(new CustomEvent('toggle-mobile-menu', { detail: { tab: 'menu' } }))" class="text-gray-700 hover:text-gray-900 p-1 focus:outline-none cursor-pointer" aria-label="Toggle Navigation Menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
        </button>
        <a href="/" class="flex-shrink-0 flex items-center gap-1">
            @if($site_setting && $site_setting->site_logo)
                <img src="{{ $site_setting->getLogoUrl() }}" alt="{{ $site_setting->site_name }}"
                    class="h-8 object-contain">
            @else
                <span class="text-sm font-bold text-gray-900 uppercase tracking-wider">{{ $site_setting->site_name ?? 'The Everbloom' }}</span>
            @endif
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ Auth::check() ? route('account') : route('login') }}"
                class="text-gray-700 hover:text-gray-900 relative">
                @auth
                    @if(Auth::user()->profile_image)
                        <img src="{{ Auth::user()->profile_image }}" class="w-6 h-6 rounded-full object-cover border border-gray-200">
                    @else
                        <div
                            class="w-6 h-6 rounded-full bg-primary flex items-center justify-center text-[10px] font-bold text-white shadow-xs">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                @else
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                @endauth
            </a>
        </div>
    </div>

    <!-- Mobile Search Bar -->
    <div class="md:hidden px-2 sm:px-4 py-2 bg-white border-b border-gray-100 relative">
        <form action="{{ route('shop') }}" method="GET" class="flex items-center h-9 border border-gray-300 focus-within:border-emerald-600 focus-within:ring-2 focus-within:ring-emerald-500/20 bg-white transition-all">
            <input type="text" name="search" x-model="searchQuery" @input.debounce.300ms="fetchSearchResults()" 
                @focus="showSearchResults = searchResults.length > 0"
                class="flex-1 h-full px-3 text-xs bg-transparent border-none focus:ring-0 outline-none w-full text-gray-800 placeholder-gray-400"
                placeholder="Search for products..." autocomplete="off">
            <button type="submit" class="h-full px-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase transition-colors flex items-center justify-center">
                <svg x-show="!isSearching" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <svg x-show="isSearching" x-cloak class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>

        <!-- Mobile Search Results Dropdown -->
        <div x-show="showSearchResults" @click.away="showSearchResults = false" x-cloak
            class="absolute left-2 right-2 top-full mt-1.5 bg-white shadow-2xl rounded-none z-[100] max-h-[420px] overflow-hidden border border-gray-200">
            <div class="px-3.5 py-2 bg-gray-50 border-b border-gray-100 flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-gray-500">
                <span>Matching Products (<span x-text="searchResults.length" class="text-emerald-600 font-extrabold"></span>)</span>
            </div>
            <div class="overflow-y-auto max-h-[320px] divide-y divide-gray-100">
                <template x-for="product in searchResults" :key="product.id">
                    <a :href="'/product/' + product.slug" class="flex items-center gap-3 p-3 hover:bg-emerald-50/70 transition-colors group">
                        <div class="w-12 h-12 shrink-0 border border-gray-200 bg-white p-0.5 overflow-hidden flex items-center justify-center">
                            <img :src="product.img" :alt="product.name" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold text-gray-900 group-hover:text-emerald-700 truncate" x-text="product.name"></h4>
                            <template x-if="product.category_name">
                                <span class="inline-block text-[9px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200/60 px-1.5 py-0.2" x-text="product.category_name"></span>
                            </template>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs font-black text-emerald-600" x-text="'৳' + formatPrice(product.price)"></span>
                                <span x-show="product.old_price" class="text-[10px] text-gray-400 line-through" x-text="'৳' + formatPrice(product.old_price)"></span>
                            </div>
                        </div>
                    </a>
                </template>
            </div>
            <div x-show="searchResults.length === 0 && searchQuery.length >= 2 && !isSearching" class="p-6 text-center text-gray-500 text-xs bg-gray-50/50">
                No products found for "<span x-text="searchQuery" class="font-bold text-gray-900"></span>"
            </div>
            <a x-show="searchResults.length > 0" :href="'{{ route('shop') }}?search=' + searchQuery" 
                class="block py-2.5 px-3 text-center text-[10px] font-bold text-gray-600 hover:text-emerald-600 bg-gray-50 border-t border-gray-100 transition-colors uppercase tracking-wider">
                View All Results
            </a>
        </div>
    </div>

    <!-- Top Bar (Desktop) -->
    <div class="hidden md:block bg-slate-900 text-white text-xs py-1.5">
        <div class="max-w-[1400px] mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 text-gray-300">
                    @if($site_setting && $site_setting->facebook_url)
                        <a href="{{ $site_setting->facebook_url }}" target="_blank" class="hover:text-white"><svg
                                class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg></a>
                    @endif
                    @if($site_setting && $site_setting->twitter_url)
                        <a href="{{ $site_setting->twitter_url }}" target="_blank" class="hover:text-white"><svg
                                class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                            </svg></a>
                    @endif
                    @if($site_setting && $site_setting->instagram_url)
                        <a href="{{ $site_setting->instagram_url }}" target="_blank" class="hover:text-white"><svg
                                class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                            </svg></a>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="tel:{{ $site_setting->site_phone ?? '+8801720000000' }}"
                    class="hover:text-primary transition-colors flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                        </path>
                    </svg>
                    {{ $site_setting->site_phone ?? '+88 01720 000000' }}
                </a>
                <span class="text-gray-600">|</span>
                <a href="mailto:{{ $site_setting->site_email ?? 'support@feriwalarhat.com' }}"
                    class="hover:text-primary transition-colors flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                    {{ $site_setting->site_email ?? 'support@feriwalarhat.com' }}
                </a>
                <span class="text-gray-600">|</span>
                <a href="{{ route('track-order') }}" class="hover:text-primary transition-colors">Track Order</a>
                <span class="text-gray-600">|</span>
                <a href="{{ route('about') }}" class="hover:text-primary transition-colors">About Us</a>
                <span class="text-gray-600">|</span>
                <a href="{{ route('contact') }}" class="hover:text-primary transition-colors">Contact Us</a>
            </div>
        </div>
    </div>

    <!-- Main Header (Desktop) -->
    <div class="hidden md:block border-b border-gray-100 bg-white">
        <div class="max-w-[1400px] mx-auto px-4 py-4 flex items-center justify-between gap-6">

            <!-- Logo -->
            <a href="/" class="flex-shrink-0 flex items-center gap-1">
                @if($site_setting && $site_setting->site_logo)
                    <img src="{{ $site_setting->getLogoUrl() }}" alt="{{ $site_setting->site_name }}"
                        class="h-11 object-contain">
                @else
                    <span class="text-xl font-extrabold text-slate-900 uppercase tracking-tight">{{ $site_setting->site_name ?? 'THE EVERBLOOM' }}</span>
                @endif
            </a>

            <!-- Search Bar -->
            <div class="flex-1 max-w-3xl relative">
                <form action="{{ route('shop') }}" method="GET"
                    class="flex items-center h-10 border border-gray-300 hover:border-gray-400 focus-within:border-emerald-600 focus-within:ring-2 focus-within:ring-emerald-500/20 bg-white transition-all">
                    <input type="text" name="search" x-model="searchQuery" @input.debounce.300ms="fetchSearchResults()"
                        @focus="showSearchResults = searchResults.length > 0"
                        class="flex-1 h-full px-3.5 text-xs sm:text-sm bg-transparent border-none focus:ring-0 outline-none w-full text-gray-800 placeholder-gray-400"
                        placeholder="Search for products..." autocomplete="off">
                    <button type="submit" class="h-full px-5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wide transition-colors flex items-center justify-center gap-1.5">
                        <svg x-show="!isSearching" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <svg x-show="isSearching" x-cloak class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Search</span>
                    </button>
                </form>

                <!-- Desktop Search Results Dropdown -->
                <div x-show="showSearchResults" @click.away="showSearchResults = false" x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    class="absolute left-0 right-0 top-full mt-1.5 bg-white shadow-2xl rounded-none z-[100] max-h-[500px] overflow-hidden border border-gray-200">
                    
                    <!-- Search Header Bar -->
                    <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between text-xs font-bold uppercase tracking-wider text-gray-500">
                        <span>Matching Products (<span x-text="searchResults.length" class="text-emerald-600 font-extrabold"></span>)</span>
                        <span class="text-gray-400 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Live Results
                        </span>
                    </div>

                    <!-- Search Items -->
                    <div class="overflow-y-auto max-h-[380px] divide-y divide-gray-100">
                        <template x-for="product in searchResults" :key="product.id">
                            <a :href="'/product/' + product.slug" class="flex items-center gap-4 p-3.5 hover:bg-emerald-50/70 transition-colors group">
                                <div class="w-14 h-14 shrink-0 border border-gray-200 bg-white p-0.5 overflow-hidden flex items-center justify-center">
                                    <img :src="product.img" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-xs sm:text-sm font-bold text-gray-900 group-hover:text-emerald-700 transition-colors truncate" x-text="product.name"></h4>
                                    <template x-if="product.category_name">
                                        <span class="inline-block text-[10px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200/60 px-1.5 py-0.5 mt-0.5" x-text="product.category_name"></span>
                                    </template>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs sm:text-sm font-black text-emerald-600" x-text="'৳' + formatPrice(product.price)"></span>
                                        <span x-show="product.old_price" class="text-[11px] text-gray-400 line-through font-normal" x-text="'৳' + formatPrice(product.old_price)"></span>
                                    </div>
                                </div>
                                <div class="text-gray-300 group-hover:text-emerald-600 group-hover:translate-x-1 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
                        </template>
                    </div>

                    <!-- No Results -->
                    <div x-show="searchResults.length === 0 && searchQuery.length >= 2 && !isSearching" class="p-8 text-center bg-gray-50/50">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 text-gray-400 mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-600 text-xs sm:text-sm">No products found matching "<span class="font-bold text-gray-900" x-text="searchQuery"></span>"</p>
                    </div>

                    <!-- Footer Action Bar -->
                    <a x-show="searchResults.length > 0" :href="'{{ route('shop') }}?search=' + searchQuery" 
                        class="block py-2.5 px-4 text-center text-xs font-bold text-gray-600 hover:text-emerald-600 bg-gray-50 border-t border-gray-100 transition-colors uppercase tracking-wider">
                        View All Results for "<span x-text="searchQuery" class="font-extrabold text-gray-900"></span>"
                    </a>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 sm:gap-5 flex-shrink-0">
                <!-- Wishlist -->
                <a href="{{ route('account', 'wishlist') }}"
                    class="hidden lg:flex items-center gap-1.5 h-10 bg-emerald-600 text-white px-3.5 text-xs font-bold hover:bg-emerald-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z">
                        </path>
                    </svg>
                    <span>Wishlist (<span x-text="wishlistCount"></span>)</span>
                </a>

                <div class="hidden lg:block w-px h-6 bg-gray-200"></div>

                <!-- Account -->
                @auth
                    <div class="relative" x-data="{ accountOpen: false }" @mouseenter="accountOpen = true"
                        @mouseleave="accountOpen = false" @click.away="accountOpen = false">
                        <button @click="accountOpen = !accountOpen" class="flex items-center gap-2.5 group text-left">
                            <div class="w-10 h-10 bg-gray-50 border border-gray-200 flex items-center justify-center transition-all overflow-hidden group-hover:border-emerald-600">
                                @if(Auth::user()->profile_image)
                                    <img src="{{ Auth::user()->profile_image }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-emerald-600 font-bold text-xs">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="hidden sm:flex flex-col">
                                <span class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider leading-none mb-1">Welcome back,</span>
                                <span class="text-xs sm:text-sm font-bold text-slate-900 leading-none group-hover:text-emerald-600 transition-colors truncate max-w-[120px]">{{ explode(' ', Auth::user()->name)[0] }}</span>
                            </div>
                        </button>

                        <div x-show="accountOpen" style="display: none;"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            class="absolute right-0 top-full pt-2 w-44 z-50">
                            <div class="bg-white border border-gray-200 shadow-md">
                                <a href="{{ route('account') }}"
                                    class="block px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 hover:text-emerald-600 transition-colors">My Account</a>
                                <a href="{{ route('account', 'orders') }}"
                                    class="block px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 hover:text-emerald-600 transition-colors">My Orders</a>
                                <form method="POST" action="{{ route('logout') }}" class="w-full m-0">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2.5 text-xs font-semibold text-red-600 hover:bg-red-50 border-t border-gray-100 transition-colors">Sign Out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 bg-gray-50 border border-gray-200 flex items-center justify-center group-hover:border-emerald-600 group-hover:bg-emerald-50/50 transition-all">
                            <svg class="w-4.5 h-4.5 text-slate-600 group-hover:text-emerald-600 transition-colors"
                                fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="hidden sm:flex flex-col">
                            <span class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider leading-none mb-1">Sign In</span>
                            <span class="text-xs sm:text-sm font-bold text-slate-900 leading-none group-hover:text-emerald-600 transition-colors">My Account</span>
                        </div>
                    </a>
                @endauth

                <div class="hidden sm:block w-px h-6 bg-gray-200"></div>

                <!-- Cart -->
                <button @click="if (!window.location.pathname.includes('/checkout')) { $store.cartDrawer ? $store.cartDrawer.open() : (isCartOpen = true) }" class="flex items-center gap-2.5 group text-left cursor-pointer">
                    <div class="relative w-10 h-10 bg-gray-50 border border-gray-200 flex items-center justify-center group-hover:border-emerald-600 group-hover:bg-emerald-50/50 transition-all">
                        <svg class="w-4.5 h-4.5 text-slate-700 group-hover:text-emerald-600 transition-colors"
                            fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z">
                            </path>
                        </svg>
                        <span x-show="cartCount > 0" x-text="cartCount"
                            class="absolute -top-1 -right-1 bg-amber-500 text-white text-[9px] font-bold w-4 h-4 flex items-center justify-center border border-white"
                            x-cloak></span>
                    </div>
                    <div class="hidden sm:flex flex-col">
                        <span class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider leading-none mb-1">My Cart</span>
                        <span class="text-xs sm:text-sm font-bold text-slate-900 leading-none group-hover:text-emerald-600 transition-colors"><span x-text="cartCount"></span> Items</span>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation (Desktop) -->
    <div class="hidden md:flex max-w-[1400px] mx-auto px-4 items-center justify-between border-b border-gray-100">

        <div class="flex items-center flex-1">
            <!-- Categories Dropdown -->
            <div class="relative h-full flex items-center mr-6 z-50" x-data="{ catOpen: false }" @click.away="catOpen = false">
                <button @click="catOpen = !catOpen" type="button"
                    class="flex items-center justify-between w-64 bg-slate-50 px-4 py-3 text-slate-800 hover:text-emerald-600 font-medium border-x border-gray-200 transition-colors cursor-pointer">
                    <div class="flex items-center mr-2">
                        <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <span class="text-xs sm:text-sm uppercase tracking-wide font-bold">Categories</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Main Dropdown -->
                <div x-show="catOpen" style="display: none;"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    class="absolute left-0 top-full w-64 bg-white border border-gray-200 shadow-md z-50">
                    <ul class="flex flex-col">
                        @if(isset($header_categories) && $header_categories->count() > 0)
                            @foreach($header_categories as $category)
                                @if($category->children && $category->children->count() > 0)
                                    <li class="relative border-b border-gray-100 last:border-0" x-data="{ hover: false }"
                                        @mouseenter="hover = true" @mouseleave="hover = false">
                                        <a href="{{ route('shop', ['category' => $category->slug]) }}"
                                            class="flex items-center justify-between px-4 py-3 text-[13px] text-slate-800 transition-colors uppercase tracking-wide font-medium"
                                            :class="hover ? 'text-emerald-600 bg-slate-50' : 'hover:text-emerald-600 hover:bg-slate-50'">
                                            <div class="flex items-center gap-2">
                                                {{ $category->name }}
                                            </div>
                                            <svg class="w-3.5 h-3.5 text-gray-400" :class="hover ? 'text-emerald-600' : ''" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>

                                        <!-- Submenu -->
                                        <div x-show="hover" style="display: none;"
                                            class="absolute left-full top-0 w-64 bg-white border border-gray-200 shadow-md z-50 ml-1">
                                            <ul class="py-2 px-1 flex flex-col gap-0.5">
                                                @foreach($category->children as $child)
                                                    <li>
                                                        <a href="{{ route('shop', ['category' => $child->slug]) }}"
                                                            class="block px-4 py-2 text-xs text-slate-800 hover:text-emerald-600 hover:bg-slate-50 uppercase tracking-wide font-medium transition-colors">
                                                            {{ $child->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </li>
                                @else
                                    <li class="relative border-b border-gray-100 last:border-0">
                                        <a href="{{ route('shop', ['category' => $category->slug]) }}"
                                            class="flex items-center justify-between px-4 py-3 text-[13px] text-slate-800 hover:text-emerald-600 hover:bg-slate-50 transition-colors uppercase tracking-wide font-medium">
                                            <div class="flex items-center gap-2">
                                                {{ $category->name }}
                                            </div>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        @else
                            <li class="px-4 py-3 text-xs text-gray-500">No categories found</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Links -->
            <nav class="hidden md:flex items-center flex-wrap">
                <a href="{{ route('home') }}"
                    class="mx-3 text-xs sm:text-sm uppercase tracking-wide py-3 border-b-2 transition-colors {{ request()->routeIs('home') ? 'text-emerald-600 border-emerald-600 font-black' : 'text-slate-700 hover:text-emerald-600 border-transparent font-bold' }}">Home</a>
                @foreach($header_menus as $menu)
                    <a href="{{ $menu->url }}"
                        class="mx-3 text-xs sm:text-sm uppercase tracking-wide py-3 border-b-2 transition-colors {{ request()->url() == url($menu->url) ? 'text-emerald-600 border-emerald-600 font-black' : 'text-slate-700 hover:text-emerald-600 border-transparent font-bold' }}">{{ $menu->name }}</a>
                @endforeach
            </nav>
        </div>
    </div>
</header>
