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

                <div class="popup-item d-flex justify-content-between align-items-center">
                    <span>Save Last Filter State</span>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault"
                            wire:model="isFilterSaved"
                            wire:change="saveFilter"
                            @if($this->isFilterSaved) {{'checked'}} @endif
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

