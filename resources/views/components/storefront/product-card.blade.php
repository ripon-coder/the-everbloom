@props([
    'product',
])

@php
    $image = $product['images'][0] ?? null;
    $hoverImage = $product['images'][1] ?? $image;
    $url = url('/shop/product/' . $product['slug']);
    $isOnSale = !empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price'];
    $productJson = htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8');
@endphp

<article class="group relative flex flex-col bg-transparent rounded-none transition-all duration-200">
    <!-- Image Media Container (Fixed aspect-[4/5] so it never collapses or shifts layout) -->
    <div class="relative overflow-hidden bg-sage-light/40 aspect-[4/5] w-full rounded-none">
        <a href="{{ $url }}" class="block w-full h-full focus-visible:outline-none" tabindex="0">
            <x-storefront.image 
                :src="$image" 
                :alt="$product['name']" 
                aspect-ratio="aspect-[4/5]"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
            />
        </a>

        <!-- Badges (Solid = sale, Outline = editorial label; constrained max-w-[65%] so it never overlaps wishlist button) -->
        <div class="absolute top-2.5 left-2.5 sm:top-3 sm:left-3 flex flex-col gap-1.5 pointer-events-none max-w-[65%] z-10">
            @if($isOnSale)
                <span class="inline-block bg-forest text-ivory font-sans text-xs uppercase tracking-[0.16em] sm:tracking-[0.2em] font-medium px-2 py-0.5 sm:px-2.5 sm:py-1 truncate max-w-full">
                    Sale
                </span>
            @endif

            @if(!empty($product['badge']) && !$isOnSale)
                @php
                    $badgeText = $product['badge'];
                    $mobileBadge = ($badgeText === 'Daily Essential') ? 'Essential' : $badgeText;
                @endphp
                <span class="inline-block bg-ivory/95 text-forest font-sans text-xs uppercase tracking-[0.16em] sm:tracking-[0.2em] font-medium px-2 py-0.5 sm:px-2.5 sm:py-1 border border-forest truncate max-w-full" title="{{ $badgeText }}">
                    <span class="sm:hidden">{{ $mobileBadge }}</span>
                    <span class="hidden sm:inline">{{ $badgeText }}</span>
                </span>
            @endif
        </div>

        <!-- Floating Wishlist Button (44x44px touch target) -->
        <div class="absolute top-2 right-2 sm:top-2.5 sm:right-2.5 z-10">
            <x-storefront.wishlist-button :product="$product" size="sm" />
        </div>

        <!-- Quick Add on Mobile/Touch (Compact icon-only "+" button in bottom-right corner, 44x44px touch target, always visible on <lg) -->
        <div class="absolute bottom-2 right-2 sm:bottom-2.5 sm:right-2.5 z-10 lg:hidden">
            <button 
                type="button" 
                x-data="{ adding: false }"
                @click.stop.prevent="
                    adding = true;
                    $store.cart.add({{ $productJson }}, 1, '{{ $product['sizes'][0] ?? null }}', true);
                    setTimeout(() => adding = false, 800);
                "
                :disabled="adding || '{{ $product['stock_status'] ?? 'in_stock' }}' === 'out_of_stock'"
                class="w-11 h-11 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-full bg-ivory/95 text-forest hover:bg-forest hover:text-ivory border border-[#E3E6DD] shadow-sm transition-colors cursor-pointer focus-visible:outline-none focus-visible:border-forest"
                aria-label="Add {{ $product['name'] }} to bag"
            >
                <template x-if="!adding">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </template>
                <template x-if="adding">
                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
            </button>
        </div>

        <!-- Quick Add on Desktop (Revealed on hover AND keyboard focus using group-focus-within on lg+) -->
        <div class="absolute inset-x-3 bottom-3 z-10 opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 group-focus-within:opacity-100 group-focus-within:translate-y-0 transition-all duration-300 hidden lg:block">
            <x-storefront.add-to-cart-button 
                :product="$product" 
                size="{{ $product['sizes'][0] ?? null }}" 
                label="Quick Add" 
                class="!py-2.5 !text-xs shadow-sm" 
            />
        </div>
    </div>

    <!-- Product Meta Below Image -->
    <div class="pt-3.5 flex-1 flex flex-col justify-between">
        <div>
            <!-- Category Tag (12px) -->
            <span class="block text-xs uppercase tracking-[0.2em] font-medium text-muted mb-1 truncate">
                {{ $product['category'] }}
            </span>

            <!-- Title (line-clamp-2 per C5) -->
            <h3 class="font-serif text-sm sm:text-base text-ink font-normal tracking-tight group-hover:text-forest transition-colors line-clamp-2">
                <a href="{{ $url }}" class="text-sm sm:text-base">
                    {{ $product['name'] }}
                </a>
            </h3>

            <!-- Rating & Reviews -->
            <div class="mt-1.5 flex items-center gap-1.5">
                <x-storefront.rating :rating="$product['rating']" :count="$product['review_count']" size="sm" />
            </div>
        </div>

        <div class="mt-3 pt-2 border-t border-[#ECEEE6]">
            <div class="flex items-baseline justify-between">
                <x-storefront.price-display :price="$product['price']" :compare-at-price="$product['compare_at_price'] ?? null" />
            </div>
        </div>
    </div>
</article>
