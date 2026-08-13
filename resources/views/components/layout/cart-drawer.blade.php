<div x-data="cartDrawerData">
    <!-- Backdrop Overlay -->
    <div x-show="$store.cartDrawer ? $store.cartDrawer.isOpen : false" 
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
    <div x-show="$store.cartDrawer ? $store.cartDrawer.isOpen : false" 
        x-cloak style="display: none;"
        x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full" 
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300" 
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed top-0 bottom-0 right-0 h-full w-full max-w-[380px] bg-white z-[1000000] shadow-2xl flex flex-col border-l border-gray-200">

        <!-- Drawer Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-white shrink-0">
            <div class="flex items-center gap-2">
                <svg class="w-6 h-6 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 000-4z">
                    </path>
                </svg>
                <h2 class="text-lg font-bold text-slate-800">Your Cart</h2>
                <span x-text="cartCount"
                    class="bg-gray-100 text-gray-600 text-[11px] font-bold px-2 py-0.5 rounded-full ml-1"></span>
            </div>
            <button @click="$store.cartDrawer.close()" class="p-1.5 text-gray-400 hover:text-slate-800 transition-colors rounded-full hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Drawer Scrollable Content -->
        <div class="flex-1 overflow-y-auto px-5 py-6">
            <!-- Empty Cart State -->
            <div x-show="!cart || cart.length === 0"
                class="flex flex-col items-center justify-center h-full text-gray-500 space-y-4 py-12">
                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 000-4z">
                    </path>
                </svg>
                <p class="font-medium text-slate-800">Your cart is empty.</p>
                <button @click="$store.cartDrawer.close()" class="text-primary text-sm font-bold hover:underline">Continue Shopping</button>
            </div>

            <!-- Cart Items List -->
            <div x-show="cart && cart.length > 0">
                <template x-for="(item, index) in cart" :key="index">
                    <div x-show="item && parseInt(item.quantity || 0) > 0" class="flex gap-4 mb-5 pb-5 border-b border-gray-100 group">
                        <div class="w-20 h-20 bg-gray-50 rounded-lg flex-shrink-0 overflow-hidden border border-gray-100">
                            <img :src="item ? (item.image || item.image_url || item.img || '/images/image1.jpg') : '/images/image1.jpg'" :alt="item ? (item.name || 'Product') : 'Product'" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <div class="flex-1 flex flex-col justify-between">
                            <div>
                                <h4 class="text-[13px] font-bold text-slate-800 leading-tight mb-1 group-hover:text-primary transition-colors" x-text="item ? (item.name || item.title || 'Product') : 'Product'"></h4>
                                <template x-if="item && item.attributes && typeof item.attributes === 'object' && !Array.isArray(item.attributes) && Object.keys(item.attributes).length > 0">
                                    <div class="text-[11px] text-gray-500 mb-2 flex flex-wrap gap-1">
                                        <template x-for="(val, key) in item.attributes" :key="key">
                                            <span><span x-text="key + ':'" class="font-medium"></span> <span x-text="val"></span></span>
                                        </template>
                                    </div>
                                </template>
                                <div class="flex items-center gap-3 mt-1">
                                    <div class="flex items-center border border-gray-200 rounded bg-white">
                                        <button @click="updateQuantity(index, -1)" class="px-2 py-0.5 text-gray-400 hover:text-slate-800 transition-colors">-</button>
                                        <span x-text="item ? (item.quantity || 1) : 1" class="px-2 py-0.5 text-[12px] font-bold text-slate-800 border-x border-gray-200"></span>
                                        <button @click="updateQuantity(index, 1)" class="px-2 py-0.5 text-gray-400 hover:text-slate-800 transition-colors">+</button>
                                    </div>
                                    <button @click="removeItem(index)" class="text-[11px] text-gray-400 hover:text-danger underline font-medium">Remove</button>
                                </div>
                            </div>
                            <div class="text-right mt-2">
                                <span class="text-[14px] font-bold text-slate-800">Tk. <span x-text="formatPrice((item ? (item.unit_final_price || item.unit_base_price || item.price || 0) : 0) * (item ? (item.quantity || 1) : 1))"></span></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Drawer Footer -->
        <div class="border-t border-gray-200 p-5 bg-gray-50 mt-auto shrink-0" x-show="cart && cart.length > 0">
            <div class="flex items-center justify-between mb-3">
                <span class="text-slate-600 font-medium text-sm">Subtotal</span>
                <span class="text-base font-bold text-slate-800">Tk. <span x-text="formatPrice(cartTotal)"></span></span>
            </div>
            <p class="text-xs text-gray-500 mb-4">Taxes and shipping calculated at checkout</p>
            <div class="flex flex-col gap-2">
                <a href="{{ route('cart') }}" class="block w-full bg-slate-900 text-white text-center font-bold uppercase tracking-wide text-xs px-4 py-3 rounded hover:bg-black transition-colors">View & Edit Cart</a>
                <a href="{{ route('checkout') }}" class="block w-full bg-primary text-white text-center font-bold uppercase tracking-wide text-xs px-4 py-3 rounded hover:bg-primary-dark transition-colors">Checkout</a>
            </div>
        </div>
    </div>
</div>
