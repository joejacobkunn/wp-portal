<div class="row px-2">
    <div class="card border-light shadow-sm mb-4">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
            <livewire:component.action-button :actionButtons="$actionButtons">
                <h3 class="h5 mb-0">Vehicle Overview</h3>
        </div>

        <div class="card-body">

            @if(is_null($vehicle->retired_at))
                <div class="alert alert-light-success color-success" role="alert">

                    @can('vehicle.manage')
                        <button wire:click="$toggle('retire_modal')" type="button" class="btn btn-sm btn-outline-danger float-end my-n1" wire:key="is_active_{{ now() }}">
                            <div wire:loading wire:target="$toggle('retire_modal')">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </div>
                            Retire
                        </button>
                    @endcan

                    <i class="fa fa-check" aria-hidden="true"></i> This Vehicle is Active
                </div>
            @else
                <div class="alert alert-light-danger color-danger" role="alert">
                    @can('vehicle.manage')
                        <button wire:click="activate()" type="button" class="btn btn-sm btn-outline-success float-end my-n1" wire:key="is_active_{{ now() }}">
                            <div wire:loading wire:target="activate">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </div>
                            Activate
                        </button>
                    @endcan
                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> This Vehicle was Retired on {{$vehicle->retired_at->toDayDateTimeString()}}
                </div>
            @endif

            <ul class="list-group list-group-flush">


                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Vehicle Name</h3>
                        <p class="small pe-4">{{ $vehicle->name ?? '-' }}</p>
                    </div>
                    <div>
                </li>

                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Vehicle License Plate Number</h3>
                        <p class="small pe-4">{{ $vehicle->license_plate_number }}</p>
                    </div>
                    <div>
                </li>


                <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                    <div>
                        <h3 class="h6 mb-1">Vehicle VIN</h3>
                        <p class="small pe-4">{{ $vehicle->vin }}</p>
                    </div>
                </li>

                <div class="row">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                <div class="list-group">
                                    <li class="list-group-item list-group-item-info">
                                        Vehicle Details for VIN# {{$vehicle->vin}}
                                        <span class="float-end"><a href="https://vpic.nhtsa.dot.gov/api/" target="_blank">Data from NHTSA</a></span>
                                    </li>
                                    
                                        <li class="list-group-item list-group-item-light">
                                            Vehicle Make : <strong>{{$vehicle->make}}</strong>
                                        </li>

                                        <li class="list-group-item list-group-item-light">
                                            Vehicle Model : <strong>{{$vehicle->model}}</strong>
                                        </li>

                                        <li class="list-group-item list-group-item-light">
                                            Vehicle Model Year : <strong>{{$vehicle->year}}</strong>
                                        </li>

                                        <li class="list-group-item list-group-item-light">
                                            Vehicle Model Type : <strong>{{$vehicle->type}}</strong>
                                        </li>

                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </ul>
        </div>
    </div>
</div>
