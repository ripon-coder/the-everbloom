@props([
    'product' => null,
    'productId' => null,
    'size' => 'md',
    'class' => '',
])

@php
    $id = $product['id'] ?? $productId;
    $productName = $product['name'] ?? 'item';
    $buttonSize = match($size) {
        'sm' => 'w-11 h-11 min-w-[44px] min-h-[44px]',
        'lg' => 'w-12 h-12 min-w-[44px] min-h-[44px]',
        default => 'w-11 h-11 min-w-[44px] min-h-[44px]',
    };
    $iconSize = match($size) {
        'sm' => 'w-4 h-4',
        'lg' => 'w-5 h-5',
        default => 'w-5 h-5',
    };
    $productJson = $product ? htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8') : null;
@endphp

<button 
    type="button" 
    x-data
    @click.stop.prevent="
        @if($productJson)
            $store.wishlist.toggle({{ $productJson }})
        @else
            $store.wishlist.toggle('{{ $id }}')
        @endif
    "
    :aria-label="$store.wishlist.has('{{ $id }}') ? 'Remove {{ addslashes($productName) }} from wishlist' : 'Save {{ addslashes($productName) }} to wishlist'"
    :aria-pressed="$store.wishlist.has('{{ $id }}') ? 'true' : 'false'"
    :class="$store.wishlist.has('{{ $id }}') ? 'text-forest bg-ivory border-forest shadow-sm' : 'text-ink/80 hover:text-forest bg-ivory/90 hover:bg-ivory border-[#E3E6DD] hover:border-[#A8B7A0]'"
    {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full border transition-all duration-200 focus-visible:outline-none focus-visible:border-forest focus-visible:shadow-[0_0_0_3px_rgba(48,76,61,0.12)] cursor-pointer {$buttonSize} {$class}"]) }}
>
    <!-- Filled Heart (Active) -->
    <svg 
        x-show="$store.wishlist.has('{{ $id }}')" 
        class="{{ $iconSize }} fill-current" 
        viewBox="0 0 24 24" 
        aria-hidden="true"
    >
        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
    </svg>

    <!-- Outlined Heart (Inactive) -->
    <svg 
        x-show="!$store.wishlist.has('{{ $id }}')" 
        class="{{ $iconSize }}" 
        fill="none" 
        stroke="currentColor" 
        stroke-width="1.75" 
        viewBox="0 0 24 24" 
        aria-hidden="true"
    >
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
    </svg>
</button>
