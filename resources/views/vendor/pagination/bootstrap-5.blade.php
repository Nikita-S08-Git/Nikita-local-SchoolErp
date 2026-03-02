@if ($paginator->hasPages())
    <nav aria-label="{{ $attributes->get('aria-label', 'Pagination navigation') }}" role="navigation">
        <ul class="pagination pagination-sm mb-0">
            {{-- First Page Button --}}
            @if ($paginator->currentPage() > 2)
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url(1) }}" aria-label="First">
                        <i class="bi bi-chevron-double-left"></i>
                    </a>
                </li>
            @endif

            {{-- Previous Button --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">
                        <i class="bi bi-chevron-left"></i>
                        <span class="d-none d-sm-inline">Previous</span>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                        <i class="bi bi-chevron-left"></i>
                        <span class="d-none d-sm-inline">Previous</span>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">
                                    <strong>{{ $page }}</strong>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Button --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                        <span class="d-none d-sm-inline">Next</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">
                        <span class="d-none d-sm-inline">Next</span>
                        <i class="bi bi-chevron-right"></i>
                    </span>
                </li>
            @endif

            {{-- Last Page Button --}}
            @if ($paginator->currentPage() < $paginator->lastPage() - 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" aria-label="Last">
                        <i class="bi bi-chevron-double-right"></i>
                    </a>
                </li>
            @endif
        </ul>
    </nav>
@endif
