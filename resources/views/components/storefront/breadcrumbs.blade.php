@props([
    'items' => [],
])

@php
    $allItems = array_merge([
        ['label' => 'Home', 'url' => '/']
    ], $items);

    $breadcrumbElements = [];
    foreach ($allItems as $index => $item) {
        $breadcrumbElements[] = [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $item['label'],
            'item' => url($item['url'] ?? ''),
        ];
    }
@endphp

<nav aria-label="Breadcrumb" {{ $attributes->merge(['class' => 'py-3 text-xs']) }}>
    <ol class="flex flex-wrap items-center gap-2 text-muted">
        @foreach($allItems as $index => $item)
            @php $isLast = $loop->last; @endphp
            <li class="inline-flex items-center gap-2">
                @if(!$loop->first)
                    <span class="text-beige-dark select-none" aria-hidden="true">/</span>
                @endif

                @if($isLast || empty($item['url']))
                    <span class="text-ink font-medium truncate max-w-[200px] sm:max-w-none" aria-current="page">
                        {{ $item['label'] }}
                    </span>
                @else
                    <a href="{{ $item['url'] }}" class="hover:text-forest transition-colors font-normal">
                        {{ $item['label'] }}
                    </a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>

@push('structured-data')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => $breadcrumbElements,
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush
