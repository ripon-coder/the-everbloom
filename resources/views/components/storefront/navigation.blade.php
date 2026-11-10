@props([
    'items' => config('demo.navigation.main', []),
])

<nav class="flex items-center justify-center flex-wrap gap-x-8 gap-y-2" aria-label="Primary Navigation">
    @foreach($items as $item)
        @php
            $isActive = request()->is(ltrim($item['url'], '/')) || (request()->is('shop*') && $item['url'] === '/shop');
        @endphp
        <a 
            href="{{ $item['url'] }}" 
            class="text-xs uppercase tracking-[0.2em] font-medium py-1 transition-colors relative {{ $isActive ? 'text-forest font-semibold' : 'text-ink/80 hover:text-forest' }}"
            @if($isActive) aria-current="page" @endif
        >
            {{ $item['label'] }}
            @if($isActive)
                <span class="absolute -bottom-1 inset-x-0 h-0.5 bg-forest rounded-full"></span>
            @endif
        </a>
    @endforeach
</nav>
