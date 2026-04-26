<x-layouts.app title="Track Your Order | Everbloom">
    <div class="bg-gray-50 py-10 md:py-20" x-data="{ 
        hasSearched: false, 
        orderId: '', 
        email: '',
        isSearching: false,
        
        trackOrder() {
            if(!this.orderId || !this.email) return;
            this.isSearching = true;
            setTimeout(() => {
                this.isSearching = false;
                this.hasSearched = true;
            }, 800);
        }
    }">
        <div class="max-w-[800px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                
                <!-- Track Form -->
                <div class="p-8 md:p-12 text-center" x-show="!hasSearched">
                    <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 text-red-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight mb-2">Track Your Order</h2>
                    <p class="text-sm text-gray-500 mb-8 max-w-md mx-auto">
                        To track your order please enter your Order ID in the box below and press the "Track" button. This was given to you on your receipt and in the confirmation email you should have received.
                    </p>
                    
                    <form @submit.prevent="trackOrder" class="max-w-md mx-auto text-left space-y-5">
                        <div>
                            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Order ID *</label>
                            <input type="text" x-model="orderId" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-3" placeholder="Found in your order confirmation email" required>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wide block mb-1">Billing Email *</label>
                            <input type="email" x-model="email" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-red-500 focus:border-red-500 py-3" placeholder="Email you used during checkout" required>
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white px-8 py-3.5 rounded text-sm font-bold uppercase tracking-widest transition-colors shadow-md flex justify-center items-center gap-2" :disabled="isSearching" :class="isSearching ? 'opacity-70 cursor-not-allowed' : ''">
                                <span x-show="!isSearching">Track Order</span>
                                <span x-show="isSearching" x-cloak>Tracking...</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tracking Results Mockup -->
                <div class="p-8 md:p-12" x-show="hasSearched" x-cloak>
                    <div class="flex items-center justify-between border-b border-gray-100 pb-6 mb-8">
                        <div>
                            <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-1">Order <span x-text="orderId" class="text-red-600"></span></h2>
                            <p class="text-sm text-gray-500">Placed on October 24, 2023</p>
                        </div>
                        <button @click="hasSearched = false; orderId = ''; email = ''" class="text-[11px] font-bold text-gray-500 hover:text-red-600 uppercase tracking-wider transition-colors border border-gray-200 px-3 py-1.5 rounded hover:border-red-600">
                            Track Another
                        </button>
                    </div>

                    <div class="mb-10">
                        <div class="flex items-center justify-between text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">
                            <span>Order Placed</span>
                            <span class="text-red-600">Shipped</span>
                            <span>Delivered</span>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="relative w-full h-2 bg-gray-100 rounded-full mb-8">
                            <div class="absolute top-0 left-0 h-full bg-red-600 rounded-full" style="width: 50%;"></div>
                            
                            <!-- Dots -->
                            <div class="absolute top-1/2 left-0 w-4 h-4 bg-red-600 border-4 border-white rounded-full -translate-y-1/2 transform shadow"></div>
                            <div class="absolute top-1/2 left-1/2 w-4 h-4 bg-red-600 border-4 border-white rounded-full -translate-x-1/2 -translate-y-1/2 transform shadow ring-2 ring-red-100"></div>
                            <div class="absolute top-1/2 right-0 w-4 h-4 bg-gray-200 border-4 border-white rounded-full -translate-y-1/2 transform"></div>
                        </div>

                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                            <h4 class="font-bold text-gray-900 text-sm mb-4">Tracking History</h4>
                            <div class="space-y-4">
                                <div class="flex gap-4">
                                    <div class="w-1.5 h-1.5 rounded-full bg-red-600 mt-1.5"></div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900">Package has left the courier facility.</p>
                                        <p class="text-[11px] text-gray-500 mt-0.5">October 26, 2023 - 09:45 AM | Dhaka, BD</p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div class="w-1.5 h-1.5 rounded-full bg-gray-300 mt-1.5"></div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-600">Package is processing at local hub.</p>
                                        <p class="text-[11px] text-gray-400 mt-0.5">October 25, 2023 - 02:15 PM | Dhaka, BD</p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div class="w-1.5 h-1.5 rounded-full bg-gray-300 mt-1.5"></div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-600">Order Information Received by Courier.</p>
                                        <p class="text-[11px] text-gray-400 mt-0.5">October 24, 2023 - 11:30 AM</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-gray-100">
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm mb-2 uppercase tracking-wide">Shipping Address</h4>
                            <address class="text-sm text-gray-600 not-italic leading-relaxed">
                                John Doe<br>
                                House 45, Road 12, Block E<br>
                                Banani, Dhaka - 1213<br>
                                Bangladesh
                            </address>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm mb-2 uppercase tracking-wide">Courier Details</h4>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                <strong>Provider:</strong> eCourier Bangladesh<br>
                                <strong>Tracking ID:</strong> EC-99482910<br>
                                <a href="#" class="text-red-600 hover:underline font-medium mt-1 inline-block">Track on Courier Website &rarr;</a>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
            
        </div>
    </div>
</x-layouts.app>
