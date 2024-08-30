<div class="table-action mb-1 me-2">
    <div class="dropdown-tab text-center" x-data="{ isOpen: false }">
        <button type="button" class="btn btn-outline-secondary dropdown-toggle"  @click="isOpen = true">
            <i class="fas fa-cog"></i>
        </button>
        <div x-show="isOpen" @click.away="isOpen = false" class="popup custom-width">
            <div class="popup-content">
                <a class="popup-item" wire:navigate href="{{ route('order.email-template.index') }}">Templates</a>
                <a class="popup-item" @click="isOpen = false" wire:click="$toggle('enableEcomZwhs')">Show ECOM and ZWHS :
                    @if (!$this->enableEcomZwhs)
                        <span class="badge bg-light-danger">No</span>
                    @else
                        <span class="badge bg-light-success">Yes</span>
                    @endif
                </a>
            </div>
        </div>
    </div>
</div>

