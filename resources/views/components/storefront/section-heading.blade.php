@props([
    'eyebrow' => null,
    'title' => '',
    'description' => null,
    'centered' => false,
    'linkText' => null,
    'linkUrl' => null,
    'tag' => 'h2',
])

<div {{ $attributes->merge(['class' => 'mb-8 md:mb-12 ' . ($centered ? 'text-center max-w-2xl mx-auto' : 'flex flex-col md:flex-row md:items-end md:justify-between gap-4')]) }}>
    <div>
        @if($eyebrow)
            <span class="block text-xs uppercase tracking-[0.2em] font-medium text-forest mb-2">
                {{ $eyebrow }}
            </span>
        @endif

        <{{ $tag }} class="font-serif text-2xl sm:text-3xl md:text-4xl text-ink font-normal tracking-tight">
            {{ $title }}
        </{{ $tag }}>

        @if($description)
            <p class="mt-3 text-sm md:text-base text-muted max-w-xl leading-relaxed">
                {{ $description }}
            </p>
        @endif
    </div>

    @if($linkText && $linkUrl)
        <div class="{{ $centered ? 'mt-4' : 'shrink-0' }}">
            <a href="{{ $linkUrl }}" class="inline-flex items-center gap-1.5 text-xs uppercase tracking-[0.2em] font-medium text-forest hover:text-ink transition-colors border-b border-forest pb-0.5 group">
                <span>{{ $linkText }}</span>
                <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    @endif
</div>
