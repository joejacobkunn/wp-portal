@if ($paginator->hasPages())
    <div class="d-flex justify-content-between align-items-center mb-2">
        <!-- Pagination Info -->
        <div class="d-flex align-items-center">
            <span>{{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} of {{ $paginator->total() }}</span>

            <!-- Previous Page Link -->
            @if ($paginator->onFirstPage())
                <button class="btn btn-light ms-2" disabled>
                    <i class="bi bi-chevron-left"></i>
                </button>
            @else
                <button class="btn btn-light ms-2" wire:click="previousPage">
                    <i class="bi bi-chevron-left"></i>
                </button>
            @endif

            <!-- Next Page Link -->
            @if ($paginator->hasMorePages())
                <button class="btn btn-light" wire:click="nextPage">
                    <i class="bi bi-chevron-right"></i>
                </button>
            @else
                <button class="btn btn-light" disabled>
                    <i class="bi bi-chevron-right"></i>
                </button>
            @endif
        </div>
    </div>
@endif
