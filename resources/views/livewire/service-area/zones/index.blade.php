<div class="card border-light shadow-sm warranty-tab">
    @if (!$addRecord)
        <div class="card-header border-gray-300 p-3 mb-4">
            @can('equipment.floor-model-inventory.manage')
                <button wire:click="create" class="btn btn-primary btn-lg btn-fab"><i
                        class="fas fa-plus"></i></button>
            @endcan
        </div>
    @endif
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                @if ($addRecord)
                    @include('livewire.service-area.zones.partials.form', [
                        'button_text' => 'Add New Zone',
                    ])
                @else

                @endif

            </div>
        </div>

    </div>
</div>
