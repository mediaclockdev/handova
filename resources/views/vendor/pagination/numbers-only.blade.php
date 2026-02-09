@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-6">
        <ul class="flex items-center space-x-1 list-style-none gap-3">

            {{-- Previous Button --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span
                        class="px-3 py-2 text-sm text-gray-400 bg-gray-100 border rounded cursor-not-allowed">
                        Prev
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}"
                       class="px-3 py-2 text-sm text-gray-700 bg-white border rounded hover:bg-gray-100 no-underline">
                        Prev
                    </a>
                </li>
            @endif

            {{-- Pagination Numbers --}}
            @foreach ($elements as $element)
                {{-- Dots --}}
                @if (is_string($element))
                    <li>
                        <span class="px-3 py-2 text-sm text-gray-400">
                            {{ $element }}
                        </span>
                    </li>
                @endif

                {{-- Page Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span
                                    class="px-3 py-2 text-sm font-semibold text-gray-700 bg-indigo-600 border border-indigo-600 rounded no-underline">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}"
                                   class="px-3 py-2 text-sm text-gray-700 bg-white border rounded hover:bg-indigo-50 hover:text-indigo-600 no-underline">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Button --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}"
                       class="px-3 py-2 text-sm text-gray-700 bg-white border rounded hover:bg-gray-100 no-underline">
                        Next
                    </a>
                </li>
            @else
                <li>
                    <span
                        class="px-3 py-2 text-sm text-gray-400 bg-gray-100 border rounded cursor-not-allowed no-underline">
                        Next
                    </span>
                </li>
            @endif

        </ul>
    </nav>
@endif
