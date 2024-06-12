<div class="btn-group mb-1 me-2">
    <div class="dropdown">
        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-cog"></i>
        </button>
        <div class="dropdown-menu">
            <a class="dropdown-item" wire:navigate href="{{ route('order.email-template.index') }}">Templates</a>
            <a class="dropdown-item" wire:click="$toggle('enableEcomZwhs')">Show ECOM and ZWHS :
                @if (!$this->enableEcomZwhs)
                    <span class="badge bg-light-danger">No</span>
                @else
                    <span class="badge bg-light-success">Yes</span>
                @endif
            </a>
        </div>
    </div>
</div>
