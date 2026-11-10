@php
    $freeShippingThreshold = config('demo.site.shipping_rates.standard.free_threshold', 65.00);
    $currencySymbol = config('demo.site.currency.symbol', '$');
@endphp

<!-- Catalog and Discount Data Payload for Store Synchronization -->
<script id="everbloom-products-data" type="application/json">
{!! json_encode(config('demo.products', []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script id="everbloom-discounts-data" type="application/json">
{!! json_encode(config('demo.site.demo_discounts', []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

<div 
    x-data
    x-show="$store.cart.drawerOpen" 
    @keydown.escape.window="$store.cart.closeDrawer()"
    class="relative z-50"
    role="dialog"
    aria-modal="true"
    aria-label="Shopping Bag Drawer"
    x-cloak
>
    <!-- Backdrop Overlay -->
    <div 
        x-show="$store.cart.drawerOpen" 
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="$store.cart.closeDrawer()"
        class="fixed inset-0 bg-ink/40 backdrop-blur-xs"
        aria-hidden="true"
    ></div>

    <!-- Sliding Drawer Panel -->
    <div class="fixed inset-y-0 right-0 max-w-md w-full bg-ivory shadow-drawer flex flex-col justify-between overflow-hidden">
        
        <!-- Drawer Header -->
        <div class="p-5 sm:p-6 border-b border-beige/80 flex items-center justify-between bg-ivory">
            <div class="flex items-baseline gap-2">
                <h2 class="font-serif text-xl sm:text-2xl text-ink font-normal">
                    Shopping Bag
                </h2>
                <span class="text-xs uppercase tracking-widest text-muted" x-text="'(' + $store.cart.count + ' items)'">
                    (0 items)
                </span>
            </div>

            <button 
                type="button" 
                @click="$store.cart.closeDrawer()"
                class="p-2 text-ink/70 hover:text-forest transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest cursor-pointer"
                aria-label="Close bag drawer"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Free Shipping Progress Indicator -->
        <div class="bg-sage-light/60 px-5 py-3 border-b border-beige/60 text-xs">
            <template x-if="$store.cart.subtotal >= {{ $freeShippingThreshold }}">
                <p class="text-forest font-medium text-center flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4 text-forest shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Complimentary botanical shipping unlocked!</span>
                </p>
            </template>
            <template x-if="$store.cart.subtotal < {{ $freeShippingThreshold }}">
                <div class="space-y-1.5">
                    <p class="text-muted text-center">
                        Add <span class="font-semibold text-ink" x-text="'{{ $currencySymbol }}' + ({{ $freeShippingThreshold }} - $store.cart.subtotal).toFixed(2)"></span> more for free ground shipping
                    </p>
                    <div class="w-full bg-beige/80 h-1.5 rounded-full overflow-hidden">
                        <div 
                            class="bg-forest h-full transition-all duration-300 rounded-full" 
                            :style="'width: ' + Math.min(100, ($store.cart.subtotal / {{ $freeShippingThreshold }}) * 100) + '%'"
                        ></div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Drawer Body: Cart Items List -->
        <div class="flex-1 overflow-y-auto p-5 sm:p-6 divide-y divide-beige/50">
            <!-- Empty State -->
            <template x-if="$store.cart.items.length === 0">
                <div class="h-full flex flex-col items-center justify-center text-center py-12">
                    <div class="w-16 h-16 rounded-full bg-sage-light/50 flex items-center justify-center text-muted mb-4">
                        <svg class="w-8 h-8 text-forest/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <p class="font-serif text-lg text-ink">Your bag is currently empty</p>
                    <p class="text-xs text-muted max-w-xs mt-1.5 mb-6">Explore our pure botanical formulas to begin your ritual.</p>
                    <x-storefront.button href="/shop" @click="$store.cart.closeDrawer()" size="sm">
                        Explore Formulations
                    </x-storefront.button>
                </div>
            </template>

            <!-- Items Loop -->
            <template x-for="item in $store.cart.items" :key="item.key">
                <div class="py-4 first:pt-0 last:pb-0 flex gap-4">
                    <!-- Thumbnail with x-storefront.image, aspect-square, 96px fixed, wrapped in link -->
                    <a :href="'/shop/product/' + item.slug" class="w-24 h-24 shrink-0 block focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest">
                        <x-storefront.image ::src="'/images/' + item.image" ::alt="item.name" aspect-ratio="aspect-square" class="w-24 h-24 shrink-0 object-cover border border-beige/60" />
                    </a>

                    <!-- Meta Details -->
                    <div class="flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <a :href="'/shop/product/' + item.slug" class="font-serif text-sm font-medium text-ink hover:text-forest transition-colors line-clamp-1" x-text="item.name"></a>
                                <button 
                                    type="button" 
                                    @click="$store.cart.remove(item.key)"
                                    class="text-muted hover:text-ink text-xs p-1 -mr-1 transition-colors cursor-pointer"
                                    :aria-label="'Remove ' + item.name + (item.size ? ' (' + item.size + ')' : '') + ' from bag'"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>

                            <p class="text-[11px] text-muted uppercase tracking-wider mt-0.5" x-show="item.size" x-text="item.size"></p>

                            <div class="mt-1">
                                <span class="text-xs text-muted" x-text="'{{ $currencySymbol }}' + parseFloat(item.price).toFixed(2) + ' each'"></span>
                            </div>
                        </div>

                        <!-- Quantity Selector and Line Total -->
                        <div class="flex items-center justify-between pt-2">
                            <div class="inline-flex items-center border border-beige bg-ivory">
                                <button 
                                    type="button" 
                                    @click="$store.cart.updateQty(item.key, item.qty - 1)" 
                                    class="w-8 h-8 flex items-center justify-center text-ink/70 hover:text-forest transition-colors cursor-pointer"
                                    :aria-label="'Decrease quantity of ' + item.name + (item.size ? ' (' + item.size + ')' : '')"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="w-8 text-center text-xs font-medium text-ink select-none" x-text="item.qty"></span>
                                <button 
                                    type="button" 
                                    @click="$store.cart.updateQty(item.key, item.qty + 1)" 
                                    class="w-8 h-8 flex items-center justify-center text-ink/70 hover:text-forest transition-colors cursor-pointer"
                                    :disabled="item.qty >= 10"
                                    :aria-label="'Increase quantity of ' + item.name + (item.size ? ' (' + item.size + ')' : '')"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>

                            <span class="text-xs font-semibold text-forest" x-text="'{{ $currencySymbol }}' + (item.price * item.qty).toFixed(2)"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Drawer Footer: Subtotal and Actions -->
        <div class="p-5 sm:p-6 border-t border-beige/80 bg-ivory space-y-3" x-show="$store.cart.items.length > 0">
            <div class="flex items-baseline justify-between text-sm">
                <span class="uppercase tracking-widest text-xs text-muted font-medium">Subtotal</span>
                <span class="font-serif text-lg text-ink font-semibold" x-text="'{{ $currencySymbol }}' + $store.cart.subtotal.toFixed(2)"></span>
            </div>
            <p class="text-[11px] text-muted">Taxes and shipping calculated at checkout demonstration.</p>

            <div class="space-y-2 pt-2">
                <a 
                    href="/checkout" 
                    @click="$store.cart.closeDrawer()" 
                    class="btn-primary w-full text-center block"
                >
                    Proceed to Demo Checkout
                </a>
                <a 
                    href="/cart" 
                    @click="$store.cart.closeDrawer()" 
                    class="btn-secondary w-full text-center block !py-2.5 !text-[11px]"
                >
                    View Shopping Bag
                </a>
            </div>
        </div>

    </div>
</div>
