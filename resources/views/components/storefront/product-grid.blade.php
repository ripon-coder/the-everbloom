@props([
    'products' => [],
    'columns' => '4', // 2, 3, 4
])

@php
    $gridCols = match($columns) {
        '2' => 'grid-cols-2',
        '3' => 'grid-cols-2 md:grid-cols-3',
        default => 'grid-cols-2 md:grid-cols-3 lg:grid-cols-4',
    };
@endphp

@if(count($products) > 0)
    <div {{ $attributes->merge(['class' => "grid {$gridCols} gap-x-4 gap-y-10 sm:gap-x-6 sm:gap-y-12 lg:gap-x-8 lg:gap-y-14"]) }}>
        @foreach($products as $product)
            <x-storefront.product-card :product="$product" />
        @endforeach
    </div>
@else
    <div class="py-16 text-center border border-dashed border-beige bg-ivory/50 p-8">
        <svg class="w-10 h-10 mx-auto text-muted mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <p class="font-serif text-lg text-ink font-normal">No botanical products found</p>
        <p class="text-xs text-muted mt-1 uppercase tracking-widest">Try adjusting your filters or search terms</p>
    </div>
@endif
