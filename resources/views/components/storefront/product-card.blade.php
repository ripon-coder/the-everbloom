@props([
    'product',
])

@php
    $image = $product['images'][0] ?? null;
    $hoverImage = $product['images'][1] ?? $image;
    $url = url('/shop/product/' . $product['slug']);
    $isOnSale = !empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price'];
    $discountPercent = $isOnSale ? (int) round((($product['compare_at_price'] - $product['price']) / $product['compare_at_price']) * 100) : 0;
    $brandOrCategory = !empty($product['brand']) ? $product['brand'] : ($product['category'] ?? 'The Everbloom');
@endphp

<article class="group relative flex flex-col bg-transparent rounded-none transition-all duration-300 hover:-translate-y-0.5">
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
                <span class="inline-block bg-forest text-ivory font-sans text-xs uppercase tracking-[0.16em] sm:tracking-[0.2em] font-medium px-2.5 py-0.5 sm:px-3 sm:py-1 truncate max-w-full">
                    {{ $discountPercent > 0 ? $discountPercent . '% OFF' : 'Sale' }}
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
    </div>

    <!-- Product Meta Below Image -->
    <div class="pt-3.5 flex-1 flex flex-col justify-between">
        <div>
            <!-- Brand / Category Tag (12px) -->
            <span class="block text-xs uppercase tracking-[0.2em] font-medium text-muted mb-1 truncate">
                {{ $brandOrCategory }}
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
