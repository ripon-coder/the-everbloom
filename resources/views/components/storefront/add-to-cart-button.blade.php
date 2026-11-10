@props([
    'product',
    'size' => null,
    'quantity' => 1,
    'variant' => 'primary',
    'label' => 'Add to Bag',
    'class' => '',
])

@php
    $productJson = htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8');
@endphp

<button 
    type="button" 
    x-data="{ adding: false }"
    @click.stop.prevent="
        adding = true;
        $store.cart.add({{ $productJson }}, {{ $quantity }}, '{{ $size }}', true);
        setTimeout(() => adding = false, 800);
    "
    :disabled="adding || '{{ $product['stock_status'] ?? 'in_stock' }}' === 'out_of_stock'"
    {{ $attributes->merge(['class' => ($variant === 'secondary' ? 'btn-secondary' : 'btn-primary') . " w-full relative overflow-hidden transition-all {$class}"]) }}
>
    <!-- Normal state -->
    <span x-show="!adding" class="inline-flex items-center gap-2">
        @if(($product['stock_status'] ?? '') === 'out_of_stock')
            <span>Sold Out</span>
        @else
            <svg class="w-4 h-4 text-inherit" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <span>{{ $label }}</span>
        @endif
    </span>

    <!-- Adding feedback -->
    <span x-show="adding" x-cloak class="inline-flex items-center gap-2">
        <svg class="w-4 h-4 animate-spin text-inherit" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Added to Bag</span>
    </span>
</button>
