@props([
    'variant' => 'primary', // primary, secondary, tertiary, dark, ghost
    'size' => 'md', // sm, md, lg
    'href' => null,
    'type' => 'button',
    'class' => '',
])

@php
    $baseStyles = "inline-flex items-center justify-center font-sans tracking-[0.2em] uppercase font-medium transition-all duration-200 select-none focus-visible:outline-none focus-visible:border-forest focus-visible:shadow-[0_0_0_3px_rgba(48,76,61,0.12)] disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer";
    
    $sizeStyles = match($size) {
        'sm' => 'text-xs px-4 py-2.5 min-h-[44px] rounded-sm',
        'lg' => 'text-xs px-8 py-4 min-h-[50px] rounded-sm',
        default => 'text-xs px-6 py-3 min-h-[44px] rounded-sm',
    };

    $variantStyles = match($variant) {
        'secondary' => 'bg-transparent text-forest border border-forest hover:bg-forest hover:text-ivory',
        'tertiary' => 'bg-transparent text-forest border-b border-forest hover:text-ink hover:border-ink !px-0 !py-1 !min-h-[32px] rounded-none',
        'dark' => 'bg-ink text-ivory border border-ink hover:bg-forest hover:border-forest',
        'ghost' => 'bg-transparent text-ink hover:text-forest !px-3 !py-2 !min-h-[44px] rounded-sm',
        default => 'bg-forest text-ivory border border-forest hover:bg-[#203329] hover:border-[#203329]',
    };

    $classes = "{$baseStyles} {$sizeStyles} {$variantStyles} {$class}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
