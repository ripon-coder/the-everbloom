<script>
    window.initialCartSession = @json(session('cart', []));
</script>
<header class="w-full bg-white font-sans border-b border-gray-200" 
    x-data="{ 
        isOpen: false, 
        activeTab: 'categories', 
        isCartOpen: false,
        cart: [],
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
        },
        loadCart() {
            let localCart = localStorage.getItem('cart');
            let sessionCart = window.initialCartSession || [];
            
            if (!localCart) {
                // If local storage was cleared but we have session data, restore it
                this.cart = sessionCart;
                if (this.cart.length > 0) {
                    localStorage.setItem('cart', JSON.stringify(this.cart));
                }
            } else {
                this.cart = JSON.parse(localCart);
            }
        },
        get cartCount() {
            return this.cart.reduce((total, item) => total + parseInt(item.quantity || 0), 0);
        },
        get cartTotal() {
            return this.cart.reduce((total, item) => total + (parseFloat(item.unit_final_price || 0) * parseInt(item.quantity || 0)), 0);
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
            } else if (newQuantity === 0) {
                this.removeItem(index);
            }
        },
        removeItem(index) {
            this.cart.splice(index, 1);
            this.saveCart();
        },
        saveCart() {
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
                body: JSON.stringify({ cart: this.cart })
            }).catch(error => console.error('Error syncing cart:', error));
        },
        formatPrice(price) {
            return parseFloat(price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }">
    <!-- Mobile Header -->
    <div class="md:hidden bg-black text-white px-4 py-3 flex items-center justify-between">
        <button @click="isOpen = true" class="text-gray-300 hover:text-white">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <a href="/" class="flex-shrink-0 flex items-center gap-1">
            <div class="border-[2.5px] border-white text-white px-2 py-0.5 rounded-sm font-black text-xl italic tracking-tighter leading-none">SHEI</div>
            <div class="font-black text-xl tracking-tighter text-white leading-none">TECH</div>
        </a>
        <a href="{{ Auth::check() ? route('account') : route('login') }}" class="text-gray-300 hover:text-white relative">
            @auth
                @if(Auth::user()->profile_image)
                    <img src="{{ Auth::user()->profile_image }}" class="w-7 h-7 rounded-full object-cover border border-white">
                @else
                    <div class="w-7 h-7 rounded-full bg-red-600 flex items-center justify-center text-[11px] font-bold text-white border border-white">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                @endif
            @else
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            @endauth
        </a>
    </div>

    <!-- Mobile Search Bar -->
    <div class="md:hidden px-4 py-3 bg-white border-b border-gray-100">
        <div class="flex items-center h-11 border border-gray-400 rounded-md overflow-hidden">
            <input type="text" class="flex-1 h-full px-3 text-[15px] bg-transparent border-none focus:ring-0 outline-none w-full" placeholder="Search for products">
            <button class="h-full px-4 bg-black text-white flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
        </div>
    </div>

    <!-- Top Bar (Desktop) -->
    <div class="hidden md:block bg-slate-900 text-white text-[11px] py-1">
        <div class="max-w-[1400px] mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 text-gray-300">
                    <a href="#" class="hover:text-white"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                    <a href="#" class="hover:text-white"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg></a>
                    <a href="#" class="hover:text-white"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg></a>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="tel:+8801720000000" class="hover:text-red-500 transition-colors flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    +88 01720 000000
                </a>
                <span class="text-gray-600">|</span>
                <a href="mailto:support@everbloom.com" class="hover:text-red-500 transition-colors flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    support@everbloom.com
                </a>
                <span class="text-gray-600">|</span>
                <a href="{{ route('track-order') }}" class="hover:text-red-500 transition-colors">Track Order</a>
                <span class="text-gray-600">|</span>
                <a href="{{ route('about') }}" class="hover:text-red-500 transition-colors">About Us</a>
                <span class="text-gray-600">|</span>
                <a href="{{ route('contact') }}" class="hover:text-red-500 transition-colors">Contact Us</a>
            </div>
        </div>
    </div>

    <!-- Main Header (Desktop) -->
    <div class="hidden md:block border-b border-gray-100 bg-white">
        <div class="max-w-[1400px] mx-auto px-4 py-4 flex items-center justify-between gap-6">
            
            <!-- Logo -->
            <a href="/" class="flex-shrink-0 flex items-center gap-1">
                <div class="bg-black text-white px-2 py-0.5 rounded-sm font-black text-2xl italic tracking-tighter">SHEI</div>
                <div class="font-black text-2xl tracking-tighter text-slate-800">TECH</div>
            </a>

            <!-- Search Bar -->
            <div class="flex-1 max-w-3xl flex items-center h-11 border border-gray-300 rounded-full overflow-hidden bg-gray-50">
                <input type="text" class="flex-1 h-full px-5 text-sm bg-transparent border-none focus:ring-0 outline-none w-full" placeholder="Search for products...">
                <button class="h-full px-6 bg-slate-900 text-white hover:bg-red-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 sm:gap-4 flex-shrink-0">
                <!-- Pre Order -->
                <a href="#" class="hidden lg:flex items-center gap-2 bg-[#E60000] text-white px-5 py-2.5 rounded-full text-[14px] font-bold hover:bg-red-700 transition-colors shadow-[0_4px_14px_0_rgba(230,0,0,0.25)]">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                    Pre Order
                </a>

                <div class="hidden lg:block w-px h-8 bg-gray-200 mx-2"></div>

                <!-- Account -->
                @auth
                    <div class="relative" x-data="{ accountOpen: false }" @mouseenter="accountOpen = true" @mouseleave="accountOpen = false" @click.away="accountOpen = false">
                        <button @click="accountOpen = !accountOpen" class="flex items-center gap-3 group text-left">
                            <div class="w-[42px] h-[42px] rounded-full bg-red-50 border border-red-200 shadow-sm flex items-center justify-center transition-all overflow-hidden group-hover:border-red-400">
                                @if(Auth::user()->profile_image)
                                    <img src="{{ Auth::user()->profile_image }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-red-600 font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="hidden sm:flex flex-col">
                                <span class="text-[11px] text-gray-500 font-medium uppercase tracking-wider leading-none mb-1">Welcome back,</span>
                                <span class="text-[14px] font-black text-slate-800 leading-none group-hover:text-red-600 transition-colors truncate max-w-[100px]">{{ explode(' ', Auth::user()->name)[0] }}</span>
                            </div>
                        </button>
                        
                        <div x-show="accountOpen" style="display: none;" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-2"
                             class="absolute right-0 top-full pt-3 w-48 z-50">
                            <div class="relative shadow-xl rounded-lg">
                                <!-- Triangle pointer -->
                                <div class="absolute -top-2 right-5 w-4 h-4 bg-white border-t border-l border-gray-100 transform rotate-45 z-0"></div>
                                <div class="relative bg-white z-10 border border-gray-100 rounded-lg overflow-hidden">
                                    <a href="{{ route('account') }}" class="block px-4 py-2.5 text-[13px] font-semibold text-gray-700 hover:bg-gray-50 hover:text-red-600 transition-colors">My Profile</a>
                                    <a href="#" class="block px-4 py-2.5 text-[13px] font-semibold text-gray-700 hover:bg-gray-50 hover:text-red-600 transition-colors">My Orders</a>
                                    <form method="POST" action="{{ route('logout') }}" class="w-full m-0">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2.5 text-[13px] font-semibold text-red-600 hover:bg-red-50 border-t border-gray-100 transition-colors">Sign Out</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-3 group">
                        <div class="w-[42px] h-[42px] rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center group-hover:border-red-200 group-hover:bg-red-50 transition-all">
                            <svg class="w-[22px] h-[22px] text-slate-600 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div class="hidden sm:flex flex-col">
                            <span class="text-[11px] text-gray-500 font-medium uppercase tracking-wider leading-none mb-1">Hello, Sign In</span>
                            <span class="text-[14px] font-black text-slate-800 leading-none group-hover:text-red-600 transition-colors">My Account</span>
                        </div>
                    </a>
                @endauth

                <div class="hidden sm:block w-px h-8 bg-gray-200 mx-1"></div>

                <!-- Cart -->
                <button @click="isCartOpen = true" class="flex items-center gap-3 group text-left">
                    <div class="relative w-[42px] h-[42px] rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center group-hover:border-red-200 group-hover:bg-red-50 transition-all">
                        <svg class="w-[22px] h-[22px] text-slate-600 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span x-show="cartCount > 0" x-text="cartCount" class="absolute -top-1.5 -right-1.5 bg-[#E60000] text-white text-[10px] font-bold w-[20px] h-[20px] flex items-center justify-center rounded-full border-2 border-white shadow-sm transform group-hover:scale-110 transition-transform" x-cloak></span>
                    </div>
                    <div class="hidden sm:flex flex-col">
                        <span class="text-[11px] text-gray-500 font-medium uppercase tracking-wider leading-none mb-1">My Cart</span>
                        <span class="text-[14px] font-black text-slate-800 leading-none group-hover:text-red-600 transition-colors"><span x-text="cartCount"></span> Items</span>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation (Desktop) -->
    <div class="hidden md:flex max-w-[1400px] mx-auto px-4 items-center justify-between">
        
        <div class="flex items-center flex-1">
            <!-- Categories Dropdown -->
            <div class="relative group h-full flex items-center mr-6 z-50">
                <button class="flex items-center justify-between w-64 bg-slate-50 px-4 py-3 text-blue-600 font-medium border-x border-gray-200">
                    <div class="flex items-center mr-3">
                        <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <span class="text-[13px] uppercase tracking-wide font-semibold">Gadgets</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <!-- Main Dropdown -->
                <div class="absolute left-0 top-full w-64 bg-white border border-gray-100 shadow-md hidden group-hover:block transition-all duration-200">
                    <ul class="flex flex-col">
                        
                        <!-- Item 1 -->
                        <li class="relative border-b border-gray-100 last:border-0" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                            <a href="#" class="flex items-center justify-between px-4 py-3 text-[13px] text-slate-800 transition-colors uppercase tracking-wide font-medium" :class="hover ? 'text-blue-600 bg-slate-50' : 'hover:text-blue-600 hover:bg-slate-50'">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-gray-500" :class="hover ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    Adapters & Cables
                                </div>
                                <svg class="w-3.5 h-3.5 text-gray-400" :class="hover ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                            
                            <!-- Submenu -->
                            <div x-show="hover" style="display: none;" class="absolute left-full top-0 w-80 bg-white rounded-lg shadow-xl border border-gray-100 z-50 ml-1">
                                <ul class="py-3 px-2 flex flex-col gap-1">
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Sample Category 1</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Sample Category 2</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Sample Category 3</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Sample Category 4</a></li>
                                </ul>
                            </div>
                        </li>

                        <!-- Item 2 -->
                        <li class="relative border-b border-gray-100 last:border-0">
                            <a href="#" class="flex items-center justify-between px-4 py-3 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 transition-colors uppercase tracking-wide font-medium">
                                <div class="flex items-center gap-3 ml-7">
                                    Car Accessories
                                </div>
                            </a>
                        </li>

                        <!-- Item 3 -->
                        <li class="relative border-b border-gray-100 last:border-0">
                            <a href="#" class="flex items-center justify-between px-4 py-3 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 transition-colors uppercase tracking-wide font-medium">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-gray-500 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>
                                    Headsets
                                </div>
                            </a>
                        </li>

                        <!-- Item 4 (With Submenu) -->
                        <li class="relative border-b border-gray-100 last:border-0" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                            <a href="#" class="flex items-center justify-between px-4 py-3 text-[13px] transition-colors uppercase tracking-wide font-medium" :class="hover ? 'text-blue-600 bg-slate-50' : 'text-slate-800 hover:text-blue-600 hover:bg-slate-50'">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-gray-500" :class="hover ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                    Home Appliances
                                </div>
                                <svg class="w-3.5 h-3.5 text-gray-400" :class="hover ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                            
                            <!-- Submenu -->
                            <div x-show="hover" style="display: none;" class="absolute left-full top-0 w-80 bg-white rounded-lg shadow-xl border border-gray-100 z-50 ml-1">
                                <ul class="py-3 px-2 flex flex-col gap-1">
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Accessories</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Cleaning Appliances</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Clocks</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Electric Heaters</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Humidifier</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Kitchen Appliances</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Mosquito Bats</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Security & Surveillance Devices</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Security Camera</a></li>
                                </ul>
                            </div>
                        </li>

                        <!-- Item 5 -->
                        <li class="relative border-b border-gray-100 last:border-0" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                            <a href="#" class="flex items-center justify-between px-4 py-3 text-[13px] text-slate-800 transition-colors uppercase tracking-wide font-medium" :class="hover ? 'text-blue-600 bg-slate-50' : 'hover:text-blue-600 hover:bg-slate-50'">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-gray-500" :class="hover ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Lifestyle
                                </div>
                                <svg class="w-3.5 h-3.5 text-gray-400" :class="hover ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                            
                            <!-- Submenu -->
                            <div x-show="hover" style="display: none;" class="absolute left-full top-0 w-80 bg-white rounded-lg shadow-xl border border-gray-100 z-50 ml-1">
                                <ul class="py-3 px-2 flex flex-col gap-1">
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Sample Category 1</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Sample Category 2</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Sample Category 3</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Sample Category 4</a></li>
                                </ul>
                            </div>
                        </li>

                        <!-- Item 6 -->
                        <li class="relative border-b border-gray-100 last:border-0">
                            <a href="#" class="flex items-center justify-between px-4 py-3 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 transition-colors uppercase tracking-wide font-medium">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-gray-500 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                                    Neckbands
                                </div>
                            </a>
                        </li>

                        <!-- Item 7 -->
                        <li class="relative border-b border-gray-100 last:border-0">
                            <a href="#" class="flex items-center justify-between px-4 py-3 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 transition-colors uppercase tracking-wide font-medium">
                                <div class="flex items-center gap-3 ml-7">
                                    Pen Drive
                                </div>
                            </a>
                        </li>

                        <!-- Item 8 -->
                        <li class="relative border-b border-gray-100 last:border-0" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                            <a href="#" class="flex items-center justify-between px-4 py-3 text-[13px] text-slate-800 transition-colors uppercase tracking-wide font-medium" :class="hover ? 'text-blue-600 bg-slate-50' : 'hover:text-blue-600 hover:bg-slate-50'">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-gray-500" :class="hover ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    Phone Accessories
                                </div>
                                <svg class="w-3.5 h-3.5 text-gray-400" :class="hover ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                            
                            <!-- Submenu -->
                            <div x-show="hover" style="display: none;" class="absolute left-full top-0 w-80 bg-white rounded-lg shadow-xl border border-gray-100 z-50 ml-1">
                                <ul class="py-3 px-2 flex flex-col gap-1">
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Sample Category 1</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Sample Category 2</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Sample Category 3</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Sample Category 4</a></li>
                                </ul>
                            </div>
                        </li>

                        <!-- Item 9 -->
                        <li class="relative border-b border-gray-100 last:border-0">
                            <a href="#" class="flex items-center justify-between px-4 py-3 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 transition-colors uppercase tracking-wide font-medium">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-gray-500 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2zm5 4h4v2h-4V7zm0 4h4v2h-4v-2zm0 4h4v2h-4v-2z"></path></svg>
                                    Powerbanks
                                </div>
                            </a>
                        </li>

                        <!-- Item 10 -->
                        <li class="relative border-b border-gray-100 last:border-0">
                            <a href="#" class="flex items-center justify-between px-4 py-3 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 transition-colors uppercase tracking-wide font-medium">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-gray-500 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    Rechargeable Fans
                                </div>
                            </a>
                        </li>

                        <!-- Item 11 -->
                        <li class="relative border-b border-gray-100 last:border-0" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                            <a href="#" class="flex items-center justify-between px-4 py-3 text-[13px] text-slate-800 transition-colors uppercase tracking-wide font-medium" :class="hover ? 'text-blue-600 bg-slate-50' : 'hover:text-blue-600 hover:bg-slate-50'">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-gray-500" :class="hover ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Smart Watches
                                </div>
                                <svg class="w-3.5 h-3.5 text-gray-400" :class="hover ? 'text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                            
                            <!-- Submenu -->
                            <div x-show="hover" style="display: none;" class="absolute left-full bottom-0 w-80 bg-white rounded-lg shadow-xl border border-gray-100 z-50 ml-1">
                                <ul class="py-3 px-2 flex flex-col gap-1">
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Sample Category 1</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Sample Category 2</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Sample Category 3</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-[13px] text-slate-800 hover:text-blue-600 hover:bg-slate-50 rounded uppercase tracking-wide font-medium transition-colors">Sample Category 4</a></li>
                                </ul>
                            </div>
                        </li>

                    </ul>
                </div>
            </div>

            <!-- Links -->
            <nav class="hidden md:flex items-center flex-wrap">
                <a href="{{ route('home') }}" class="mx-3 text-sm uppercase tracking-wide py-3 border-b-2 transition-colors {{ request()->routeIs('home') ? 'text-red-600 border-red-600 font-black' : 'text-slate-700 hover:text-red-600 border-transparent font-bold' }}">Home</a>
                <a href="{{ route('shop') }}" class="mx-3 text-sm uppercase tracking-wide py-3 border-b-2 transition-colors {{ request()->routeIs('shop') ? 'text-red-600 border-red-600 font-black' : 'text-slate-700 hover:text-red-600 border-transparent font-bold' }}">Shop</a>
                <a href="#" class="mx-3 text-slate-700 hover:text-red-600 font-bold text-sm uppercase tracking-wide py-3 border-b-2 border-transparent transition-colors">PC Builder</a>
                <a href="#" class="mx-3 text-slate-700 hover:text-red-600 font-bold text-sm uppercase tracking-wide py-3 border-b-2 border-transparent transition-colors">Campaigns</a>
                <a href="#" class="mx-3 text-slate-700 hover:text-red-600 font-bold text-sm uppercase tracking-wide py-3 border-b-2 border-transparent transition-colors">Offers</a>
                <a href="#" class="mx-3 text-slate-700 hover:text-red-600 font-bold text-sm uppercase tracking-wide py-3 border-b-2 border-transparent transition-colors">Blog</a>
            </nav>
        </div>


    </div>

    <!-- Mobile Drawer Overlay -->
    <div x-show="isOpen" style="display: none;" class="fixed inset-0 bg-black bg-opacity-50 z-50 md:hidden" @click="isOpen = false" x-transition.opacity></div>
    
    <!-- Mobile Drawer Menu -->
    <div x-show="isOpen" style="display: none;" 
         x-transition:enter="transform transition ease-in-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 w-[85%] max-w-sm bg-white z-50 md:hidden flex flex-col">
         
         <!-- Tabs -->
         <div class="flex border-b border-gray-200 shrink-0">
             <button @click="activeTab = 'menu'" class="flex-1 py-4 text-[13px] font-bold text-center tracking-wide" :class="activeTab === 'menu' ? 'text-slate-800 border-b-[3px] border-black bg-white' : 'text-gray-500 bg-gray-100'">MENU</button>
             <button @click="activeTab = 'categories'" class="flex-1 py-4 text-[13px] font-bold text-center tracking-wide" :class="activeTab === 'categories' ? 'text-slate-800 border-b-[3px] border-black bg-white' : 'text-gray-500 bg-gray-100'">CATEGORIES</button>
         </div>

         <!-- Menu Tab Content -->
         <div x-show="activeTab === 'menu'" style="display: none;" class="flex-1 overflow-y-auto pb-20">
             <ul class="flex flex-col">
                 <li class="border-b border-gray-100"><a href="{{ route('home') }}" class="block px-6 py-4 text-[14px] font-bold tracking-wide transition-colors {{ request()->routeIs('home') ? 'text-red-600' : 'text-slate-800 hover:text-red-600' }}">Home</a></li>
                 <li class="border-b border-gray-100"><a href="{{ route('shop') }}" class="block px-6 py-4 text-[14px] font-bold tracking-wide transition-colors {{ request()->routeIs('shop') ? 'text-red-600' : 'text-slate-800 hover:text-red-600' }}">Shop</a></li>
                 <li class="border-b border-gray-100"><a href="#" class="block px-6 py-4 text-[14px] text-slate-800 hover:text-red-600 font-bold tracking-wide transition-colors">PC Builder</a></li>
                 <li class="border-b border-gray-100"><a href="#" class="block px-6 py-4 text-[14px] text-slate-800 hover:text-red-600 font-bold tracking-wide transition-colors">Campaigns</a></li>
                 <li class="border-b border-gray-100"><a href="#" class="block px-6 py-4 text-[14px] text-slate-800 hover:text-red-600 font-bold tracking-wide transition-colors">Offers</a></li>
                 <li class="border-b border-gray-100"><a href="#" class="block px-6 py-4 text-[14px] text-slate-800 hover:text-red-600 font-bold tracking-wide transition-colors">Blog</a></li>
                 <li class="border-b border-gray-100"><a href="#" class="block px-6 py-4 text-[14px] text-slate-800 hover:text-red-600 font-bold tracking-wide transition-colors">Pre Order</a></li>
             </ul>
             <div class="px-6 py-6 mt-4 border-t border-gray-200">
                 <div class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-4">Contact & Support</div>
                 <a href="tel:+8801720000000" class="flex items-center gap-3 text-slate-700 hover:text-red-600 font-medium mb-4">
                     <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                     +88 01720 000000
                 </a>
                 <a href="mailto:support@everbloom.com" class="flex items-center gap-3 text-slate-700 hover:text-red-600 font-medium">
                     <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                     support@everbloom.com
                 </a>
             </div>
         </div>

         <!-- Categories Tab Content -->
         <div x-show="activeTab === 'categories'" class="flex-1 overflow-y-auto pb-20">
             <ul class="flex flex-col">
                 <!-- Item 1 with Accordion -->
                 <li class="border-b border-gray-200" x-data="{ open: true }">
                     <div class="flex items-stretch justify-between text-[14px] text-slate-800 font-medium transition-colors duration-200" :class="open ? 'bg-slate-50' : ''">
                         <a href="#" class="flex items-center gap-3 px-5 py-3.5 flex-1">
                             <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                             Adapters & Cables
                         </a>
                         <button @click="open = !open" class="w-[54px] flex-shrink-0 flex items-center justify-center border-l border-gray-200 transition-colors duration-200" :class="open ? 'bg-black text-white border-black' : 'text-gray-400 bg-white hover:bg-gray-50'">
                             <svg class="w-4 h-4 transition-transform duration-200" :class="open ? '' : '-rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                         </button>
                     </div>
                     <ul x-show="open" class="bg-white border-t border-gray-200">
                         <li class="border-b border-gray-100 last:border-0"><a href="#" class="block pl-12 pr-5 py-3 text-[13px] text-slate-500 hover:text-black">Adapters</a></li>
                         <li class="border-b border-gray-100 last:border-0"><a href="#" class="block pl-12 pr-5 py-3 text-[13px] text-slate-500 hover:text-black">Bluetooth & WIFI Receivers</a></li>
                         <li class="border-b border-gray-100 last:border-0"><a href="#" class="block pl-12 pr-5 py-3 text-[13px] text-slate-500 hover:text-black">Cables</a></li>
                         <li class="border-b border-gray-100 last:border-0"><a href="#" class="block pl-12 pr-5 py-3 text-[13px] text-slate-500 hover:text-black">Dongles & Converters</a></li>
                         <li class="border-b border-gray-100 last:border-0"><a href="#" class="block pl-12 pr-5 py-3 text-[13px] text-slate-500 hover:text-black">Multiplugs</a></li>
                         <li class="border-b border-gray-100 last:border-0"><a href="#" class="block pl-12 pr-5 py-3 text-[13px] text-slate-500 hover:text-black">Universal Adapters</a></li>
                     </ul>
                 </li>
                 
                 <!-- Item 2 -->
                 <li class="border-b border-gray-200"><a href="#" class="flex items-center justify-between px-5 py-3.5 text-[14px] text-slate-800 font-medium"><div class="flex items-center gap-3 pl-8">Car Accessories</div></a></li>
                 
                 <!-- Item 3 -->
                 <li class="border-b border-gray-200"><a href="#" class="flex items-center justify-between px-5 py-3.5 text-[14px] text-slate-800 font-medium"><div class="flex items-center gap-3"><svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>Headsets</div></a></li>
                 
                 <!-- Item 4 with Accordion -->
                 <li class="border-b border-gray-200" x-data="{ open: false }">
                     <div class="flex items-stretch justify-between text-[14px] text-slate-800 font-medium transition-colors duration-200" :class="open ? 'bg-slate-50' : ''">
                         <a href="#" class="flex items-center gap-3 px-5 py-3.5 flex-1">
                             <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                             Home Appliances
                         </a>
                         <button @click="open = !open" class="w-[54px] flex-shrink-0 flex items-center justify-center border-l border-gray-200 transition-colors duration-200" :class="open ? 'bg-black text-white border-black' : 'text-gray-400 bg-white hover:bg-gray-50'">
                             <svg class="w-4 h-4 transition-transform duration-200" :class="open ? '' : '-rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                         </button>
                     </div>
                     <ul x-show="open" style="display: none;" class="bg-white border-t border-gray-200">
                         <li class="border-b border-gray-100 last:border-0"><a href="#" class="block pl-12 pr-5 py-3 text-[13px] text-slate-500 hover:text-black">Sample Subcategory 1</a></li>
                         <li class="border-b border-gray-100 last:border-0"><a href="#" class="block pl-12 pr-5 py-3 text-[13px] text-slate-500 hover:text-black">Sample Subcategory 2</a></li>
                     </ul>
                 </li>
                 
                 <!-- Item 5 -->
                 <li class="border-b border-gray-200"><a href="#" class="flex items-center justify-between px-5 py-3.5 text-[14px] text-slate-800 font-medium"><div class="flex items-center gap-3"><svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Lifestyle</div></a></li>
                 
                 <!-- Item 6 -->
                 <li class="border-b border-gray-200"><a href="#" class="flex items-center justify-between px-5 py-3.5 text-[14px] text-slate-800 font-medium"><div class="flex items-center gap-3"><svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>Neckbands</div></a></li>
                 
                 <!-- Item 7 -->
                 <li class="border-b border-gray-200"><a href="#" class="flex items-center justify-between px-5 py-3.5 text-[14px] text-slate-800 font-medium"><div class="flex items-center gap-3 pl-8">Pen Drive</div></a></li>
                 
                 <!-- Item 8 with Accordion -->
                 <li class="border-b border-gray-200" x-data="{ open: false }">
                     <div class="flex items-stretch justify-between text-[14px] text-slate-800 font-medium transition-colors duration-200" :class="open ? 'bg-slate-50' : ''">
                         <a href="#" class="flex items-center gap-3 px-5 py-3.5 flex-1">
                             <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                             Phone Accessories
                         </a>
                         <button @click="open = !open" class="w-[54px] flex-shrink-0 flex items-center justify-center border-l border-gray-200 transition-colors duration-200" :class="open ? 'bg-black text-white border-black' : 'text-gray-400 bg-white hover:bg-gray-50'">
                             <svg class="w-4 h-4 transition-transform duration-200" :class="open ? '' : '-rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                         </button>
                     </div>
                     <ul x-show="open" style="display: none;" class="bg-white border-t border-gray-200">
                         <li class="border-b border-gray-100 last:border-0"><a href="#" class="block pl-12 pr-5 py-3 text-[13px] text-slate-500 hover:text-black">Sample Subcategory 1</a></li>
                     </ul>
                 </li>
                 
                 <!-- Item 9 -->
                 <li class="border-b border-gray-200"><a href="#" class="flex items-center justify-between px-5 py-3.5 text-[14px] text-slate-800 font-medium"><div class="flex items-center gap-3"><svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2zm5 4h4v2h-4V7zm0 4h4v2h-4v-2zm0 4h4v2h-4v-2z"></path></svg>Powerbanks</div></a></li>
                 
                 <!-- Item 10 -->
                 <li class="border-b border-gray-200"><a href="#" class="flex items-center justify-between px-5 py-3.5 text-[14px] text-slate-800 font-medium"><div class="flex items-center gap-3"><svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>Rechargeable Fans</div></a></li>
             </ul>
         </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-40 flex justify-around items-center py-2 pb-safe shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
        <a href="tel:+8801720000000" class="flex flex-col items-center gap-1 text-slate-700">
            <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
            <span class="text-[11px] font-bold uppercase">Call</span>
        </a>
        <a href="https://wa.me/8801720000000" class="flex flex-col items-center gap-1 text-slate-700">
            <svg class="w-[22px] h-[22px]" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.385 0 12.031c0 2.12.553 4.186 1.602 5.998L.14 23.473l5.59-1.467A11.967 11.967 0 0012.031 24c6.646 0 12.031-5.385 12.031-12.031S18.677 0 12.031 0zM17.9 16.73c-.25.703-1.43 1.343-2.001 1.41-.531.062-1.218.156-3.86-1.023-3.187-1.422-5.265-4.664-5.422-4.882-.156-.219-1.296-1.727-1.296-3.297 0-1.57.812-2.344 1.11-2.656.296-.312.64-.39.86-.39.218 0 .437.008.624.008.188 0 .469-.07.72.547.25.61 1.077 2.624 1.171 2.812.094.187.157.406.032.656-.125.25-.188.406-.375.625-.188.219-.406.453-.562.61-.172.171-.36.359-.14.734.218.375.984 1.625 2.11 2.625 1.453 1.281 2.671 1.671 3.046 1.843.375.172.594.141.813-.11.218-.25.937-1.093 1.187-1.468.25-.375.5-.312.844-.187.344.125 2.187 1.031 2.562 1.218.375.188.625.282.72.438.093.156.093.906-.157 1.609z"/></svg>
            <span class="text-[11px] font-bold uppercase">WhatsApp</span>
        </a>
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 transition-colors {{ request()->routeIs('home') ? 'text-red-600' : 'text-slate-700 hover:text-red-600' }}">
            <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-[11px] font-bold uppercase">Home</span>
        </a>
        <a href="{{ route('shop') }}" class="flex flex-col items-center gap-1 transition-colors {{ request()->routeIs('shop') ? 'text-red-600' : 'text-slate-700 hover:text-red-600' }}">
            <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            <span class="text-[11px] font-bold uppercase">Shop</span>
        </a>
        <button @click="isCartOpen = true" class="flex flex-col items-center gap-1 text-slate-700 relative">
            <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            <span x-show="cartCount > 0" x-text="cartCount" class="absolute -top-1 -right-2 bg-red-600 text-white text-[10px] font-black w-4 h-4 flex items-center justify-center rounded-full border-2 border-white" x-cloak></span>
            <span class="text-[11px] font-bold uppercase text-slate-800">Cart</span>
        </button>
    </div>

    <!-- Cart Drawer Overlay -->
    <div x-show="isCartOpen" style="display: none;" class="fixed inset-0 bg-black bg-opacity-50 z-[60]" @click="isCartOpen = false" x-transition.opacity></div>

    <!-- Cart Drawer Menu -->
    <div x-show="isCartOpen" style="display: none;" 
         x-transition:enter="transform transition ease-in-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 w-full max-w-[360px] bg-white z-[60] shadow-2xl flex flex-col">
         
         <!-- Cart Header -->
         <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
             <div class="flex items-center gap-2">
                 <svg class="w-6 h-6 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                 <h2 class="text-lg font-bold text-slate-800">Your Cart</h2>
                 <span x-text="cartCount" class="bg-gray-100 text-gray-600 text-[11px] font-bold px-2 py-0.5 rounded-full ml-1"></span>
             </div>
             <button @click="isCartOpen = false" class="text-gray-400 hover:text-red-600 transition-colors">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
             </button>
         </div>

         <!-- Cart Content -->
         <div class="flex-1 overflow-y-auto px-5 py-6">
             <!-- Empty Cart -->
             <div x-show="cartCount === 0" class="flex flex-col items-center justify-center h-full text-gray-500 space-y-4">
                 <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                 <p class="font-medium text-slate-800">Your cart is empty.</p>
                 <button @click="isCartOpen = false" class="text-red-600 text-sm font-bold hover:underline">Continue Shopping</button>
             </div>

             <template x-for="(item, index) in cart" :key="index">
                 <div class="flex gap-4 mb-6 pb-6 border-b border-gray-100 group">
                     <div class="w-20 h-20 bg-gray-50 rounded-lg flex-shrink-0 overflow-hidden border border-gray-100">
                         <img :src="item.image" :alt="item.name" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                     </div>
                     <div class="flex-1 flex flex-col justify-between">
                         <div>
                             <h4 class="text-[14px] font-bold text-slate-800 leading-tight mb-1 group-hover:text-red-600 transition-colors" x-text="item.name"></h4>
                             
                             <!-- Attributes Display -->
                             <template x-if="item.attributes && Object.keys(item.attributes).length > 0">
                                 <div class="text-[11px] text-gray-500 mb-2 flex flex-wrap gap-1">
                                     <template x-for="(val, key) in item.attributes" :key="key">
                                         <span>
                                             <span x-text="key + ':'" class="font-medium"></span> 
                                             <span x-text="val"></span>
                                         </span>
                                     </template>
                                 </div>
                             </template>

                             <div class="flex items-center gap-3 mt-1">
                                 <div class="flex items-center border border-gray-200 rounded-md bg-white">
                                     <button @click="updateQuantity(index, -1)" class="px-2 py-1 text-gray-400 hover:text-slate-800 transition-colors">-</button>
                                     <span x-text="item.quantity" class="px-2 py-1 text-[13px] font-bold text-slate-800 border-x border-gray-200"></span>
                                     <button @click="updateQuantity(index, 1)" class="px-2 py-1 text-gray-400 hover:text-slate-800 transition-colors">+</button>
                                 </div>
                                 <button @click="removeItem(index)" class="text-[12px] text-gray-400 hover:text-red-600 underline font-medium">Remove</button>
                             </div>
                         </div>
                        <div class="text-right">
                            <span class="text-[15px] font-black text-slate-800">৳ <span x-text="formatPrice((item.unit_final_price || 0) * (item.quantity || 0))"></span></span>
                        </div>
                     </div>
                 </div>
             </template>
         </div>

         <!-- Cart Footer (Sticky) -->
         <div class="border-t border-gray-200 p-5 bg-gray-50 mt-auto" x-show="cartCount > 0">
             <div class="flex items-center justify-between mb-4">
                 <span class="text-slate-600 font-medium">Subtotal</span>
                 <span class="text-lg font-black text-slate-800">৳ <span x-text="formatPrice(cartTotal)"></span></span>
             </div>
             <p class="text-xs text-gray-500 mb-4">Taxes and shipping calculated at checkout</p>
             <a href="{{ route('checkout') }}" class="block w-full bg-red-600 text-white text-center font-bold uppercase tracking-wide text-sm px-4 py-3.5 rounded hover:bg-red-700 transition-colors">Checkout</a>
         </div>
    </div>
</header>
