@props([
    'product',
])

@php
    $image = $product['images'][0] ?? null;
    $hoverImage = $product['images'][1] ?? $image;
    $url = url('/shop/product/' . $product['slug']);
    $isOnSale = !empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price'];
@endphp

<article class="group relative flex flex-col bg-transparent rounded-none transition-all duration-200">
    <!-- Image Media Container -->
    <div class="relative overflow-hidden bg-sage-light/40 aspect-[4/5] w-full rounded-none">
        <a href="{{ $url }}" class="block w-full h-full focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-forest" tabindex="0">
            <x-storefront.image 
                :src="$image" 
                :alt="$product['name']" 
                aspect-ratio="aspect-[4/5]"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
            />
        </a>

        <!-- Badges (Sale takes precedence or pairs with status badge) -->
        <div class="absolute top-3 left-3 flex flex-col gap-1.5 pointer-events-none">
            @if($isOnSale)
                <span class="inline-block bg-forest text-ivory font-sans text-xs uppercase tracking-[0.18em] font-medium px-2.5 py-1 shadow-sm">
                    Sale
                </span>
            @endif

            @if(!empty($product['badge']) && !$isOnSale)
                <span class="inline-block bg-ivory/95 text-forest font-sans text-xs uppercase tracking-[0.18em] font-medium px-2.5 py-1 border border-sage/30 shadow-sm">
                    {{ $product['badge'] }}
                </span>
            @endif
        </div>

        <!-- Floating Wishlist Button -->
        <div class="absolute top-3 right-3 z-10">
            <x-storefront.wishlist-button :product="$product" size="sm" />
        </div>

        <!-- Quick Add on Mobile (Visible compact "+ Add" in image corner below lg) -->
        <div class="absolute bottom-2.5 right-2.5 z-10 lg:hidden">
            <x-storefront.add-to-cart-button 
                :product="$product" 
                size="{{ $product['sizes'][0] ?? null }}" 
                label="+ Add" 
                class="!w-auto !px-2.5 !py-1 !text-xs !min-h-0 !h-8 !bg-ivory/95 !text-forest hover:!bg-forest hover:!text-ivory !border !border-sage/40 shadow-sm backdrop-blur-xs font-medium tracking-normal" 
            />
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
    <div class="pt-4 flex-1 flex flex-col justify-between">
        <div>
            <!-- Category Tag (Minimum 12px) -->
            <span class="block text-xs uppercase tracking-[0.18em] font-medium text-muted mb-1">
                {{ $product['category'] }}
            </span>

            <!-- Title (Minimum 13px+) -->
            <h3 class="font-serif text-base sm:text-lg text-ink font-normal tracking-tight group-hover:text-forest transition-colors line-clamp-1">
                <a href="{{ $url }}" class="text-[15px] sm:text-base">
                    {{ $product['name'] }}
                </a>
            </h3>

            <!-- Rating & Reviews -->
            <div class="mt-1.5 flex items-center gap-1.5">
                <x-storefront.rating :rating="$product['rating']" :count="$product['review_count']" size="sm" />
            </div>
        </div>

        <div class="mt-3 pt-2 border-t border-beige/40">
            <div class="flex items-baseline justify-between">
                <x-storefront.price-display :price="$product['price']" :compare-at-price="$product['compare_at_price'] ?? null" />
            </div>
        </div>
    </div>
</article>
