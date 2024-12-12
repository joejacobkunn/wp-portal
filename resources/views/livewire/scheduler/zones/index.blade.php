<div>

    <div class="card border-light shadow-sm zones-tab">
        @if (!$addRecord)
            <div class="card-header border-gray-300 p-3 mb-4">
                <button wire:click="create" class="btn btn-primary btn-lg btn-fab"><i class="fas fa-plus"></i></button>
            </div>
        @endif
        <div class="card-body">
            @if (!$addRecord)
                <div class="alert alert-light-primary color-primary"> View and manage zones for
                    <strong>{{ $warehouse->title }}</strong>
                    here
                </div>
            @else
                <div class="alert alert-light-primary color-primary">Add zone for
                    <strong>{{ $warehouse->title }}</strong>
                    here
                </div>
            @endif

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    @if ($addRecord)
                        @include('livewire.scheduler.zones.partials.form', [
                            'button_text' => 'Add New Zone',
                        ])
                    @else
                        <livewire:scheduler.zones.table lazy wire:key="{{ 'zones' }}"
                            whseId="{{ $warehouseId }}">
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>
