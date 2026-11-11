@props([
    'src' => '',
    'alt' => 'The Everbloom Botanical Skincare',
    'aspectRatio' => 'aspect-[4/5]',
    'width' => 800,
    'height' => 1000,
    'loading' => 'lazy',
    'class' => '',
    'rounded' => false, // false = rounded-none, true = rounded-full
])

@php
    $isDynamic = $attributes->has(':src') || $attributes->has('x-bind:src');
    $dynamicSrc = $attributes->get(':src') ?? $attributes->get('x-bind:src');
    $dynamicAlt = $attributes->get(':alt') ?? $attributes->get('x-bind:alt');

    $realPath = (!$isDynamic && !empty($src)) ? public_path('images/' . ltrim($src, '/')) : null;
    $fileExists = $realPath && file_exists($realPath);
    $imageUrl = $fileExists ? asset('images/' . ltrim($src, '/')) : ($src ? asset(ltrim($src, '/')) : '');
    $filename = basename($src);
@endphp

@if($isDynamic)
    <div 
        x-data="{ imgError: false }"
        {{ $attributes->except([':src', 'x-bind:src', ':alt', 'x-bind:alt'])->merge(['class' => "relative overflow-hidden bg-sage-light/60 flex items-center justify-center select-none {$aspectRatio} " . ($rounded ? 'rounded-full' : 'rounded-none') . ' ' . $class]) }}
    >
        <img 
            :src="{{ $dynamicSrc }}" 
            @if($dynamicAlt) :alt="{{ $dynamicAlt }}" @else alt="{{ $alt }}" @endif
            width="{{ $width }}" 
            height="{{ $height }}" 
            loading="{{ $loading }}" 
            x-on:error="imgError = true"
            x-show="!imgError"
            class="w-full h-full object-cover object-center transition-transform duration-300 hover:scale-105"
        />
        <!-- Botanical Editorial Placeholder Fallback on Error -->
        <div x-show="imgError" x-cloak class="absolute inset-0 p-2 flex flex-col items-center justify-center text-center bg-gradient-to-br from-ivory/80 via-sage-light/50 to-beige/40 border border-[#E3E6DD] group">
            <div class="w-8 h-8 rounded-full bg-forest/5 flex items-center justify-center text-forest/60 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M12 3v18m-9-9h18M5 5l14 14M5 19L19 5" />
                    <circle cx="12" cy="12" r="3" stroke-width="1.25" />
                </svg>
            </div>
            <span class="font-serif text-xs text-forest/80 line-clamp-1" @if($dynamicAlt) x-text="{{ $dynamicAlt }}" @endif>
                {{ $alt }}
            </span>
        </div>
    </div>
@else
    <div 
        x-data="{ imgError: false }"
        {{ $attributes->merge(['class' => "relative overflow-hidden bg-sage-light/60 flex items-center justify-center select-none {$aspectRatio} " . ($rounded ? 'rounded-full' : 'rounded-none') . ' ' . $class]) }}
    >
        @if($fileExists)
            <img 
                src="{{ $imageUrl }}" 
                alt="{{ $alt }}" 
                width="{{ $width }}" 
                height="{{ $height }}" 
                loading="{{ $loading }}" 
                x-on:error="imgError = true"
                x-show="!imgError"
                class="w-full h-full object-cover object-center transition-transform duration-300 hover:scale-105"
            />
            <!-- Botanical Editorial Placeholder on Error -->
            <div x-show="imgError" x-cloak class="absolute inset-0 p-4 flex flex-col items-center justify-center text-center bg-gradient-to-br from-ivory/80 via-sage-light/50 to-beige/40 border border-[#E3E6DD] group">
                <div class="w-10 h-10 mb-2 rounded-full bg-forest/5 flex items-center justify-center text-forest/60 group-hover:text-forest transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M12 3v18m-9-9h18M5 5l14 14M5 19L19 5" />
                        <circle cx="12" cy="12" r="3" stroke-width="1.25" />
                    </svg>
                </div>
                <p class="font-serif text-xs md:text-sm text-forest/80 font-normal tracking-wide px-2 line-clamp-1">
                    {{ $alt }}
                </p>
                <span class="mt-1 font-mono text-xs text-muted tracking-tight px-2 py-0.5 bg-ivory/90 border border-[#E3E6DD] uppercase">
                    {{ $filename ?: 'placeholder.jpg' }}
                </span>
            </div>
        @else
            <!-- Botanical Editorial Placeholder -->
            <div class="absolute inset-0 p-4 flex flex-col items-center justify-center text-center bg-gradient-to-br from-ivory/80 via-sage-light/50 to-beige/40 border border-[#E3E6DD] group">
                <div class="w-10 h-10 mb-2 rounded-full bg-forest/5 flex items-center justify-center text-forest/60 group-hover:text-forest transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M12 3v18m-9-9h18M5 5l14 14M5 19L19 5" />
                        <circle cx="12" cy="12" r="3" stroke-width="1.25" />
                    </svg>
                </div>
                <p class="font-serif text-xs md:text-sm text-forest/80 font-normal tracking-wide px-2 line-clamp-1">
                    {{ $alt }}
                </p>
                <span class="mt-1 font-mono text-xs text-muted tracking-tight px-2 py-0.5 bg-ivory/90 border border-[#E3E6DD] uppercase">
                    {{ $filename ?: 'placeholder.jpg' }}
                </span>
            </div>
        @endif
    </div>
@endif
