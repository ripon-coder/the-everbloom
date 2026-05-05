<x-layouts.app title="Checkout | feriwalarhat">
    <div class="bg-gray-50 py-4 md:py-8" x-data="checkoutPage()">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumbs -->
            <nav class="flex text-xs font-medium text-gray-500 uppercase tracking-wider mb-6 md:mb-8">
                <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Home</a>
                <span class="mx-2">/</span>
                <a href="{{ route('shop') }}" class="hover:text-red-600 transition-colors">Shop</a>
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
                                <select x-model="selectedAddressId" @change="applySavedAddress()" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5 cursor-pointer bg-gray-50">
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
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5"
                                    placeholder="Enter your full name" required>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Phone
                                    Number *</label>
                                <input type="tel" x-model="phone"
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5"
                                    placeholder="e.g. 017xxxxxxxx" required>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Full
                                    Address *</label>
                                <textarea rows="2" x-model="address"
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5"
                                    placeholder="House/Road/Area" required></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">City
                                    / District *</label>
                                <select x-model="districtId"
                                    class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5 cursor-pointer">
                                    <option value="">Select City</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if(auth()->check() && $userAddresses->count() == 0)
                            <div class="mt-5 p-3 bg-blue-50 text-blue-700 text-xs rounded-md border border-blue-100 flex items-start gap-2.5">
                                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>You don't have any saved addresses. To speed up future checkouts, you can save your addresses in your <a href="{{ route('account') }}" class="font-bold underline hover:text-blue-900">Account Dashboard</a>.</span>
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
                                :class="paymentMethod === 'cod' ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="payment" value="cod" x-model="paymentMethod"
                                        class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300">
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
                                :class="paymentMethod === 'online' ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="payment" value="online" x-model="paymentMethod"
                                        class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300">
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
                            <svg class="animate-spin h-8 w-8 text-red-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-xs font-bold text-gray-700 uppercase tracking-widest drop-shadow-sm">Calculating...</span>
                        </div>

                        <h2 class="text-lg font-bold text-gray-900 uppercase tracking-widest mb-6">Order Summary</h2>

                        <!-- Cart Items -->
                        <div
                            class="space-y-4 max-h-[300px] overflow-y-auto pt-2 pr-2 scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent mb-6">

                            <template x-for="item in calculatedItems" :key="item.variant_id || item.product_id">
                                <div
                                    class="flex items-start gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0"
                                    :class="!item.available && 'opacity-50'">
                                    <div
                                        class="relative w-16 h-16 bg-gray-50 rounded border border-gray-200 flex items-center justify-center flex-shrink-0">
                                        <img :src="item.image || 'https://placehold.co/100x100?text=Product'"
                                            alt="Product" class="max-w-full max-h-full p-1 object-contain">
                                        <span x-show="item.available"
                                            class="absolute -top-2 -right-2 bg-slate-600 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full ring-2 ring-white shadow-sm"
                                            x-text="item.quantity"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-bold text-gray-900 truncate" x-text="item.name"></h4>
                                        <template x-for="(val, key) in item.attributes" :key="key">
                                            <p class="text-xs text-gray-500 mt-0.5" x-text="key + ': ' + val"></p>
                                        </template>
                                        <p x-show="item.available" class="text-sm font-bold text-red-600 mt-1" x-text="'৳ ' + item.unit_final_price.toFixed(2)"></p>
                                        <p x-show="item.available && item.available_stock < 10" class="text-[11px] font-semibold text-amber-600 mt-0.5" x-text="'Only ' + item.available_stock + ' left'"></p>
                                        <p x-show="!item.available" class="text-xs font-bold text-red-500 mt-1">Out of Stock</p>
                                    </div>
                                </div>
                            </template>

                            <div x-show="calculatedItems.length === 0" class="text-center py-4 text-gray-500 text-sm">
                                Your cart is empty.
                            </div>
                        </div>

                        <!-- Calculation Errors -->
                        <div x-show="calculationErrors.length > 0"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm">
                            <div class="flex items-center mb-2">
                                <svg class="w-4 h-4 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-xs font-bold text-red-800 uppercase tracking-wider">Attention Required</span>
                            </div>
                            <ul class="space-y-1">
                                <template x-for="error in calculationErrors" :key="error">
                                    <li class="text-[11px] text-red-700 leading-tight flex items-start">
                                        <span class="mr-1.5 mt-1 block w-1 h-1 rounded-full bg-red-400 flex-shrink-0"></span>
                                        <span x-text="error"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <!-- Coupon Code -->
                        <div class="mb-6 border-y border-gray-100 py-4">
                            <div class="flex gap-2" x-show="!couponApplied">
                                <input type="text" x-model="couponCode"
                                    class="flex-1 border-gray-300 rounded text-sm focus:ring-red-500 focus:border-red-500"
                                    placeholder="Coupon code">
                                <button type="button" @click="applyCoupon()" :disabled="isCalculating || !couponCode"
                                    class="bg-gray-900 text-white px-4 py-2 rounded text-sm font-bold uppercase tracking-wider hover:bg-black transition-colors disabled:opacity-50 disabled:cursor-not-allowed">Apply</button>
                            </div>
                            <div x-show="couponError" x-transition class="flex items-center gap-1.5 mt-2 text-red-600 bg-red-50 px-2.5 py-2 rounded border border-red-100 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-[10px] font-bold uppercase tracking-wider" x-text="couponError"></span>
                            </div>
                            <div x-show="couponApplied" class="flex items-center justify-between mt-2 bg-green-50 border border-green-200 rounded px-3 py-2">
                                <span class="text-xs text-green-700 font-bold" x-text="`Coupon '${couponCode}' applied!`"></span>
                                <button type="button" @click="removeCoupon()" class="text-xs text-red-500 font-bold hover:underline">Remove</button>
                            </div>
                        </div>

                        <!-- Totals -->
                        <div class="space-y-3 mb-6 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-bold text-gray-900" x-text="'৳ ' + subtotal.toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping</span>
                                <span class="font-bold text-gray-900" x-text="'৳ ' + shippingCost.toFixed(2)"></span>
                            </div>
                            <div x-show="discount > 0" class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span class="font-bold" x-text="'- ৳ ' + discount.toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between items-end pt-4 border-t border-gray-200 mt-4">
                                <span class="text-base font-bold text-gray-900 uppercase tracking-widest">Total</span>
                                <div class="text-right">
                                    <span
                                        class="text-[10px] text-gray-400 block mb-0.5 uppercase tracking-wider">Including
                                        VAT</span>
                                    <span class="text-2xl font-black text-red-600"
                                        x-text="'৳ ' + total.toFixed(2)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="button" @click="placeOrder()"
                            :disabled="isCalculating || isPlacingOrder || calculatedItems.length === 0 || allItemsUnavailable || isBillingIncomplete"
                            class="w-full bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-4 rounded-md text-sm uppercase tracking-widest transition-colors shadow-md flex items-center justify-center gap-2">
                            <svg x-show="isPlacingOrder" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isPlacingOrder ? 'Processing...' : (isCalculating ? 'Calculating...' : 'Complete Order')"></span>
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

    <script>
        function checkoutPage() {
            return {
                userAddresses: @json($userAddresses ?? []),
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
                couponCode: '',
                couponApplied: false,
                couponError: null,

                get hasUnavailableItems() {
                    return this.calculatedItems.some(item => !item.available);
                },

                get allItemsUnavailable() {
                    return this.calculatedItems.length > 0 && this.calculatedItems.every(item => !item.available);
                },

                get isBillingIncomplete() {
                    return !this.fullName || !this.phone || !this.address || !this.districtId;
                },

                init() {
                    if (this.userAddresses.length > 0) {
                        const defaultAddress = this.userAddresses.find(a => a.is_default == 1) || this.userAddresses[0];
                        if (defaultAddress) {
                            this.selectedAddressId = defaultAddress.id;
                            this.applySavedAddress();
                        }
                    }

                    this.calculateCart();

                    this.$watch('districtId', () => {
                        this.calculateCart();
                    });
                },

                applySavedAddress() {
                    if (this.selectedAddressId) {
                        const addr = this.userAddresses.find(a => a.id == this.selectedAddressId);
                        if (addr) {
                            this.fullName = addr.name;
                            this.phone = addr.phone;
                            this.address = addr.address;
                            this.districtId = addr.district_id;
                        }
                    } else {
                        // User chose to enter a new address
                        this.fullName = '';
                        this.phone = '';
                        this.address = '';
                        this.districtId = '';
                    }
                },

                calculateCart() {
                    this.isCalculating = true;
                    let cart = JSON.parse(localStorage.getItem('cart')) || [];

                    fetch('{{ route("checkout.calculate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            cart: cart,
                            district_id: this.districtId,
                            coupon_code: this.couponApplied ? this.couponCode : ''
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.data) {
                                this.calculatedItems = data.data.items;
                                this.subtotal = data.data.subtotal;
                                this.shippingCost = data.data.shipping_cost;
                                this.discount = data.data.discount;
                                this.total = data.data.total;
                                this.calculationErrors = data.data.errors || [];
                                if (this.calculationErrors.length > 0) {
                                    window.dispatchEvent(new CustomEvent('notify', { 
                                        detail: { message: 'Some items in your cart need attention.', type: 'error' } 
                                    }));
                                }
                                
                                if (data.data.coupon_error) {
                                    this.couponError = data.data.coupon_error;
                                    this.couponApplied = false;
                                    window.dispatchEvent(new CustomEvent('notify', { 
                                        detail: { message: data.data.coupon_error, type: 'error' } 
                                    }));
                                } else if (this.couponApplied && !data.data.coupon_error) {
                                    window.dispatchEvent(new CustomEvent('notify', { 
                                        detail: { message: 'Coupon applied successfully!', type: 'success' } 
                                    }));
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Calculation error:', error);
                            this.calculationErrors = ['Failed to calculate checkout totals. Please try again.'];
                        })
                        .finally(() => {
                            this.isCalculating = false;
                        });
                },

                applyCoupon() {
                    if (!this.couponCode) return;
                    this.couponError = null;
                    this.couponApplied = true;
                    this.calculateCart();
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
                    let cart = JSON.parse(localStorage.getItem('cart')) || [];

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
                            // Clear the cart from localStorage
                            localStorage.removeItem('cart');

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

                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: { message: 'Order placed successfully! Order #' + data.order_number, type: 'success' }
                            }));

                            // Redirect to account page after short delay
                            setTimeout(() => {
                                window.location.href = '{{ route("account") }}';
                            }, 1500);
                        } else {
                            // Show validation errors in the errors panel
                            if (data.validation_errors && data.validation_errors.length > 0) {
                                this.calculationErrors = data.validation_errors;
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
            }
        }
    </script>
</x-layouts.app>
