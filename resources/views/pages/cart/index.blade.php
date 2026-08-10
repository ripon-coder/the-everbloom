<x-layouts.app title="Shopping Cart | Feriwalarhat">
    <div class="bg-gray-50 py-6 md:py-10" x-data="cartViewPage()">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumbs -->
            <nav class="flex text-xs font-medium text-gray-500 uppercase tracking-wider mb-6 md:mb-8">
                <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ route('shop') }}" class="hover:text-red-600 transition-colors">Shop</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900 font-bold">Shopping Cart</span>
            </nav>

            <!-- Page Header -->
            <div class="flex items-center justify-between mb-6 md:mb-8">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl md:text-3xl font-black text-gray-900 uppercase tracking-tight">Shopping Cart</h1>
                    <span x-show="cart.length > 0" class="bg-red-100 text-red-600 text-xs md:text-sm font-black px-2.5 py-1 rounded-full" x-text="cartCount + ' items'"></span>
                </div>
                <a href="{{ route('shop') }}" class="hidden sm:inline-flex items-center gap-2 text-xs font-bold text-gray-600 hover:text-red-600 transition-colors uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Continue Shopping</span>
                </a>
            </div>

            <!-- Inactive/Unavailable Items Warning Banner -->
            <div x-show="hasInactiveItems" x-cloak class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v3a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span class="text-xs font-bold text-red-800">Attention: One or more items in your cart are no longer available (inactive or out of stock). Please remove unavailable items to proceed to checkout.</span>
            </div>

            <!-- Cart Body -->
            <div x-show="cart.length > 0" class="flex flex-col lg:flex-row gap-6 md:gap-10">

                <!-- Left Column: Cart Items Table/List -->
                <div class="flex-1 space-y-4">
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        
                        <!-- Table Header (Desktop) -->
                        <div class="hidden sm:grid sm:grid-cols-12 gap-4 px-6 py-4 bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <div class="col-span-5">Product Details</div>
                            <div class="col-span-2 text-center">Price</div>
                            <div class="col-span-2 text-center">Quantity</div>
                            <div class="col-span-3 text-right">Subtotal</div>
                        </div>

                        <!-- Items List -->
                        <div class="divide-y divide-gray-100">
                            <template x-for="(item, index) in cart" :key="index">
                                <div class="p-4 sm:p-6 transition-colors" :class="item.is_active === false || item.available === false ? 'bg-red-50/60 opacity-70 grayscale' : 'hover:bg-gray-50/50'">
                                    <div class="flex flex-col sm:grid sm:grid-cols-12 gap-4 sm:items-center">
                                        
                                        <!-- Product Info -->
                                        <div class="sm:col-span-5 flex items-start sm:items-center gap-3.5 sm:gap-4 w-full relative">
                                            <a :href="item.slug ? ('/product/' + item.slug) : '#'" class="relative w-20 h-20 bg-gray-50 rounded-lg border border-gray-200 p-1 flex-shrink-0 flex items-center justify-center group overflow-hidden shadow-2xs">
                                                <img :src="item.image || 'https://placehold.co/100x100?text=Product'" :alt="item.name" class="max-w-full max-h-full object-contain transition-transform duration-300 group-hover:scale-110">
                                            </a>
                                            <div class="flex-1 min-w-0 pr-7 sm:pr-0">
                                                <h3 class="text-sm md:text-base font-extrabold text-gray-900 hover:text-red-600 transition-colors leading-tight line-clamp-2 sm:truncate">
                                                    <a :href="item.slug ? ('/product/' + item.slug) : '#'" x-text="item.name"></a>
                                                </h3>
                                                
                                                <!-- Attributes -->
                                                <div class="flex flex-wrap gap-1.5 mt-1.5">
                                                    <template x-for="(val, key) in item.attributes" :key="key">
                                                        <span class="inline-flex items-center text-[11px] font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-full">
                                                            <span class="text-slate-400 mr-1" x-text="key + ':'"></span>
                                                            <span x-text="val"></span>
                                                        </span>
                                                    </template>
                                                </div>

                                                <template x-if="item.is_active === false || item.available === false">
                                                    <p class="text-[11px] font-bold text-red-600 bg-red-100/80 px-2.5 py-0.5 rounded-md mt-1.5 inline-flex items-center gap-1">
                                                        <svg class="w-3 h-3 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                                        <span x-text="item.status_message || 'No Longer Available'"></span>
                                                    </p>
                                                </template>

                                                <template x-if="item.is_active !== false && item.available !== false && item.available_stock !== undefined && item.available_stock > 0 && item.available_stock < 10">
                                                    <p class="text-[11px] font-semibold text-amber-700 mt-1 flex items-center gap-1">
                                                        <svg class="w-3 h-3 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                                        <span x-text="'Only ' + item.available_stock + ' unit(s) left in stock'"></span>
                                                    </p>
                                                </template>
                                            </div>

                                            <!-- Mobile Top-Right Trash Button -->
                                            <button type="button" @click="removeItem(index)" class="absolute top-0 right-0 sm:hidden p-1 text-gray-400 hover:text-red-600 transition-colors" title="Remove item">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>

                                        <!-- Unit Price (Desktop) -->
                                        <div class="hidden sm:block sm:col-span-2 text-center whitespace-nowrap">
                                            <span class="text-sm font-bold text-gray-900 whitespace-nowrap" x-text="'Tk. ' + formatPrice(item.unit_final_price)"></span>
                                            <template x-if="item.unit_base_price && item.unit_base_price > item.unit_final_price">
                                                <span class="text-xs text-gray-400 line-through block mt-0.5 whitespace-nowrap" x-text="'Tk. ' + formatPrice(item.unit_base_price)"></span>
                                            </template>
                                        </div>

                                        <!-- Mobile Price & Quantity Bar / Desktop Quantity -->
                                        <div class="w-full sm:w-auto sm:col-span-2 flex items-center justify-between sm:justify-center pt-2 sm:pt-0 border-t border-gray-100 sm:border-0 mt-1 sm:mt-0">
                                            <!-- Mobile Price Display -->
                                            <div class="sm:hidden flex flex-col">
                                                <span class="text-xs text-gray-400 uppercase font-semibold">Total</span>
                                                <span class="text-base font-black text-red-600 whitespace-nowrap" x-text="'Tk. ' + formatPrice(item.unit_final_price * item.quantity)"></span>
                                            </div>

                                            <!-- Stepper Controls -->
                                            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden bg-gray-50 shadow-2xs">
                                                <button type="button" @click="updateQuantity(index, -1)" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors font-bold text-sm" :disabled="item.quantity <= 1 || item.is_active === false || item.available === false">-</button>
                                                <input type="number" :value="item.quantity" @change="setQuantity(index, $event.target.value)" class="w-9 h-8 text-center text-xs font-bold text-gray-900 border-none p-0 focus:ring-0 bg-transparent" min="1" max="30" readonly>
                                                <button type="button" @click="updateQuantity(index, 1)" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors font-bold text-sm" :disabled="item.is_active === false || item.available === false">+</button>
                                            </div>
                                        </div>

                                        <!-- Subtotal & Remove Action (Desktop) -->
                                        <div class="hidden sm:flex sm:col-span-3 flex-col items-end justify-center whitespace-nowrap">
                                            <span class="text-base font-black text-red-600 whitespace-nowrap" x-text="'Tk. ' + formatPrice(item.unit_final_price * item.quantity)"></span>
                                            <button type="button" @click="removeItem(index)" class="text-xs font-semibold text-gray-400 hover:text-red-600 mt-1 flex items-center gap-1 transition-colors" title="Remove Item">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Remove</span>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Footer Action Buttons (Desktop Only) -->
                        <div class="hidden sm:flex p-5 bg-gray-50/80 border-t border-gray-200 flex-row items-center justify-between gap-3">
                            <a href="{{ route('shop') }}" class="w-auto inline-flex items-center justify-center gap-2 text-xs font-bold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 px-5 py-2.5 rounded-lg transition-colors uppercase tracking-wider shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                <span>Continue Shopping</span>
                            </a>
                            <button type="button" @click="clearCart()" class="w-auto inline-flex items-center justify-center gap-1.5 text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100/80 border border-red-200 px-4 py-2.5 rounded-lg transition-colors uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Clear Shopping Cart</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Order Summary Card -->
                <div class="w-full lg:w-[380px] xl:w-[420px] flex-shrink-0">
                    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm sticky top-24 space-y-6">
                        <h2 class="text-lg font-black text-gray-900 uppercase tracking-widest border-b border-gray-100 pb-4">Order Summary</h2>

                        <!-- Totals Breakdown -->
                        <div class="space-y-3.5 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-bold text-gray-900 whitespace-nowrap" x-text="'Tk. ' + formatPrice(cartTotal)"></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Estimated Shipping</span>
                                <span class="text-xs font-bold text-emerald-600">Calculated at Checkout</span>
                            </div>
                            <div class="pt-4 border-t border-gray-200 flex justify-between items-end">
                                <div>
                                    <span class="text-base font-extrabold text-gray-900 uppercase tracking-wider block">Estimated Total</span>
                                    <span class="text-[10px] text-gray-400">Taxes & shipping added at checkout</span>
                                </div>
                                <span class="text-2xl font-black text-red-600 whitespace-nowrap" x-text="'Tk. ' + formatPrice(cartTotal)"></span>
                            </div>
                        </div>

                        <!-- Checkout CTA Button -->
                        <template x-if="!hasInactiveItems">
                            <button type="button" @click="proceedToCheckout()"
                               :disabled="checkingOut"
                               class="w-full bg-red-600 hover:bg-red-700 disabled:bg-red-400 disabled:cursor-not-allowed text-white font-bold py-4 rounded-md text-sm uppercase tracking-widest transition-colors shadow-md flex items-center justify-center gap-2">
                                <template x-if="!checkingOut">
                                    <span class="flex items-center gap-2">
                                        <span>Proceed to Checkout</span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </span>
                                </template>
                                <template x-if="checkingOut">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        <span>Validating Cart...</span>
                                    </span>
                                </template>
                            </button>
                        </template>
                        <template x-if="hasInactiveItems">
                            <button type="button" disabled 
                               class="w-full bg-gray-300 text-gray-600 font-bold py-4 rounded-md text-xs uppercase tracking-wider cursor-not-allowed flex items-center justify-center gap-2">
                                <span>Remove Inactive Items</span>
                            </button>
                        </template>

                        <!-- Trust Guarantees -->
                        <div class="pt-4 border-t border-gray-100 space-y-3 text-xs text-gray-500">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>100% Authentic & Genuine Products</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Encrypted & Safe Checkout</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                <span>Cash on Delivery Available</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Empty Cart State -->
            <div x-show="cart.length === 0" class="bg-white border border-gray-200 rounded-lg p-12 text-center max-w-5xl mx-auto shadow-sm my-8">
                <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <h2 class="text-xl font-extrabold text-gray-900 mb-2">Your Shopping Cart is Empty</h2>
                <p class="text-sm text-gray-500 mb-6">Looks like you haven't added any products to your cart yet. Explore our shop to find awesome products!</p>
                <a href="{{ route('shop') }}" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold px-8 py-3.5 rounded-md text-xs uppercase tracking-widest transition-colors shadow-md">
                    <span>Explore Products</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

        </div>
    </div>

    <script>
        function cartViewPage() {
            return {
                cart: [],
                checkingOut: false,

                init() {
                    this.loadCart();
                    this.saveCart(); // Initial sync to validate stock against DB
                    window.addEventListener('cart-updated', () => this.loadCart());
                    window.addEventListener('cart-updated-internal', () => this.loadCart());
                },

                loadCart() {
                    const localCart = localStorage.getItem('cart');
                    if (localCart) {
                        try {
                            this.cart = JSON.parse(localCart);
                        } catch (e) {
                            this.cart = [];
                        }
                    } else {
                        this.cart = {!! \Illuminate\Support\Js::from(session('cart', [])) !!};
                    }
                },

                get cartCount() {
                    return this.cart.reduce((total, item) => total + parseInt(item.quantity || 0), 0);
                },

                get cartTotal() {
                    return this.cart.reduce((total, item) => total + (parseFloat(item.unit_final_price || 0) * parseInt(item.quantity || 0)), 0);
                },

                get hasInactiveItems() {
                    return this.cart.some(item => item.is_active === false || item.available === false);
                },

                updateQuantity(index, delta) {
                    if (!this.cart[index]) return;
                    let currentQty = parseInt(this.cart[index].quantity || 0);
                    let availableStock = parseInt(this.cart[index].available_stock);
                    let newQty = currentQty + delta;

                    if (newQty <= 0) {
                        this.removeItem(index);
                        return;
                    }

                    if (!isNaN(availableStock) && availableStock > 0 && newQty > availableStock) {
                        window.dispatchEvent(new CustomEvent('notify', { 
                            detail: { message: `Only ${availableStock} unit(s) available in stock for this item.`, type: 'error' } 
                        }));
                        this.cart[index].quantity = availableStock;
                        this.saveCart();
                        return;
                    }

                    if (newQty > 30) {
                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Maximum 30 items allowed per product.', type: 'error' } }));
                        return;
                    }

                    this.cart[index].quantity = newQty;
                    this.cart[index].line_total = newQty * (parseFloat(this.cart[index].unit_final_price) || 0);
                    this.saveCart();
                },

                setQuantity(index, val) {
                    let newQty = parseInt(val) || 1;
                    let availableStock = parseInt(this.cart[index]?.available_stock);
                    if (!isNaN(availableStock) && availableStock > 0 && newQty > availableStock) {
                        newQty = availableStock;
                        window.dispatchEvent(new CustomEvent('notify', { 
                            detail: { message: `Only ${availableStock} unit(s) available in stock.`, type: 'error' } 
                        }));
                    }
                    newQty = Math.max(1, Math.min(30, newQty));
                    if (this.cart[index]) {
                        this.cart[index].quantity = newQty;
                        this.cart[index].line_total = newQty * (parseFloat(this.cart[index].unit_final_price) || 0);
                        this.saveCart();
                    }
                },

                removeItem(index) {
                    this.cart.splice(index, 1);
                    this.saveCart();
                },

                clearCart() {
                    this.cart = [];
                    this.saveCart();
                },

                saveCart() {
                    localStorage.setItem('cart', JSON.stringify(this.cart));
                    window.dispatchEvent(new CustomEvent('cart-updated-internal'));
                    fetch('/cart/sync', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ cart: this.cart })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.cart) {
                            data.cart.forEach(syncedItem => {
                                const idx = this.cart.findIndex(i => i.product_id === syncedItem.product_id && i.variant_id === syncedItem.variant_id);
                                if (idx > -1) {
                                    this.cart[idx].available_stock = syncedItem.available_stock;
                                    this.cart[idx].is_active = syncedItem.is_active;
                                    this.cart[idx].available = syncedItem.available;
                                    this.cart[idx].status_message = syncedItem.status_message;
                                    this.cart[idx].quantity = syncedItem.quantity;
                                    this.cart[idx].line_total = syncedItem.line_total;
                                }
                            });
                            localStorage.setItem('cart', JSON.stringify(this.cart));
                        }
                    })
                    .catch(error => console.error('Cart sync error:', error));
                },

                formatPrice(price) {
                    return parseFloat(price || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                async proceedToCheckout() {
                    if (this.checkingOut) return;
                    this.checkingOut = true;
                    try {
                        const response = await fetch('/cart/sync', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ cart: this.cart })
                        });
                        const data = await response.json();

                        if (data && data.cart) {
                            // Apply fresh data from server
                            data.cart.forEach(syncedItem => {
                                const idx = this.cart.findIndex(i => i.product_id === syncedItem.product_id && i.variant_id === syncedItem.variant_id);
                                if (idx > -1) {
                                    this.cart[idx].available_stock = syncedItem.available_stock;
                                    this.cart[idx].is_active      = syncedItem.is_active;
                                    this.cart[idx].available      = syncedItem.available;
                                    this.cart[idx].status_message = syncedItem.status_message;
                                    this.cart[idx].quantity       = syncedItem.quantity;
                                    this.cart[idx].line_total     = syncedItem.line_total;
                                }
                            });
                            localStorage.setItem('cart', JSON.stringify(this.cart));

                            const hasIssues = this.cart.some(
                                item => item.is_active === false || item.available === false || parseInt(item.available_stock || 0) === 0
                            );

                            if (hasIssues) {
                                window.dispatchEvent(new CustomEvent('notify', {
                                    detail: { message: 'Some items are no longer available. Please remove them before proceeding to checkout.', type: 'error' }
                                }));
                            } else {
                                window.location.href = '{{ route('checkout') }}';
                            }
                        } else {
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: { message: 'Could not validate cart. Please try again.', type: 'error' }
                            }));
                        }
                    } catch (error) {
                        console.error('Checkout validation error:', error);
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { message: 'Connection error. Please check your internet and try again.', type: 'error' }
                        }));
                    } finally {
                        this.checkingOut = false;
                    }
                }
            }
        }
    </script>
</x-layouts.app>
