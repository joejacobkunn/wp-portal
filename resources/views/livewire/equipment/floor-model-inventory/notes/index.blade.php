<div>
    <div class="card border-light shadow-sm">
        @if (!$addRecord)
            <div class="card-header border-gray-300 p-3 mb-4">
                @can('equipment.floor-model-inventory.manage')
                    <button wire:click="create" class="btn btn-primary btn-lg btn-fab"><i class="fas fa-plus"></i></button>
                @endcan
            </div>
        @endif
        <div class="card-body">
            <div>
                @if ($addRecord)
                    @include('livewire.equipment.floor-model-inventory.notes.partials.form', [
                        'button_text' => 'Add Note',
                        'submit_action' => 'submit',
                        'cancel_action' => 'cancel'
                    ])
                @else
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

                    @include('livewire.equipment.floor-model-inventory.notes.partials.list')
                @endif
            </div>
        </div>
    </div>

    @include('livewire.equipment.floor-model-inventory.notes.partials.edit-model')

    @include('livewire.equipment.floor-model-inventory.notes.partials.delete-model')

</div>
