@props([
    'product',
])

@php
    $image = $product['images'][0] ?? null;
    $hoverImage = $product['images'][1] ?? $image;
    $url = url('/shop/product/' . $product['slug']);
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

        <!-- Badge -->
        @if(!empty($product['badge']))
            <div class="absolute top-3 left-3 pointer-events-none">
                <span class="inline-block bg-ivory/95 text-forest font-sans text-[10px] uppercase tracking-[0.2em] font-medium px-2.5 py-1 border border-sage/30 shadow-sm">
                    {{ $product['badge'] }}
                </span>
            </div>
        @elseif(!empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price'])
            <div class="absolute top-3 left-3 pointer-events-none">
                <span class="inline-block bg-forest text-ivory font-sans text-[10px] uppercase tracking-[0.2em] font-medium px-2 py-1">
                    Offer
                </span>
            </div>
        @endif

        <!-- Floating Wishlist Button -->
        <div class="absolute top-3 right-3 z-10">
            <x-storefront.wishlist-button :product="$product" size="sm" />
        </div>

        <!-- Quick Add on Hover (Desktop) -->
        <div class="absolute inset-x-3 bottom-3 z-10 opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 hidden md:block">
            <x-storefront.add-to-cart-button :product="$product" size="{{ $product['sizes'][0] ?? null }}" label="Quick Add" class="!py-2.5 !text-[11px] shadow-sm" />
        </div>
    </div>

    <!-- Product Meta Below Image -->
    <div class="pt-4 flex-1 flex flex-col justify-between">
        <div>
            <!-- Category Tag -->
            <span class="block text-[11px] uppercase tracking-[0.2em] font-medium text-muted mb-1">
                {{ $product['category'] }}
            </span>

            <!-- Title -->
            <h3 class="font-serif text-base sm:text-lg text-ink font-normal tracking-tight group-hover:text-forest transition-colors line-clamp-1">
                <a href="{{ $url }}">
                    {{ $product['name'] }}
                </a>
            </h3>

            <!-- Rating & Reviews -->
            <div class="mt-1.5 flex items-center gap-1.5">
                <x-storefront.rating :rating="$product['rating']" :count="$product['review_count']" size="sm" />
            </div>
        </div>

        <div class="mt-3 flex items-baseline justify-between pt-1 border-t border-beige/40">
            <x-storefront.price-display :price="$product['price']" :compare-at-price="$product['compare_at_price'] ?? null" />

            <!-- Mobile Add Icon -->
            <div class="md:hidden">
                <button 
                    type="button" 
                    x-data 
                    @click.stop.prevent="$store.cart.add({{ htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8') }}, 1, '{{ $product['sizes'][0] ?? null }}', true)"
                    class="w-8 h-8 flex items-center justify-center text-forest hover:bg-forest hover:text-ivory rounded-none border border-forest/30 transition-colors"
                    aria-label="Add {{ $product['name'] }} to bag"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</article>
