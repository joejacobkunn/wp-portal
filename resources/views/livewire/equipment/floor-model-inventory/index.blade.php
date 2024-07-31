
<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Floor Model Inventory</x-slot>
        <x-slot:description>{{ $page }}</x-slot>
        <x-slot:content>
                <div class="card border-light shadow-sm warranty-tab">
                    @if(!$addRecord)
                        <div class="card-header border-gray-300 p-3 mb-4">
                                @can('equipment.warranty.manage')
                                    <button wire:click="create" class="btn btn-primary btn-lg btn-fab"><i class="fas fa-plus"></i></button>
                                @endcan
                        </div>
                    @endif
                    <div class="card-body">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                @if ($addRecord)
                                    @include('livewire.equipment.floor-model-inventory.partials.form', [
                                        'button_text' => 'Add Inventory',
                                    ])
                                @else
                                    <livewire:equipment.floor-model-inventory.table lazy>
                                @endif

                            </div>
                        </div>

                    </div>
                </div>
        </x-slot>
    </x-page>
</div>
