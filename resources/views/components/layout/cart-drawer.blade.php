<div x-data="cartDrawerData" x-init="if (window.location.pathname.includes('/checkout') && $store.cartDrawer) $store.cartDrawer.close()">
    <!-- Backdrop Overlay -->
    <div x-show="!window.location.pathname.includes('/checkout') && ($store.cartDrawer ? $store.cartDrawer.isOpen : false)" 
        x-cloak style="display: none;"
        class="fixed inset-0 bg-black/60 z-[999999]"
        @click="$store.cartDrawer.close()"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"></div>

    <!-- Right-Side Drawer Panel -->
    <div x-show="!window.location.pathname.includes('/checkout') && ($store.cartDrawer ? $store.cartDrawer.isOpen : false)" 
        x-cloak style="display: none;"
        x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full" 
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300" 
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed top-0 bottom-0 right-0 h-full w-full max-w-[360px] bg-white z-[1000000] flex flex-col border-l border-gray-200">

        <!-- Drawer Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-white shrink-0">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z">
                    </path>
                </svg>
                <h2 class="text-base font-bold text-gray-900 uppercase tracking-wide">Shopping Cart</h2>
                <span x-text="cartCount"
                    class="bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 ml-1" x-show="cartCount > 0"></span>
            </div>
            <button @click="$store.cartDrawer.close()" class="p-1 text-gray-400 hover:text-gray-900 transition-colors" title="Close Drawer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Drawer Scrollable Content -->
        <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
            <!-- Empty Cart State -->
            <div x-show="!cart || cart.length === 0"
                class="flex flex-col items-center justify-center h-full text-gray-500 space-y-3 py-16">
                <div class="w-14 h-14 bg-gray-100 flex items-center justify-center text-gray-400 mb-1">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z">
                        </path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-800">Your cart is empty.</p>
                <button @click="$store.cartDrawer.close()" class="text-primary text-xs sm:text-sm font-semibold hover:underline">Continue Shopping</button>
            </div>

            <!-- Cart Items List -->
            <div x-show="cart && cart.length > 0" class="divide-y divide-gray-100">
                <template x-for="(item, index) in cart" :key="item.variant_id ? ('v-' + item.variant_id) : ('p-' + (item.product_id || index))">
                    <div x-show="item && parseInt(item.quantity || 0) > 0" class="flex gap-3.5 py-4 group">
                        <!-- Product Image -->
                        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-50 flex-shrink-0 overflow-hidden border border-gray-200 p-0.5">
                            <img :src="item ? (item.image || item.image_url || item.img || '/images/image1.jpg') : '/images/image1.jpg'" :alt="item ? (item.name || 'Product') : 'Product'" class="w-full h-full object-cover">
                        </div>

                        <!-- Info & Stepper -->
                        <div class="flex-1 flex flex-col justify-between min-w-0">
                            <div>
                                <h4 class="text-xs sm:text-sm font-semibold text-gray-900 leading-snug mb-1 group-hover:text-primary transition-colors line-clamp-2" x-text="item ? (item.name || item.title || 'Product') : 'Product'"></h4>
                                <template x-if="item && item.attributes && typeof item.attributes === 'object' && !Array.isArray(item.attributes) && Object.keys(item.attributes).length > 0">
                                    <div class="text-xs text-gray-500 mb-1.5 flex flex-wrap gap-1">
                                        <template x-for="(val, key) in item.attributes" :key="key">
                                            <span class="bg-gray-100 px-1.5 py-0.5 text-gray-600 font-medium"><span x-text="key + ':'"></span> <span x-text="val"></span></span>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="item && (item.is_free_delivery || (item.meta && item.meta.free_delivery))">
                                    <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-primary bg-primary-50 px-1.5 py-0.5 mb-1">
                                        <svg class="w-2.5 h-2.5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        <span>Free Delivery</span>
                                    </span>
                                </template>
                            </div>

                            <div class="flex items-center justify-between mt-1 pt-1">
                                <!-- Stepper Controls -->
                                <div class="flex items-center border border-gray-200 bg-white">
                                    <button type="button" @click.stop="updateQuantity(index, -1)" class="w-7 h-7 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition-colors text-xs font-bold">-</button>
                                    <span x-text="item ? (item.quantity || 1) : 1" class="px-2.5 text-xs sm:text-sm font-bold text-gray-900 border-x border-gray-200"></span>
                                    <button type="button" @click.stop="updateQuantity(index, 1)" class="w-7 h-7 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition-colors text-xs font-bold">+</button>
                                </div>

                                <div class="flex items-center gap-3">
                                    <span class="text-xs sm:text-sm font-bold !text-gray-900">Tk. <span x-text="formatPrice((item ? (item.unit_final_price || item.unit_base_price || item.price || 0) : 0) * (item ? (item.quantity || 1) : 1))"></span></span>
                                    <button type="button" @click.stop.prevent="removeItem(index)" class="text-gray-400 hover:text-red-500 transition-colors p-1 flex-shrink-0" title="Remove Item">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Drawer Footer -->
        <div class="border-t border-gray-200 p-4 bg-gray-50 mt-auto shrink-0 space-y-2.5" x-show="cart && cart.length > 0">
            <div class="flex items-center justify-between">
                <span class="text-xs sm:text-sm font-bold !text-gray-900">Subtotal</span>
                <span class="text-sm sm:text-base font-extrabold !text-emerald-700">Tk. <span x-text="formatPrice(cartTotal)"></span></span>
            </div>
            <p class="text-[11px] text-gray-500">Taxes and shipping calculated at checkout</p>
            <div class="flex flex-col gap-1.5 pt-0.5">
                <a href="{{ route('cart') }}" class="block w-full bg-slate-900 hover:bg-black text-white text-center font-bold uppercase tracking-wide text-[11px] sm:text-xs px-3 py-1.5 transition-colors">View Cart</a>
                <a href="{{ route('checkout') }}" class="block w-full bg-primary hover:bg-primary-dark text-white text-center font-bold uppercase tracking-wide text-[11px] sm:text-xs px-3 py-1.5 transition-colors">Checkout</a>
            </div>
        </div>
    </div>
</div>
