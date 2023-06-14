<div>
    <div class="card border-light shadow-sm mb-4">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0" :key="'bew' . time()">
            @if (!$addLocation)
                <button wire:click="create()" class="btn btn-success btn-sm float-end"><i class="fa-solid fa-plus"></i> Add Location</button>
            @endif
            <h3 class="h5 mb-0"><i class="fas fa-bars me-1"></i> Locations</h3>
        </div>

        <div class="card-body">

            @if ($addLocation)
                @include('livewire.core.location.partials.form', ['button_text' => 'Add Location'])
            @elseif ($viewLocation)
                @include('livewire.core.location.partials.view')

            @else
                <div class="alert alert-light-info color-info">
                    <i class="bi bi-info-circle"></i> See {{$account->name}} locations here
                </div>

                <livewire:core.location.table :account="$account"/>
            @endif

            <hr />

            <div>
            </div>
        </div>
    </div>
</div>
