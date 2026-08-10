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

<div class="flex flex-col">
    @if($flashSale)
        <div
            class="mb-4 bg-red-50 border border-red-200 rounded-md p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z">
                    </path>
                </svg>
                <span class="text-sm font-black text-red-600 uppercase tracking-wide">{{ $flashSale->name }}</span>
            </div>

            @php
                $endDate = $flashSale->end_date->format('Y-m-d H:i:s');
            @endphp
            <div class="flex items-center gap-1.5" x-data="{ 
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
                <span class="text-xs font-bold text-red-500 mr-1 uppercase tracking-wider">Ends in:</span>
                <template x-if="days > 0">
                    <span
                        class="w-6 h-6 flex items-center justify-center bg-red-600 text-white rounded text-xs font-bold shadow-sm"
                        x-text="days"></span>
                </template>
                <template x-if="days > 0"><span class="text-red-600 font-bold">:</span></template>
                <span
                    class="w-6 h-6 flex items-center justify-center bg-red-600 text-white rounded text-xs font-bold shadow-sm"
                    x-text="hours.toString().padStart(2, '0')"></span>
                <span class="text-red-600 font-bold">:</span>
                <span
                    class="w-6 h-6 flex items-center justify-center bg-red-600 text-white rounded text-xs font-bold shadow-sm"
                    x-text="minutes.toString().padStart(2, '0')"></span>
                <span class="text-red-600 font-bold">:</span>
                <span
                    class="w-6 h-6 flex items-center justify-center bg-red-600 text-white rounded text-xs font-bold shadow-sm"
                    x-text="seconds.toString().padStart(2, '0')"></span>
            </div>
        </div>
    @endif

    <div class="mb-2 md:mb-4">
        <h1 class="text-xl md:text-2xl font-extrabold text-gray-900 mb-1.5 md:mb-2 leading-tight">{{ $product->name }}
        </h1>
        <div class="flex items-center gap-3 text-xs md:text-sm">
            <div class="flex text-amber-400">
                @php $avgRating = $product->reviews->avg('rating') ?: 0; @endphp
                @for($i = 1; $i <= 5; $i++)
                    <svg class="w-3.5 h-3.5 md:w-4 md:h-4 {{ $i <= round($avgRating) ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                @endfor
            </div>
            <span class="text-gray-400 font-medium">({{ $product->reviews->count() }} reviews)</span>
            <template x-if="currentStock > 0">
                <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full inline-block"></span>
                    <span x-show="currentStock >= 10">In Stock</span>
                    <span x-show="currentStock < 10">Only <span x-text="currentStock"></span> left!</span>
                </span>
            </template>
            <template x-if="currentStock <= 0">
                <span class="inline-flex items-center gap-1 text-red-600 font-semibold">
                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full inline-block"></span>
                    Out of Stock
                </span>
            </template>
            @if($product->is_free_delivery)
                <span class="text-gray-300">|</span>
                <span
                    class="inline-flex items-center gap-1.5 text-blue-600 font-bold uppercase tracking-wider text-[10px] md:text-xs">
                    <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    Free Delivery
                </span>
            @endif
        </div>
        <div class="mt-2 flex flex-wrap gap-3 text-[10px] md:text-xs">
            <div class="flex items-center gap-1.5">
                <span class="font-bold text-gray-400 uppercase tracking-tighter">SKU:</span>
                <span
                    class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md font-medium"
                    x-text="currentSku">{{ $product->sku ?? 'N/A' }}</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="font-bold text-gray-400 uppercase tracking-tighter">Category:</span>
                <span
                    class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md font-medium">{{ $product->category->name ?? 'Uncategorized' }}</span>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-1 py-3 md:py-4 border-y border-gray-100 mb-3 md:mb-4">
        <div class="flex items-center justify-between gap-4">
            <div class="flex flex-col gap-1">
                <div class="flex items-baseline gap-3 md:gap-4">
                    <span class="text-2xl md:text-3xl font-bold text-red-600">Tk. <span
                            x-text="formatPrice(currentPrice)">{{ number_format($initialPrice, 2) }}</span></span>
                    <template x-if="currentOldPrice">
                        <span class="text-base md:text-lg text-red-600/70 line-through font-medium">Tk. <span
                                x-text="formatPrice(currentOldPrice)">{{ number_format($initialOldPrice, 2) }}</span></span>
                    </template>
                </div>
                <!-- Savings Info -->
                <div class="h-4 md:h-5">
                    <div x-show="currentOldPrice > 0" x-cloak class="text-[10px] md:text-xs font-bold text-green-600">
                        You Save: Tk. <span
                            x-text="formatPrice(currentOldPrice - currentPrice)">{{ $initialOldPrice > 0 ? number_format($initialOldPrice - $initialPrice, 2) : '0.00' }}</span>
                        (<span
                            x-text="currentOldPrice > 0 ? Math.round(((currentOldPrice - currentPrice) / currentOldPrice) * 100) : 0">{{ $initialOldPrice > 0 ? round((($initialOldPrice - $initialPrice) / $initialOldPrice) * 100) : 0 }}</span>%
                        Off)
                    </div>
                </div>
            </div>

            <!-- Wishlist Button -->
            <button @click="toggleWishlist()"
                class="group relative flex items-center justify-center w-11 h-11 rounded-full transition-all duration-300"
                :class="isInWishlist ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600'"
                :title="isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist'">
                <svg class="w-6 h-6 transition-transform duration-300 group-hover:scale-110" 
                    :class="isInWishlist ? 'fill-current' : 'fill-none'"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                
                <!-- Tooltip -->
                <span class="absolute -top-10 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                    <span x-text="isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist'"></span>
                </span>
            </button>
        </div>
    </div>

    @if($product->is_free_delivery)
        <div class="mb-4 bg-blue-50 border border-blue-100 rounded-md p-3 flex items-center gap-3">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-blue-600 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div>
                <p class="text-[13px] font-extrabold text-blue-900 leading-none mb-1">Free Express Shipping</p>
                <p class="text-[11px] text-blue-700 font-medium">Eligible for free delivery to your doorstep.</p>
            </div>
        </div>
    @endif

    <div class="text-sm text-gray-600 mb-4 leading-relaxed line-clamp-2 md:line-clamp-3">
        {!! $product->description ?? 'No description available.' !!}
    </div>

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
                <h3 class="text-[10px] md:text-xs font-bold text-gray-900 uppercase mb-2">Select {{ $name }}</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($values as $id => $val)
                        <button @click="selectAttribute('{{ $name }}', {{ $id }})"
                            :disabled="!isOptionAvailable('{{ $name }}', {{ $id }})"
                            class="px-3 py-1.5 rounded-md border text-xs md:text-sm font-medium transition-all disabled:opacity-10 disabled:cursor-not-allowed"
                            :class="{
                                'border-red-600 bg-red-50 text-red-600': selectedAttributes['{{ $name }}'] === {{ $id }},
                                'border-gray-200 text-gray-600 hover:border-gray-400': selectedAttributes['{{ $name }}'] !== {{ $id }},
                                'opacity-30 grayscale': !isOptionCompatible('{{ $name }}', {{ $id }}) && selectedAttributes['{{ $name }}'] !== {{ $id }}
                            }">
                            {{ $val }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Quantity -->
        <div class="flex items-center gap-4 pt-1">
            <span class="text-[10px] md:text-xs font-bold text-gray-900 uppercase">Qty</span>
            <div class="flex items-center border border-gray-300 rounded-md overflow-hidden h-9 md:h-10">
                <button @click="if(quantity > 1) quantity--"
                    class="px-3 py-1 hover:bg-gray-100 border-r border-gray-300">-</button>
                <input type="number" x-model="quantity"
                    class="w-10 text-center border-none focus:ring-0 font-medium text-sm p-0" readonly>
                <button @click="quantity++" class="px-3 py-1 hover:bg-gray-100 border-l border-gray-300">+</button>
            </div>
        </div>

        <!-- Action Buttons (Desktop Only) -->
        <div class="hidden md:flex flex-col sm:flex-row gap-2 pt-2">
            <button @click="addToCart($event)" :disabled="currentStock <= 0"
                class="flex-1 bg-red-600 hover:bg-red-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-bold py-3.5 px-4 rounded-md text-xs uppercase tracking-wide transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                </svg>
                <span x-text="currentStock > 0 ? 'Add to Cart' : 'Out of Stock'">Add to Cart</span>
            </button>
            <button :disabled="currentStock <= 0"
                class="flex-1 bg-gray-900 hover:bg-black disabled:bg-gray-700 disabled:cursor-not-allowed text-white font-bold py-3.5 px-4 rounded-md text-xs uppercase tracking-wide transition-colors">
                Buy It Now
            </button>
        </div>

        <!-- Trust Badges -->
        <div class="hidden md:flex items-center gap-6 pt-4 mt-2 border-t border-gray-100">
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
