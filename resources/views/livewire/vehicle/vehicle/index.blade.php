<div>

    <x-page
        :breadcrumbs="$breadcrumbs"
    >

        <x-slot:title>Vehicles</x-slot>

        <x-slot:description>
            {{ !$addRecord ? 'Manage vehicles here' : 'Create a new vehicle here' }}
        </x-slot>

        <x-slot:content>
            
            @if($addRecord)
                @include('livewire.vehicle.vehicle.partials.form', ['button_text' => 'Add Vehicle'])
            @else
                <div>
                    <div class="card border-light shadow-sm mb-4" style="min-height: 600px">
                        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">

                            @can('vehicle.manage')
                                <button wire:click="create()" class="btn btn-success btn-lg btn-fab"><i class="fas fa-plus"></i></button>
                            @endcan

                            <h3 class="h5 mb-0">Vehicle List for {{$account->name}}</h3>
                        </div>

                        <div class="card-body">
                            <livewire:vehicle.vehicle.table :account="$account"/>
                        </div>
                    </div>
                </div>

            @endif
        </x-slot>

    </x-page>

</div>




