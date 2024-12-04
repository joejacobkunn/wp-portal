
<div>
    <x-page :breadcrumbs="$breadcrumbs">
        <x-slot:title>Service Area</x-slot>
        <x-slot:description>{{ $this->Titledesc }}</x-slot>
        <x-slot:content>
            <div class="row">
                <div class="col-12 col-md-3 col-xxl-3">
                    <div class="card border-light shadow-sm mb-4">
                        <div class="card-header bg-primary  border-bottom p-3">
                            <h3 class="h5 mb-0 text-white"><i class="fas fa-warehouse me-2"></i>Warehouses</h3>
                        </div>
                        <div class="card-body warehouse-nav">
                            <ul class="list-group list-group-flush">
                                @foreach ($warehouses as $warehouse)
                                <li class="list-group-item d-flex justify-content-center align-items-center px-0 border-bottom {{ $activeWarehouse == $warehouse->id ? 'active' : '' }}">
                                    <div>
                                        <a class="h6 mb-1" wire:click="changeWarehouse({{$warehouse->id}})">{{$warehouse->title}}</a>
                                    </div>
                                </li>

                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-8 col-md-8 col-xxl-8">
                        <x-tabs :tabs="$tabs" tabId="service-area-tabs" :key="'tabs'.$this->activeWarehouse">
                            <x-slot:tab_content_zones component="service-area.zones.index" WarehouseId="{{$activeWarehouse}}" :key="'zones'.$this->activeWarehouse">
                            </x-slot>

                            <x-slot:tab_content_zones component="service-area.zones.index" wire:key="zipcodes">
                            </x-slot>
                        </x-tabs>

                </div>
            </div>
        </x-slot>
    </x-page>
</div>
