@props([
    'currentPage' => 1,
    'totalPages' => 1,
    'baseUrl' => '/shop',
])

@if($totalPages > 1)
    <nav aria-label="Pagination" {{ $attributes->merge(['class' => 'flex items-center justify-center gap-2 py-8']) }}>
        <!-- Previous Page Button -->
        <a 
            href="{{ $currentPage > 1 ? $baseUrl . '?page=' . ($currentPage - 1) : '#' }}" 
            class="px-3 py-2 text-xs uppercase tracking-widest font-medium border border-beige transition-colors {{ $currentPage <= 1 ? 'pointer-events-none opacity-40 text-muted' : 'text-ink hover:border-forest hover:text-forest' }}"
            aria-disabled="{{ $currentPage <= 1 ? 'true' : 'false' }}"
        >
            Previous
        </a>

        <!-- Page Numbers -->
        @for($i = 1; $i <= $totalPages; $i++)
            <a 
                href="{{ $baseUrl . '?page=' . $i }}" 
                class="w-9 h-9 flex items-center justify-center text-xs font-sans font-medium transition-colors border {{ $i === $currentPage ? 'bg-forest text-ivory border-forest' : 'border-beige text-ink hover:border-forest' }}"
                @if($i === $currentPage) aria-current="page" @endif
            >
                {{ $i }}
            </a>
        @endfor

        <!-- Next Page Button -->
        <a 
            href="{{ $currentPage < $totalPages ? $baseUrl . '?page=' . ($currentPage + 1) : '#' }}" 
            class="px-3 py-2 text-xs uppercase tracking-widest font-medium border border-beige transition-colors {{ $currentPage >= $totalPages ? 'pointer-events-none opacity-40 text-muted' : 'text-ink hover:border-forest hover:text-forest' }}"
            aria-disabled="{{ $currentPage >= $totalPages ? 'true' : 'false' }}"
        >
            Next
        </a>
    </nav>
@endif
