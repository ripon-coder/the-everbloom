@props([
    'rating' => 5.0,
    'count' => null,
    'showCount' => true,
    'size' => 'sm',
])

@php
    // Star size increased to 12-13px (w-[13px] h-[13px] for sm, w-4 h-4 for md)
    $starSize = $size === 'md' ? 'w-4 h-4' : 'w-[13px] h-[13px]';
    $roundedRating = round((float)$rating, 1);
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5']) }} aria-label="Rated {{ $roundedRating }} out of 5 stars">
    <div class="flex items-center text-[#B88746]" aria-hidden="true">
        @for($i = 1; $i <= 5; $i++)
            @if($i <= floor($roundedRating))
                <!-- Full Star (13px) -->
                <svg class="{{ $starSize }} fill-current shrink-0" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            @elseif($i - $roundedRating < 0.75 && $i - $roundedRating > 0.25)
                <!-- Half Star (13px) -->
                <svg class="{{ $starSize }} shrink-0" viewBox="0 0 20 20">
                    <defs>
                        <linearGradient id="halfStar-{{ $i }}">
                            <stop offset="50%" stop-color="#B88746"/>
                            <stop offset="50%" stop-color="#E8DFD0"/>
                        </linearGradient>
                    </defs>
                    <path fill="url(#halfStar-{{ $i }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            @else
                <!-- Empty Star (13px) -->
                <svg class="{{ $starSize }} text-beige fill-current shrink-0" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            @endif
        @endfor
    </div>

    @if($showCount && $count !== null)
        <span class="text-xs text-muted font-sans font-normal tracking-tight">
            ({{ $count }})
        </span>
    @endif
</div>
