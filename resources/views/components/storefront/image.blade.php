@props([
    'src' => '',
    'alt' => 'The Everbloom Botanical Skincare',
    'aspectRatio' => 'aspect-[4/5]',
    'width' => 800,
    'height' => 1000,
    'loading' => 'lazy',
    'class' => '',
    'rounded' => false, // false = rounded-none, true = rounded-full or custom
])

@php
    $realPath = public_path('images/' . ltrim($src, '/'));
    $fileExists = !empty($src) && file_exists($realPath);
    $imageUrl = asset('images/' . ltrim($src, '/'));
    $filename = basename($src);
@endphp

<div {{ $attributes->merge(['class' => "relative overflow-hidden bg-sage-light/60 flex items-center justify-center select-none {$aspectRatio} " . ($rounded ? 'rounded-full' : 'rounded-none') . ' ' . $class]) }}>
    @if($fileExists)
        <img 
            src="{{ $imageUrl }}" 
            alt="{{ $alt }}" 
            width="{{ $width }}" 
            height="{{ $height }}" 
            loading="{{ $loading }}" 
            class="w-full h-full object-cover object-center transition-transform duration-500 hover:scale-105"
        />
    @else
        <!-- Botanical Editorial Placeholder -->
        <div class="absolute inset-0 p-4 flex flex-col items-center justify-center text-center bg-gradient-to-br from-ivory/80 via-sage-light/50 to-beige/40 border border-sage/20 group">
            <div class="w-10 h-10 mb-2 rounded-full bg-forest/5 flex items-center justify-center text-forest/60 group-hover:text-forest transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M12 3v18m-9-9h18M5 5l14 14M5 19L19 5" />
                    <circle cx="12" cy="12" r="3" stroke-width="1.25" />
                </svg>
            </div>
            <p class="font-serif text-xs md:text-sm text-forest/80 font-normal tracking-wide px-2 line-clamp-1">
                {{ $alt }}
            </p>
            <span class="mt-1 font-mono text-[10px] text-muted tracking-tight px-1.5 py-0.5 bg-ivory/80 border border-sage/20 uppercase">
                {{ $filename ?: 'placeholder.jpg' }}
            </span>
        </div>
    @endif
</div>
