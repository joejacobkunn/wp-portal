<div>
    <x-page :breadcrumbs="$breadcrumbs">
        <x-slot:title>Service Area</x-slot>
        <x-slot:description>{{ $this->Titledesc }}</x-slot>
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
                                        class="list-group-item d-flex justify-content-center align-items-center px-0 border-bottom {{ $activeWarehouse->id == $warehouse->id ? 'active' : '' }}">
                                        <div>
                                            <a class="h6 mb-1"
                                                wire:click="changeWarehouse({{ $warehouse->id }})">{{ $warehouse->title }}</a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-10 col-md-10 col-xxl-10">
                    <x-tabs :tabs="$tabs" tabId="service-area-tabs" :key="'tabs' . $activeWarehouse->id">
                        <x-slot:tab_header_zones> Zones <span class="badge badge-lg bg-primary ml-2":key='now()'>
                                {{ $this->ZoneBadgeCount }}</span></x-slot>
                        <x-slot:tab_header_zip_code> ZIP Codes <span class="badge badge-lg bg-primary ml-2":key='now()'>
                                {{ $this->ZipBadgeCount }}</span></x-slot>
                        <x-slot:tab_content_zones component="scheduler.service-area.zones.index" :warehouseId="$activeWarehouse->id" :key="'zones' . $activeWarehouse->id">
                        </x-slot>

                        <x-slot:tab_content_zip_code component="scheduler.service-area.zip-code.index" :warehouseId="$activeWarehouse->id" :key="'zipcode' . $activeWarehouse->id">
                        </x-slot>
                    </x-tabs>


                </div>
            </div>
        </x-slot>
    </x-page>
</div>
