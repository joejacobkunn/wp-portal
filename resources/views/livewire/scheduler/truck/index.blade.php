
<div>
    <x-page :breadcrumbs="$breadcrumbs">
        <x-slot:title>Trucks</x-slot>
        <x-slot:description> {{ !$addRecord ? 'Manage trucks here' : 'Create a new truck here' }}</x-slot>
        <x-slot:content>
            <div class="row">
                <div class="col-2 col-md-2 col-xxl-2">
                    <div class="card border-light shadow-sm mb-4">
                        <div class="card-header bg-primary  border-bottom p-3">
                            <h4 class="h5 mb-0 text-white"><i class="fas fa-warehouse me-2"></i>Warehouses</h4>
                        </div>
                        <div class="card-body warehouse-nav">
                            <ul class="list-group list-group-flush">
                                @foreach ($warehouses->sortBy('title') as $warehouse)
                                    <li
                                        class="list-group-item d-flex justify-content-center align-items-center px-0 border-bottom {{ $activeWarehouse->short == $warehouse->short ? 'active' : '' }}">
                                        <div>
                                            <a class="h6 mb-1"
                                                wire:click="changeWarehouse('{{ $warehouse->short }}')">{{ $warehouse->title }}</a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-10 col-md-10 col-xxl-10"  wire:key="{{'index' . $activeWarehouse->id}}">
                    @if($addRecord)
                        @include('livewire.scheduler.truck.partials.form', ['button_text' => 'Add Truck'])
                    @else

                        @include('livewire.scheduler.truck.partials.listing')
                    @endif

                </div>
            </div>
        </x-slot>
    </x-page>
</div>
