<div class="row">
    <div class="col-md-4 col-sm-12">
        <div class="form-group x-input">
            <div class="input-group">
                <input type="text" class="form-control"
                    placeholder="Search" wire:model.live.debounce.800ms="searchText">
            </div>
        </div>
    </div>
</div>

@forelse($notes as $note)
    <div class="card card-body border mb-2">
        <div class="row d-block d-sm-flex">
            <div class="col-auto mb-3 mb-sm-0">
                <div class="avatar avatar-lg bg-primary">
                    <span class="avatar-content">{{ $note->user?->abbreviation ?? '-' }}</span>
                </div>
            </div>
            <div class="col">
                <h6 class="h6 mb-1">{{ $note->user?->name ?? '-' }}</h6>
                <p>{{ $note->note }}</p>
                <div class="small text-muted mt-2">{{ $note->created_at?->diffForHumans() }}</div>
            </div>
            @can('update', $note)
                <div class="col-auto float-right">
                    <button wire:click="edit({{ $note->id }})" type="button"
                        class="btn btn-outline-primary">
                        <i class="fa fa-pen" aria-hidden="true"></i>
                    </button>
                    <button wire:click="delete({{ $note->id }})" type="button"
                        class="btn btn-outline-danger">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                </div>
            @endcan
        </div>
    </div>

@empty
    <div class="card card-body border">
        <div class="text-center m-5">No Notes Found</div>
    </div>
@endforelse

{{ $notes->links() }}