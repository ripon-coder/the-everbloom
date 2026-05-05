@if ($paginator->hasPages())
<nav aria-label="Page navigation example">
    <ul class="inline-flex -space-x-px text-sm">
        {{-- Previous Page Link --}}
        <li>
            @if ($paginator->onFirstPage())
                <span
                    class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg cursor-default dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    Previous
                </a>
            @endif
        </li>

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li>
                    <span
                        class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 cursor-default dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400">
                        {{ $element }}
                    </span>
                </li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    <li>
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                class="flex items-center justify-center px-3 h-8 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                {{ $page }}
                            </a>
                        @endif
                    </li>
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        <li>
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    Next
                </a>
            @else
                <span
                    class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg cursor-default dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400">
                    Next
                </span>
            @endif
        </li>
    </ul>
</nav>
@endif
