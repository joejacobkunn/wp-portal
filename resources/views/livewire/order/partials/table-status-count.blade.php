<div class="btn-group btn-group-sm me-2" role="group" aria-label="Basic outlined example">
    <button type="button" wire:click="setFilterValue('status', 'Pending Review')" class="btn btn-primary">
        Pending Review <span class="badge bg-transparent">{{ $pending_review_count }}</span>
    </button>
    <button type="button" wire:click="setFilterValue('status', 'Follow Up')" class="btn btn-info">Follow Up <span
            class="badge bg-transparent">{{ $follow_up_count }}</span></button>
    <button type="button" wire:click="setFilterValue('status', 'Ignored')" class="btn btn-secondary">Ignored <span
            class="badge bg-transparent">{{ $ignored_count }}</span></button>
</div>
