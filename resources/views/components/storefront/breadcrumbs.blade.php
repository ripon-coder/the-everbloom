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

<nav aria-label="Breadcrumb" {{ $attributes->merge(['class' => 'py-2.5 lg:py-3 text-[13px]']) }}>
    <ol class="flex flex-nowrap sm:flex-wrap items-center gap-1.5 sm:gap-2 text-muted overflow-hidden">
        @foreach($allItems as $index => $item)
            @php $isLast = $loop->last; @endphp
            <li class="inline-flex items-center gap-1.5 sm:gap-2 {{ $isLast ? 'min-w-0 flex-1 sm:flex-initial' : 'shrink-0' }}">
                @if(!$loop->first)
                    <span class="text-beige-dark select-none" aria-hidden="true">/</span>
                @endif

                @if($isLast || empty($item['url']))
                    <span class="text-ink font-medium truncate block" aria-current="page">
                        {{ $item['label'] }}
                    </span>
                @else
                    <a href="{{ $item['url'] }}" class="hover:text-forest transition-colors font-normal whitespace-nowrap">
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
