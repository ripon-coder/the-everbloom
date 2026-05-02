<x-layouts.app title="Checkout | Everbloom">
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
                    
                    <!-- Contact Information -->
                    <div class="bg-white border border-gray-200 rounded-lg p-5 md:p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-base md:text-lg font-bold text-gray-900 uppercase tracking-widest">Contact Information</h2>
                            <a href="{{ route('login') }}" class="text-xs text-red-600 font-bold hover:underline">Log in</a>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Email or Mobile Phone Number</label>
                            <input type="text" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5" placeholder="Enter your email or phone">
                        </div>
                        <div class="mt-3 flex items-center">
                            <input type="checkbox" id="newsletter" class="rounded border-gray-300 text-red-600 focus:ring-red-500 h-4 w-4">
                            <label for="newsletter" class="ml-2 text-sm text-gray-600">Email me with news and offers</label>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white border border-gray-200 rounded-lg p-5 md:p-6 shadow-sm">
                        <h2 class="text-base md:text-lg font-bold text-gray-900 uppercase tracking-widest mb-4">Shipping Address</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Full Name *</label>
                                <input type="text" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5" placeholder="Enter your full name" required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Phone Number *</label>
                                <input type="tel" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5" placeholder="e.g. 017xxxxxxxx" required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Full Address *</label>
                                <textarea rows="2" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5" placeholder="House/Road/Area" required></textarea>
                            </div>

                            <div>
                                <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">City / District *</label>
                                <select class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5 cursor-pointer">
                                    <option value="">Select City</option>
                                    <option value="dhaka">Dhaka</option>
                                    <option value="chattogram">Chattogram</option>
                                    <option value="sylhet">Sylhet</option>
                                    <!-- Add more cities as needed -->
                                </select>
                            </div>

                            <div>
                                <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Area / Zone</label>
                                <select class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-2.5 cursor-pointer">
                                    <option value="">Select Area</option>
                                    <!-- Populate dynamically based on city -->
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Method -->
                    <div class="bg-white border border-gray-200 rounded-lg p-5 md:p-6 shadow-sm">
                        <h2 class="text-base md:text-lg font-bold text-gray-900 uppercase tracking-widest mb-4">Shipping Method</h2>
                        
                        <div class="space-y-3">
                            <label class="flex items-center justify-between p-4 border rounded-md cursor-pointer transition-colors" :class="shippingMethod === 'inside_dhaka' ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="shipping" value="inside_dhaka" x-model="shippingMethod" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900">Inside Dhaka Delivery</span>
                                        <span class="text-[11px] text-gray-500">2-3 Business Days</span>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-red-600">৳ 60</span>
                            </label>

                            <label class="flex items-center justify-between p-4 border rounded-md cursor-pointer transition-colors" :class="shippingMethod === 'outside_dhaka' ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="shipping" value="outside_dhaka" x-model="shippingMethod" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900">Outside Dhaka Delivery</span>
                                        <span class="text-[11px] text-gray-500">3-5 Business Days</span>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-red-600">৳ 120</span>
                            </label>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white border border-gray-200 rounded-lg p-5 md:p-6 shadow-sm">
                        <h2 class="text-base md:text-lg font-bold text-gray-900 uppercase tracking-widest mb-4">Payment Method</h2>
                        <p class="text-xs text-gray-500 mb-4">All transactions are secure and encrypted.</p>
                        
                        <div class="space-y-3">
                            <label class="flex items-center justify-between p-4 border rounded-md cursor-pointer transition-colors" :class="paymentMethod === 'cod' ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="payment" value="cod" x-model="paymentMethod" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300">
                                    <span class="text-sm font-bold text-gray-900">Cash on Delivery (COD)</span>
                                </div>
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </label>

                            <label class="flex items-center justify-between p-4 border rounded-md cursor-pointer transition-colors" :class="paymentMethod === 'online' ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="payment" value="online" x-model="paymentMethod" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300">
                                    <span class="text-sm font-bold text-gray-900">Online Payment (bKash, Cards)</span>
                                </div>
                                <div class="flex gap-1">
                                    <div class="w-8 h-5 bg-pink-500 rounded flex items-center justify-center text-white text-[8px] font-black italic">bKash</div>
                                    <div class="w-8 h-5 bg-blue-800 rounded flex items-center justify-center text-white text-[8px] font-black">VISA</div>
                                </div>
                            </label>
                        </div>
                        
                        <div x-show="paymentMethod === 'online'" class="mt-4 p-4 bg-gray-50 rounded text-sm text-gray-600 border border-gray-100">
                            After clicking "Complete Order", you will be redirected to the payment gateway to complete your purchase securely.
                        </div>
                    </div>

                </div>

                <!-- Right Column: Order Summary -->
                <div class="w-full lg:w-[400px] xl:w-[450px] flex-shrink-0">
                    <div class="bg-white border border-gray-200 rounded-lg p-5 md:p-6 shadow-sm sticky top-24">
                        <h2 class="text-lg font-bold text-gray-900 uppercase tracking-widest mb-6">Order Summary</h2>
                        
                        <!-- Cart Items -->
                        <div class="space-y-4 max-h-[300px] overflow-y-auto pt-2 pr-2 scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent mb-6">
                            <!-- Sample Item 1 -->
                            <div class="flex items-start gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                                <div class="relative w-16 h-16 bg-gray-50 rounded border border-gray-200 flex items-center justify-center flex-shrink-0">
                                    <img src="{{ asset('images/image1.jpg') }}" alt="Product" class="max-w-full max-h-full p-1 object-contain" onerror="this.src='https://placehold.co/100x100?text=Product'">
                                    <span class="absolute -top-2 -right-2 bg-slate-600 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full ring-2 ring-white shadow-sm">1</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-bold text-gray-900 truncate">Premium Smart Watch Series 7</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">Color: Black</p>
                                    <p class="text-sm font-bold text-red-600 mt-1">৳ 2,450.00</p>
                                </div>
                            </div>
                            
                            <!-- Sample Item 2 -->
                            <div class="flex items-start gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                                <div class="relative w-16 h-16 bg-gray-50 rounded border border-gray-200 flex items-center justify-center flex-shrink-0">
                                    <img src="{{ asset('images/image1.jpg') }}" alt="Product" class="max-w-full max-h-full p-1 object-contain" onerror="this.src='https://placehold.co/100x100?text=Product'">
                                    <span class="absolute -top-2 -right-2 bg-slate-600 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full ring-2 ring-white shadow-sm">2</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-bold text-gray-900 truncate">Fast Charging Power Bank 20000mAh</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">Capacity: 20000mAh</p>
                                    <p class="text-sm font-bold text-red-600 mt-1">৳ 1,800.00</p>
                                </div>
                            </div>
                        </div>

                        <!-- Coupon Code -->
                        <div class="flex gap-2 mb-6 border-y border-gray-100 py-4">
                            <input type="text" class="flex-1 border-gray-300 rounded text-sm focus:ring-red-500 focus:border-red-500" placeholder="Discount code">
                            <button type="button" class="bg-gray-900 text-white px-4 py-2 rounded text-sm font-bold uppercase tracking-wider hover:bg-black transition-colors">Apply</button>
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
                            <div class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span class="font-bold">- ৳ 0.00</span>
                            </div>
                            <div class="flex justify-between items-end pt-4 border-t border-gray-200 mt-4">
                                <span class="text-base font-bold text-gray-900 uppercase tracking-widest">Total</span>
                                <div class="text-right">
                                    <span class="text-[10px] text-gray-400 block mb-0.5 uppercase tracking-wider">Including VAT</span>
                                    <span class="text-2xl font-black text-red-600" x-text="'৳ ' + total.toFixed(2)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="button" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-md text-sm uppercase tracking-widest transition-colors shadow-md flex items-center justify-center gap-2">
                            <span>Complete Order</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                        
                        <!-- Trust Badges -->
                        <div class="mt-6 flex justify-center items-center gap-4 text-gray-400">
                            <div class="flex flex-col items-center gap-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <span class="text-[9px] font-bold uppercase">Secure</span>
                            </div>
                            <div class="flex flex-col items-center gap-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                <span class="text-[9px] font-bold uppercase">Payment</span>
                            </div>
                            <div class="flex flex-col items-center gap-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
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
                shippingMethod: 'inside_dhaka',
                paymentMethod: 'cod',
                subtotal: 4250.00,
                
                get shippingCost() {
                    return this.shippingMethod === 'inside_dhaka' ? 60.00 : 120.00;
                },
                
                get total() {
                    return this.subtotal + this.shippingCost;
                }
            }
        }
    </script>
</x-layouts.app>
