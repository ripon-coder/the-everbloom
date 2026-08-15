@php
    $flashSale = $product->flashSales->first();
    $initialPrice = $product->price;
    $initialOldPrice = $product->old_price;

    if ($flashSale) {
        $discountPrice = $flashSale->pivot->discount_price;
        $discountPercentage = $flashSale->pivot->discount_percentage;
        $initialOldPrice = $product->price;
        $initialPrice = $discountPrice
            ? ($product->price - $discountPrice)
            : ($product->price - ($product->price * ($discountPercentage / 100)));
        $initialPrice = max(0, $initialPrice);
    }
@endphp

<div class="flex flex-col px-1.5 md:px-0">
    @if($flashSale)
        <div class="mb-3 bg-red-50 border-l-4 border-accent p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-accent animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <span class="text-xs font-black text-accent uppercase tracking-wide">{{ $flashSale->name }}</span>
            </div>

            @php
                $endDate = $flashSale->end_date->format('Y-m-d H:i:s');
            @endphp
            <div class="flex items-center gap-1" x-data="{ 
                            endDate: new Date('{{ $endDate }}').getTime(),
                            days: 0, hours: 0, minutes: 0, seconds: 0,
                            init() {
                                this.updateTimer();
                                setInterval(() => this.updateTimer(), 1000);
                            },
                            updateTimer() {
                                let now = new Date().getTime();
                                let distance = this.endDate - now;
                                if (distance > 0) {
                                    this.days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                    this.hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                    this.minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                    this.seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                } else {
                                    this.days = 0; this.hours = 0; this.minutes = 0; this.seconds = 0;
                                }
                            }
                        }">
                <span class="text-[10px] font-bold text-gray-500 mr-1 uppercase">Ends:</span>
                <template x-if="days > 0">
                    <span class="w-6 h-6 flex items-center justify-center bg-accent text-white text-[10px] font-bold" x-text="days"></span>
                </template>
                <template x-if="days > 0"><span class="text-accent font-bold text-xs">:</span></template>
                <span class="w-6 h-6 flex items-center justify-center bg-accent text-white text-[10px] font-bold" x-text="hours.toString().padStart(2, '0')"></span>
                <span class="text-accent font-bold text-xs">:</span>
                <span class="w-6 h-6 flex items-center justify-center bg-accent text-white text-[10px] font-bold" x-text="minutes.toString().padStart(2, '0')"></span>
                <span class="text-accent font-bold text-xs">:</span>
                <span class="w-6 h-6 flex items-center justify-center bg-accent text-white text-[10px] font-bold" x-text="seconds.toString().padStart(2, '0')"></span>
            </div>
        </div>
    @endif

    <!-- Product Title -->
    <div class="mb-3">
        <h1 class="text-lg md:text-2xl font-bold text-gray-900 leading-snug">{{ $product->name }}</h1>
        <div class="flex items-center gap-3 mt-1.5 text-xs">
            <div class="flex text-amber-400">
                @php $avgRating = $product->reviews->avg('rating') ?: 0; @endphp
                @for($i = 1; $i <= 5; $i++)
                    <svg class="w-3.5 h-3.5 {{ $i <= round($avgRating) ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                @endfor
            </div>
            <span class="text-gray-400">({{ $product->reviews->count() }} reviews)</span>
            <template x-if="currentStock > 0">
                <span class="text-primary font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-primary inline-block"></span>
                    <span x-show="currentStock >= 10">In Stock</span>
                    <span x-show="currentStock < 10">Only <span x-text="currentStock"></span> left!</span>
                </span>
            </template>
            <template x-if="currentStock <= 0">
                <span class="text-danger font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-danger inline-block"></span>
                    Out of Stock
                </span>
            </template>
        </div>
        <div class="mt-2 flex flex-wrap gap-2 text-xs sm:text-sm">
            <div class="flex items-center gap-1">
                <span class="font-bold text-gray-400 uppercase">SKU:</span>
                <span class="bg-gray-100 text-gray-700 px-2 py-0.5 font-semibold" x-text="currentSku">{{ $product->sku ?? 'N/A' }}</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="font-bold text-gray-400 uppercase">Category:</span>
                <span class="bg-gray-100 text-gray-700 px-2 py-0.5 font-semibold">{{ $product->category->name ?? 'Uncategorized' }}</span>
            </div>
            @if($product->is_free_delivery)
                <div class="flex items-center gap-1 text-primary font-bold uppercase">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                    Free Delivery
                </div>
            @endif
        </div>
    </div>

    <!-- Price Section -->
    <div class="py-3 border-y border-gray-100 mb-3 flex items-center justify-between">
        <div>
            <div class="flex items-baseline gap-3">
                <span class="text-2xl md:text-3xl font-bold text-primary">Tk. <span x-text="formatPrice(currentPrice)">{{ number_format($initialPrice, 2) }}</span></span>
                <template x-if="currentOldPrice">
                    <span class="text-sm md:text-base text-gray-400 line-through">Tk. <span x-text="formatPrice(currentOldPrice)">{{ number_format($initialOldPrice, 2) }}</span></span>
                </template>
            </div>
            <div class="h-4">
                <div x-show="currentOldPrice > 0" x-cloak class="text-xs sm:text-sm font-bold text-accent">
                    Save: Tk. <span x-text="formatPrice(currentOldPrice - currentPrice)">{{ $initialOldPrice > 0 ? number_format($initialOldPrice - $initialPrice, 2) : '0.00' }}</span>
                    (<span x-text="currentOldPrice > 0 ? Math.round(((currentOldPrice - currentPrice) / currentOldPrice) * 100) : 0">{{ $initialOldPrice > 0 ? round((($initialOldPrice - $initialPrice) / $initialOldPrice) * 100) : 0 }}</span>% Off)
                </div>
            </div>
        </div>

        <!-- Wishlist -->
        <button @click="toggleWishlist()"
            :disabled="isWishlistLoading"
            class="w-10 h-10 flex items-center justify-center border transition-all disabled:opacity-75 disabled:cursor-wait"
            :class="isInWishlist ? 'border-primary bg-primary-50 text-primary' : 'border-gray-200 text-gray-400 hover:text-gray-600'"
            :title="isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist'">
            <template x-if="isWishlistLoading">
                <svg class="animate-spin w-4 h-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </template>
            <template x-if="!isWishlistLoading">
                <svg class="w-5 h-5" :class="isInWishlist ? 'fill-current' : 'fill-none'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </template>
        </button>
    </div>

    @if($product->is_free_delivery)
        <div class="mb-3 bg-primary-50 border-l-4 border-primary p-3 flex items-center gap-3">
            <svg class="w-5 h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <div>
                <p class="text-xs font-bold text-primary-900 leading-none mb-0.5">Free Express Shipping</p>
                <p class="text-xs text-primary-700">Eligible for free delivery to your doorstep.</p>
            </div>
        </div>
    @endif



    <div class="space-y-4">
        <!-- Attributes Selection -->
        @php
            $groupedAttributes = [];
            if ($product->relationLoaded('variants') && $product->variants) {
                foreach ($product->variants as $variant) {
                    if ($variant->status && $variant->status !== \App\Constants\ProductVariantStatus::ACTIVE) {
                        continue;
                    }
                    if ($variant->relationLoaded('variantAttributes') && $variant->variantAttributes) {
                        foreach ($variant->variantAttributes as $va) {
                            if ($va->attribute && $va->attributeValue) {
                                $attrName = $va->attribute->name;
                                $attrVal = $va->attributeValue->value;
                                $groupedAttributes[$attrName][$va->attribute_value_id] = $attrVal;
                            }
                        }
                    }
                }
            }
        @endphp

        @foreach($groupedAttributes as $name => $values)
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-gray-900 uppercase mb-2">Select {{ $name }}</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($values as $id => $val)
                        <button @click="selectAttribute('{{ $name }}', {{ $id }})"
                            :disabled="!isOptionAvailable('{{ $name }}', {{ $id }})"
                            class="px-3.5 py-2 border text-xs sm:text-sm font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="{
                                'border-primary bg-primary-50 text-primary font-bold': selectedAttributes['{{ $name }}'] === {{ $id }},
                                'border-gray-200 text-slate-800 hover:border-gray-400': selectedAttributes['{{ $name }}'] !== {{ $id }},
                                'opacity-60': !isOptionCompatible('{{ $name }}', {{ $id }}) && selectedAttributes['{{ $name }}'] !== {{ $id }}
                            }">
                            {{ $val }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Quantity -->
        <div class="flex items-center gap-4">
            <span class="text-xs sm:text-sm font-bold text-gray-900 uppercase">Qty</span>
            <div class="flex items-center border border-gray-200 overflow-hidden h-9 md:h-10">
                <button @click="if(quantity > 1) quantity--" class="px-3 py-1 hover:bg-gray-50 border-r border-gray-200 text-gray-600 font-bold">-</button>
                <input type="number" x-model="quantity" class="w-10 text-center border-none focus:ring-0 font-bold text-sm sm:text-base p-0 text-gray-900 bg-transparent opacity-100 read-only:opacity-100 read-only:text-gray-900 [-webkit-text-fill-color:#111827] [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" readonly>
                <button @click="quantity++" class="px-3 py-1 hover:bg-gray-50 border-l border-gray-200 text-gray-600 font-bold">+</button>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2.5 pt-1">
            <button @click="addToCart($event)" :disabled="currentStock <= 0"
                class="flex-1 bg-primary hover:bg-primary-dark disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-bold py-2.5 px-4 text-xs uppercase tracking-wide transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                </svg>
                <span x-text="currentStock > 0 ? 'Add to Cart' : 'Out of Stock'">Add to Cart</span>
            </button>
            <button @click="buyNow($event)" :disabled="currentStock <= 0"
                class="flex-1 bg-gray-900 hover:bg-black disabled:bg-gray-700 disabled:cursor-not-allowed text-white font-bold py-2.5 px-4 text-xs uppercase tracking-wide transition-colors">
                Buy It Now
            </button>
        </div>

        <!-- Trust Badges -->
        <div class="hidden md:flex items-center gap-6 pt-3 mt-1 border-t border-gray-100">
            <div class="flex items-center gap-1.5 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span class="text-[10px] font-semibold uppercase tracking-wide">Secure Payment</span>
            </div>
            <div class="flex items-center gap-1.5 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span class="text-[10px] font-semibold uppercase tracking-wide">Fast Delivery</span>
            </div>
            <div class="flex items-center gap-1.5 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span class="text-[10px] font-semibold uppercase tracking-wide">Easy Returns</span>
            </div>
        </div>
    </div>
</div>
