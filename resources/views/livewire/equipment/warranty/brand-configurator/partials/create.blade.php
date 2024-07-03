<div class="card border-light shadow-sm warranty-tab">
    <div class="card-header border-gray-300 mt-4 p-3 mb-md-0">
        @if (!$addRecord)
            @can('equipment.warranty.manage')
                <button wire:click="create()" class="btn btn-primary btn-lg btn-fab"><i class="fas fa-plus"></i></button>
            @endcan
        @endif
    </div>

    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                @if ($addRecord)
                    @include('livewire.equipment.warranty.brand-configurator.partials.form', [
                        'button_text' => 'Add Brand',
                    ])
                @else
                    <livewire:equipment.warranty.brand-configurator.warranty-table lazy>
                @endif

            </div>
        </div>

    </div>
</div>
