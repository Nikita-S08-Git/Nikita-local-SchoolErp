{{-- Custom Pagination Component --}}
{{-- Usage: <x-pagination :paginator="$items" /> --}}
@if($paginator instanceof \Illuminate\Pagination\LengthAwarePaginator && $paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $total = $paginator->total();
        $from = $paginator->firstItem() ?? 1;
        $to = $paginator->lastItem() ?? $total;
    @endphp
    
    <div class="pagination-wrapper d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4">
        <!-- Pagination Info -->
        <div class="pagination-info">
            <span class="text-muted">
                Showing <strong class="text-primary">{{ $from }}</strong> to 
                <strong class="text-primary">{{ $to }}</strong> of 
                <strong class="text-primary">{{ $total }}</strong> entries
            </span>
        </div>
        
        <!-- Pagination Controls -->
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-bordered pagination-shadow mb-0">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="bi bi-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements - Using simple number approach --}}
                @for ($i = 1; $i <= $lastPage; $i++)
                    @if ($i == $currentPage)
                        <li class="page-item active">
                            <span class="page-link">{{ $i }}</span>
                        </li>
                    @else
                        @if (
                            $i == 1 || 
                            $i == $lastPage || 
                            ($i >= $currentPage - 2 && $i <= $currentPage + 2)
                        )
                            <li class="page-item">
                                <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                            </li>
                        @elseif (
                            $i == $currentPage - 3 || 
                            $i == $currentPage + 3
                        )
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                    @endif
                @endfor

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif
