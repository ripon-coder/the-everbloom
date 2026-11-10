@props([
    'price' => 0,
    'compareAtPrice' => null,
    'size' => 'md',
    'class' => '',
])

@php
    $currency = config('demo.site.currency.symbol', '$');
    $formattedPrice = $currency . number_format((float)$price, 2);
    $formattedCompare = $compareAtPrice ? $currency . number_format((float)$compareAtPrice, 2) : null;
    $hasDiscount = !empty($compareAtPrice) && $compareAtPrice > $price;

    $sizeClasses = match($size) {
        'sm' => 'text-xs',
        'lg' => 'text-xl md:text-2xl',
        default => 'text-sm md:text-base',
    };
@endphp

<div {{ $attributes->merge(['class' => "inline-flex items-baseline gap-2 font-sans font-medium text-ink {$sizeClasses} {$class}"]) }}>
    <span class="tracking-tight text-ink font-semibold">
        {{ $formattedPrice }}
    </span>

    @if($hasDiscount)
        <span class="text-xs md:text-sm text-muted line-through font-normal">
            {{ $formattedCompare }}
        </span>
    @endif
</div>
