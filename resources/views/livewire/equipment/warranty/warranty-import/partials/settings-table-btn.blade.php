<div class="table-action mb-1 me-2">
    <div class="dropdown-tab text-center" x-data="{ isOpen: false }">
        <button type="button" class="btn btn-outline-secondary dropdown-toggle" @click="isOpen = true">
            <i class="fas fa-cog"></i>
        </button>
        <div x-show="isOpen" @click.away="isOpen = false" class="popup custom-width">
            <div class="popup-content">
                <a class="popup-item d-flex justify-content-between align-items-center" wire:click="export">Export Reports</a>
            </div>
        </div>
    </div>
</div>
