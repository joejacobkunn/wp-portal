@include('livewire.equipment.floor-model-inventory.notes.partials.filter')

<div class="inventory-card-list">
    @forelse($notes as $note)
        <div class="card border mb-2">
            <div class="card-body ">
                <div class="row d-block d-sm-flex">
                    <div class="col">
                        <p>{{ $note->note }}</p>
                        <span class="badge warehouse-badge">
                            <i class="fas fa-warehouse me-1"></i> {{ $note->warehouse?->title ?? '-' }}
                        </span>
                    </div>
                    @can('update', $note)
                        <div class="col-auto float-right">
                            <button wire:click="edit({{ $note->id }})" type="button"
                                class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-pen" aria-hidden="true"></i>
                            </button>
                            <button wire:click="delete({{ $note->id }})" type="button"
                                class="btn btn-sm btn-outline-danger">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                        </div>
                    @endcan
                </div>

            </div>
            <div class="card-footer">
                <div class="row d-block d-sm-flex small text-muted">
                    <div class="col">
                        <p class="mb-0">{{ $note->user?->name ?? '-' }}</p>
                    </div>
                    <div class="col-auto float-right">
                        <i class="far fa-clock small"></i>
                        {{ $note->created_at?->diffForHumans() }}
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card card-body border">
            <div class="text-center m-5">No Notes Found</div>
        </div>
    @endforelse

    {{ $notes->links() }}
</div>
