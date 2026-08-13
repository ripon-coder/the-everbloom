<x-layouts.app title="Checkout | Feriwalarhat">
    <script>
        function checkoutPage() {
            return {
                userAddresses: {!! \Illuminate\Support\Js::from($userAddresses ?? []) !!},
                sessionCart: {!! \Illuminate\Support\Js::from($sessionCart ?? []) !!},
                districts: {!! \Illuminate\Support\Js::from($districts ?? []) !!},
                selectedAddressId: '',
                fullName: '',
                phone: '',
                address: '',
                districtId: '',
                paymentMethod: 'cod',
                subtotal: 0,
                shippingCost: 0,
                discount: 0,
                total: 0,
                calculatedItems: [],
                calculationErrors: [],
                isCalculating: false,
                isPlacingOrder: false,
                isEditingCart: false,
                couponCode: '',
                couponApplied: false,
                couponError: null,
                isBuyNow: false,

                get hasUnavailableItems() {
                    return this.calculatedItems.some(item => !item.available);
                },

                get allItemsUnavailable() {
                    return this.calculatedItems.length > 0 && this.calculatedItems.every(item => !item.available);
                },

                get isBillingIncomplete() {
                    return !this.fullName || !this.phone || !this.address || !this.districtId;
                },

                formatPrice(price) {
                    return parseFloat(price || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                updateShippingCost() {
                    if (this.districtId && Array.isArray(this.districts)) {
                        const dist = this.districts.find(d => d.id == this.districtId);
                        this.shippingCost = dist ? parseFloat(dist.delivery_charge || 0) : 0;
                    } else {
                        this.shippingCost = 0;
                    }
                    this.total = Math.max(0, this.subtotal + this.shippingCost - this.discount);
                },

                loadInitialCart() {
                    const urlParams = new URLSearchParams(window.location.search);
                    this.isBuyNow = urlParams.get('type') === 'buy_now';

                    let storageKey = this.isBuyNow ? 'buy_now_cart' : 'cart';
                    let localCart = localStorage.getItem(storageKey);
                    let parsed = null;
                    if (localCart) {
                        try {
                            let p = JSON.parse(localCart);
                            if (Array.isArray(p) && p.length > 0) parsed = p;
                        } catch (e) {
                            parsed = null;
                        }
                    }
                    if (!parsed && !this.isBuyNow && Array.isArray(this.sessionCart) && this.sessionCart.length > 0) {
                        parsed = this.sessionCart;
                    }

                    if (!Array.isArray(parsed) || parsed.length === 0 || parsed.some(i => i.available === false || i.is_active === false)) {
                        window.location.href = '{{ route("cart") }}';
                        return;
                    }

                    this.calculatedItems = parsed.map(item => ({
                        ...item,
                        name: item.name || 'Product',
                        available: item.available !== false && item.is_active !== false,
                        unit_final_price: parseFloat(item.unit_final_price || 0),
                        available_stock: parseInt(item.available_stock ?? 999),
                        quantity: parseInt(item.quantity || 1)
                    }));
                    this.subtotal = this.calculatedItems.reduce((acc, i) => acc + (parseFloat(i.unit_final_price || 0) * parseInt(i.quantity || 1)), 0);
                    this.updateShippingCost();
                },

                init() {
                    this.isPlacingOrder = false;
                    this.isCalculating = false;
                    this.loadInitialCart();

                    window.addEventListener('pageshow', () => {
                        this.isPlacingOrder = false;
                        this.isCalculating = false;
                        this.loadInitialCart();
                    });

                    if (this.userAddresses && this.userAddresses.length > 0) {
                        const defaultAddress = this.userAddresses.find(a => a.is_default == 1) || this.userAddresses[0];
                        if (defaultAddress) {
                            this.selectedAddressId = defaultAddress.id;
                            this.applySavedAddress();
                        }
                    }

                    this.$watch('districtId', () => {
                        this.updateShippingCost();
                    });
                },

                applySavedAddress() {
                    if (this.selectedAddressId && this.userAddresses) {
                        const addr = this.userAddresses.find(a => a.id == this.selectedAddressId);
                        if (addr) {
                            this.fullName = addr.name || '';
                            this.phone = addr.phone || '';
                            this.address = addr.address || '';
                            this.districtId = addr.district_id || '';
                        }
                    } else {
                        // User chose to enter a new address
                        this.fullName = '';
                        this.phone = '';
                        this.address = '';
                        this.districtId = '';
                    }
                    this.updateShippingCost();
                },

                calculateCart() {
                    this.isCalculating = true;
                    let storageKey = this.isBuyNow ? 'buy_now_cart' : 'cart';
                    let cart = [];
                    try {
                        let localCart = localStorage.getItem(storageKey);
                        if (localCart) {
                            let parsed = JSON.parse(localCart);
                            if (Array.isArray(parsed) && parsed.length > 0) cart = parsed;
                        }
                    } catch (e) {
                        cart = [];
                    }

                    if (cart.length === 0 && !this.isBuyNow && Array.isArray(this.sessionCart) && this.sessionCart.length > 0) {
                        cart = this.sessionCart;
                    }

                    if (cart.length === 0 && this.calculatedItems.length > 0) {
                        cart = this.calculatedItems;
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                    fetch('{{ route("checkout.calculate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            cart: cart,
                            district_id: this.districtId,
                            coupon_code: this.couponApplied ? this.couponCode : ''
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.data) {
                                if (data.data.errors && data.data.errors.length > 0) {
                                    window.location.href = '{{ route("cart") }}';
                                    return;
                                }
                                if (Array.isArray(data.data.items) && data.data.items.length > 0) {
                                    this.calculatedItems = data.data.items.map(item => ({
                                        ...item,
                                        unit_final_price: parseFloat(item.unit_final_price || 0),
                                        available_stock: parseInt(item.available_stock || 0)
                                    }));
                                }
                                this.subtotal = parseFloat(data.data.subtotal || 0);
                                this.shippingCost = parseFloat(data.data.shipping_cost || 0);
                                this.discount = parseFloat(data.data.discount || 0);
                                this.total = parseFloat(data.data.total || 0);
                                
                                if (data.data.coupon_error) {
                                    this.couponError = data.data.coupon_error;
                                    this.couponApplied = false;
                                } else if (this.couponApplied && !data.data.coupon_error) {
                                    this.couponError = null;
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Calculation error:', error);
                        })
                        .finally(() => {
                            this.isCalculating = false;
                        });
                },

                applyCoupon() {
                    if (!this.couponCode || !this.couponCode.trim()) return;
                    this.couponError = null;
                    this.isCalculating = true;

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    let cart = this.calculatedItems.length > 0 ? this.calculatedItems : (this.sessionCart || []);

                    fetch('{{ route("checkout.calculate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            cart: cart,
                            district_id: this.districtId,
                            coupon_code: this.couponCode.trim()
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.data) {
                            if (data.data.coupon_error) {
                                this.couponError = data.data.coupon_error;
                                this.couponApplied = false;
                                this.discount = 0;
                            } else {
                                this.couponApplied = true;
                                this.couponError = null;
                                this.subtotal = parseFloat(data.data.subtotal || 0);
                                this.shippingCost = parseFloat(data.data.shipping_cost || 0);
                                this.discount = parseFloat(data.data.discount || 0);
                                this.total = parseFloat(data.data.total || 0);
                            }
                        }
                    })
                    .catch(error => console.error('Coupon check error:', error))
                    .finally(() => {
                        this.isCalculating = false;
                    });
                },

                removeCoupon() {
                    this.couponCode = '';
                    this.couponApplied = false;
                    this.couponError = null;
                    this.discount = 0;
                    this.calculateCart();
                },

                placeOrder() {
                    if (this.isBillingIncomplete || this.allItemsUnavailable || this.isPlacingOrder) return;

                    this.isPlacingOrder = true;
                    let storageKey = this.isBuyNow ? 'buy_now_cart' : 'cart';
                    let cart = [];
                    try {
                        let localCart = localStorage.getItem(storageKey);
                        if (localCart) {
                            let parsed = JSON.parse(localCart);
                            if (Array.isArray(parsed) && parsed.length > 0) cart = parsed;
                        }
                    } catch (e) {
                        cart = [];
                    }

                    if (cart.length === 0 && !this.isBuyNow && Array.isArray(this.sessionCart) && this.sessionCart.length > 0) {
                        cart = this.sessionCart;
                    }
                    if (cart.length === 0 && this.calculatedItems.length > 0) {
                        cart = this.calculatedItems;
                    }

                    fetch('{{ route("checkout.place-order") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            full_name: this.fullName,
                            phone: this.phone,
                            address: this.address,
                            district_id: this.districtId,
                            payment_method: this.paymentMethod,
                            cart: cart,
                            coupon_code: this.couponApplied ? this.couponCode : ''
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (this.isBuyNow) {
                                localStorage.removeItem('buy_now_cart');
                            } else {
                                // Set localStorage cart to empty array []
                                localStorage.setItem('cart', '[]');
                                window.initialCartSession = [];
                                this.sessionCart = [];

                                // Sync empty cart with session
                                fetch('{{ route("cart.sync") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({ cart: [] })
                                });

                                // Update cart count in header without opening drawer
                                window.dispatchEvent(new CustomEvent('cart-updated-internal'));
                            }

                            this.calculatedItems = [];

                            // Redirect to Order Received page immediately
                            window.location.href = '/order-received/' + data.order_number;
                        } else {
                            if (data.validation_errors && data.validation_errors.length > 0) {
                                window.location.href = '{{ route("cart") }}';
                                return;
                            }
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: { message: data.message || 'Failed to place order.', type: 'error' }
                            }));
                            this.isPlacingOrder = false;
                        }
                    })
                    .catch(error => {
                        console.error('Order error:', error);
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { message: 'Something went wrong. Please try again.', type: 'error' }
                        }));
                        this.isPlacingOrder = false;
                    });
                }
            };
        }

        function registerCheckoutPage() {
            if (typeof Alpine !== 'undefined') {
                Alpine.data('checkoutPage', () => checkoutPage());
            }
        }
        if (typeof Alpine !== 'undefined') {
            registerCheckoutPage();
        } else {
            document.addEventListener('alpine:init', registerCheckoutPage);
        }
    </script>

    <div class="bg-gray-50 py-4 md:py-8" x-data="checkoutPage()">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumbs -->
            <nav class="flex text-xs font-medium text-gray-500 uppercase tracking-wider mb-6 md:mb-8">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ route('shop') }}" class="hover:text-primary transition-colors">Shop</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900">Checkout</span>
            </nav>

            <form class="flex flex-col lg:flex-row gap-6 md:gap-10">

                <!-- Left Column: Checkout Form -->
                <div class="flex-1 space-y-6 md:space-y-8">

                    <!-- Shipping Address -->
                    <div class="bg-white border border-gray-200 rounded-lg p-5 md:p-6 shadow-sm">
                        <h2 class="text-base md:text-lg font-bold text-gray-900 uppercase tracking-widest mb-4">Shipping
                            Address</h2>

                        @if(auth()->check() && $userAddresses->count() > 0)
                            <div class="mb-4">
                                <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Saved Addresses</label>
                                <select x-model="selectedAddressId" @change="applySavedAddress()" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-primary focus:border-primary py-2.5 cursor-pointer bg-gray-50">
                                    <option value="">-- Or enter new address below --</option>
                                    <template x-for="addr in userAddresses" :key="addr.id">
                                        <option :value="addr.id" x-text="addr.name + ' (' + addr.type + ') - ' + addr.phone"></option>
                                    </template>
                                </select>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label
                                    class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Full
                                    Name *</label>
                                <input type="text" x-model="fullName"
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-primary focus:border-primary py-2.5"
                                    placeholder="Enter your full name" required>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Phone
                                    Number *</label>
                                <input type="tel" x-model="phone"
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-primary focus:border-primary py-2.5"
                                    placeholder="e.g. 017xxxxxxxx" required>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Full
                                    Address *</label>
                                <textarea rows="2" x-model="address"
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-primary focus:border-primary py-2.5"
                                    placeholder="House/Road/Area" required></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">City
                                    / District *</label>
                                <select x-model="districtId"
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-primary focus:border-primary py-2.5 cursor-pointer">
                                    <option value="">Select City</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if(auth()->check() && $userAddresses->count() == 0)
                            <div class="mt-5 p-3 bg-primary-50 text-primary-900 text-xs rounded-md border border-primary-100 flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>You don't have any saved addresses. To speed up future checkouts, you can save your addresses in your <a href="{{ route('account') }}" class="font-bold underline hover:text-primary-dark">Account Dashboard</a>.</span>
                            </div>
                        @endif
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white border border-gray-200 rounded-lg p-5 md:p-6 shadow-sm">
                        <h2 class="text-base md:text-lg font-bold text-gray-900 uppercase tracking-widest mb-4">Payment
                            Method</h2>
                        <p class="text-xs text-gray-500 mb-4">All transactions are secure and encrypted.</p>

                        <div class="space-y-3">
                            <label
                                class="flex items-center justify-between p-4 border rounded-md cursor-pointer transition-colors"
                                :class="paymentMethod === 'cod' ? 'border-primary bg-primary-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="payment" value="cod" x-model="paymentMethod"
                                        class="w-4 h-4 text-primary focus:ring-primary border-gray-300">
                                    <span class="text-sm font-bold text-gray-900">Cash on Delivery (COD)</span>
                                </div>
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </label>

                            <label
                                class="flex items-center justify-between p-4 border rounded-md cursor-pointer transition-colors"
                                :class="paymentMethod === 'online' ? 'border-primary bg-primary-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="payment" value="online" x-model="paymentMethod"
                                        class="w-4 h-4 text-primary focus:ring-primary border-gray-300">
                                    <span class="text-sm font-bold text-gray-900">Online Payment (bKash, Cards)</span>
                                </div>
                                <div class="flex gap-1">
                                    <div
                                        class="w-8 h-5 bg-pink-500 rounded flex items-center justify-center text-white text-[8px] font-black italic">
                                        bKash</div>
                                    <div
                                        class="w-8 h-5 bg-blue-800 rounded flex items-center justify-center text-white text-[8px] font-black">
                                        VISA</div>
                                </div>
                            </label>
                        </div>

                        <div x-show="paymentMethod === 'online'"
                            class="mt-4 p-4 bg-gray-50 rounded text-sm text-gray-600 border border-gray-100">
                            After clicking "Complete Order", you will be redirected to the payment gateway to complete
                            your purchase securely.
                        </div>
                    </div>

                </div>

                <!-- Right Column: Order Summary -->
                <div class="w-full lg:w-[400px] xl:w-[450px] flex-shrink-0">
                    <div class="bg-white border border-gray-200 rounded-lg p-5 md:p-6 shadow-sm sticky top-24 relative">
                        <!-- Loading Overlay -->
                        <div x-show="isCalculating" 
                             class="absolute inset-0 bg-white/50 backdrop-blur-sm z-10 flex flex-col items-center justify-center rounded-lg"
                             x-transition.opacity>
                            <svg class="animate-spin h-8 w-8 text-primary mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-xs font-bold text-gray-700 uppercase tracking-widest drop-shadow-sm">Calculating...</span>
                        </div>

                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-bold text-gray-900 uppercase tracking-widest">Order Summary</h2>
                            <a href="{{ route('cart') }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-primary bg-primary-50 hover:bg-primary-100 rounded-md border border-primary-200 transition-colors shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span>Edit Cart</span>
                            </a>
                        </div>

                        <!-- Cart Items -->
                        <div
                            class="space-y-4 max-h-[300px] overflow-y-auto pt-2 pr-2 scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent mb-6">

                            <template x-for="(item, index) in calculatedItems" :key="index">
                                <div
                                    class="flex items-start gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0"
                                    :class="!item.available && 'opacity-60 bg-danger/10 p-2 rounded-md'">
                                    <div
                                        class="relative w-16 h-16 bg-gray-50 rounded border border-gray-200 flex items-center justify-center flex-shrink-0">
                                        <img :src="item.image || 'https://placehold.co/100x100?text=Product'"
                                            alt="Product" class="max-w-full max-h-full p-1 object-contain">
                                        <span class="absolute -top-2 -right-2 bg-slate-600 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full ring-2 ring-white shadow-sm"
                                            x-text="item.quantity"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-bold text-gray-900 truncate" x-text="item.name"></h4>
                                        <template x-for="(val, key) in item.attributes" :key="key">
                                            <p class="text-xs text-gray-500 mt-0.5" x-text="key + ': ' + val"></p>
                                        </template>
                                        <p class="text-sm font-bold text-primary mt-1 whitespace-nowrap" x-text="'Tk. ' + formatPrice(item.unit_final_price)"></p>
                                        <p x-show="item.available && item.available_stock > 0 && item.available_stock < 10" class="text-[11px] font-semibold text-amber-600 mt-0.5" x-text="'Only ' + item.available_stock + ' left'"></p>
                                        <p x-show="!item.available" class="text-xs font-bold text-danger mt-0.5" x-text="item.available_stock <= 0 ? 'Out of Stock' : ('Only ' + item.available_stock + ' available')"></p>
                                    </div>
                                </div>
                            </template>

                            <div x-show="calculatedItems.length === 0" class="text-center py-4 text-gray-500 text-sm">
                                Your cart is empty.
                            </div>
                        </div>

                        <!-- Coupon Code -->
                        <div class="mb-6 border-y border-gray-100 py-4">
                            <div class="flex gap-2" x-show="!couponApplied">
                                <input type="text" x-model="couponCode" @input="couponError = null"
                                    class="flex-1 border-gray-300 rounded text-sm focus:ring-primary focus:border-primary"
                                    placeholder="Coupon code">
                                <button type="button" @click="applyCoupon()" :disabled="isCalculating || !couponCode"
                                    class="bg-gray-900 text-white px-4 py-2 rounded text-sm font-bold uppercase tracking-wider hover:bg-black transition-colors disabled:opacity-50 disabled:cursor-not-allowed">Apply</button>
                            </div>
                            <div x-show="couponError" x-transition class="flex items-center gap-1.5 mt-2 text-danger bg-danger/10 px-2.5 py-2 rounded border border-danger/20 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-[10px] font-bold uppercase tracking-wider" x-text="couponError"></span>
                            </div>
                            <div x-show="couponApplied" class="flex items-center justify-between mt-2 bg-green-50 border border-green-200 rounded px-3 py-2">
                                <span class="text-xs text-green-700 font-bold" x-text="`Coupon '${couponCode}' applied!`"></span>
                                <button type="button" @click="removeCoupon()" class="text-xs text-danger font-bold hover:underline">Remove</button>
                            </div>
                        </div>

                        <!-- Totals -->
                        <div class="space-y-3 mb-6 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-bold text-gray-900 whitespace-nowrap" x-text="'Tk. ' + formatPrice(subtotal)"></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping</span>
                                <span class="font-bold text-gray-900 whitespace-nowrap" x-text="'Tk. ' + formatPrice(shippingCost)"></span>
                            </div>
                            <div x-show="discount > 0" class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span class="font-bold whitespace-nowrap" x-text="'- Tk. ' + formatPrice(discount)"></span>
                            </div>
                            <div class="flex justify-between items-end pt-4 border-t border-gray-200 mt-4">
                                <span class="text-base font-bold text-gray-900 uppercase tracking-widest">Total</span>
                                <div class="text-right">
                                    <span
                                        class="text-[10px] text-gray-400 block mb-0.5 uppercase tracking-wider">Including
                                        VAT</span>
                                    <span class="text-2xl font-black text-primary whitespace-nowrap"
                                        x-text="'Tk. ' + formatPrice(total)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="button" @click="placeOrder()"
                            :disabled="isCalculating || isPlacingOrder || calculatedItems.length === 0 || allItemsUnavailable || isBillingIncomplete"
                            class="w-full bg-primary hover:bg-primary-dark disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-4 rounded-md text-sm uppercase tracking-widest transition-colors shadow-md flex items-center justify-center gap-2">
                            <svg x-show="isPlacingOrder" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isPlacingOrder ? 'Processing...' : (isCalculating ? 'Calculating...' : 'Complete Order')">Complete Order</span>
                            <svg x-show="!isCalculating && !isPlacingOrder" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>

                        <!-- Trust Badges -->
                        <div class="mt-6 flex justify-center items-center gap-4 text-gray-400">
                            <div class="flex flex-col items-center gap-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                                <span class="text-[9px] font-bold uppercase">Secure</span>
                            </div>
                            <div class="flex flex-col items-center gap-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                    </path>
                                </svg>
                                <span class="text-[9px] font-bold uppercase">Payment</span>
                            </div>
                            <div class="flex flex-col items-center gap-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-[9px] font-bold uppercase">Guarantee</span>
                            </div>
                        </div>

                    </div>
                </div>

            </form>
        </div>
    </div>
</x-layouts.app>
