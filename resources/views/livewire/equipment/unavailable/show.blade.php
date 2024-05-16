<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Unavailable Equipment Info</x-slot>

        <x-slot:description>View equipment details</x-slot>

        <x-slot:content>


            <div class="row px-2">
                <div class="col-sm-4">
                    <div class="card border-light shadow-sm mb-4">
                        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                            <h3 class="h5 mb-0">Equipment Overview</h3>
                        </div>

                        <div class="card-body">

                            @unless (config('sx.mock'))
                                <div class="alert alert-light-secondary color-secondary">
                                    Equipment is in possession by <strong>{{ $this->possessed_by }}</strong>
                                </div>

                                <ul class="list-group list-group-flush">
                                    <li
                                        class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                        <div>
                                            <h3 class="h6 mb-1">Product Name</h3>
                                            <p class="small pe-4">{{ $unavailable_unit->product_name }}</p>
                                        </div>
                                        <div>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">Product Code</h3>
                                            <p class="small pe-4">{{ strtoupper($unavailable_unit->product_code) }}
                                            </p>
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">Serial Number</h3>
                                            <p class="small pe-4">
                                                {{ strtoupper($unavailable_unit->serial_number ?: 'N/A') }}
                                            </p>
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">Base Price</h3>
                                            <p class="small pe-4">${{ number_format($unavailable_unit->base_price, 2) }}
                                            </p>
                                        </div>
                                    </li>

                                    <li
                                        class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                        <div>
                                            <h3 class="h6 mb-1">Warehouse</h3>
                                            <p class="small pe-4">{{ strtoupper($unavailable_unit->whse) }}</p>
                                        </div>
                                        <div>
                                    </li>

                                </ul>
                            @endunless
                        </div>
                    </div>

                </div>

                <div class="col-sm-8">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <h4 class="card-title">Location</h4>
                                @if ($unavailable_unit->current_location)
                                    <div class="alert alert-light-primary color-primary">
                                        <button wire:click='showLocationUpdateModal()'
                                            class="btn btn-sm btn-outline-primary float-end">Update
                                            Location</button>
                                        <i class="fas fa-map-pin"></i>
                                        This equipment is currently located at
                                        <strong>{{ $unavailable_unit->current_location }}</strong>
                                    </div>
                                @else
                                    <div class="alert alert-light-warning color-warning">
                                        <button wire:click='showLocationUpdateModal()'
                                            class="btn btn-sm btn-outline-primary float-end">Update
                                            Location</button>
                                        <i class="fas fa-map-pin"></i>
                                        No location has been set for this equipment
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <livewire:x-activity-log :entity="$unavailable_unit" :key="'activity-' . time()" lazy />

                    <div class="px-2">
                        <livewire:x-comments :entity="$unavailable_unit" :key="'comments' . time()" lazy />
                    </div>

                </div>
            </div>

            @include('livewire.equipment.unavailable.partials.location-modal')

        </x-slot>


    </x-page>
</div>
