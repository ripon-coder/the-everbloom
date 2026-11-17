@props([
    'price' => 0,
    'compareAtPrice' => null,
    'size' => 'md',
    'class' => '',
])

@php
    $formattedPrice = \App\Support\Currency::format($price);
    $formattedCompare = $compareAtPrice ? \App\Support\Currency::format($compareAtPrice) : null;
    $hasDiscount = !empty($compareAtPrice) && $compareAtPrice > $price;

    $sizeClasses = match($size) {
        'sm' => 'text-xs',
        'lg' => 'text-xl md:text-2xl',
        default => 'text-sm md:text-base',
    };
@endphp

<div {{ $attributes->merge(['class' => "price lining-nums inline-flex items-baseline gap-2 font-sans font-medium text-ink {$sizeClasses} {$class}"]) }}>
    <span class="price tracking-tight text-ink font-medium">
        {{ $formattedPrice }}
    </span>

    @if($hasDiscount)
        <span class="price text-xs md:text-sm text-muted line-through font-normal">
            {{ $formattedCompare }}
        </span>
    @endif
</div>
