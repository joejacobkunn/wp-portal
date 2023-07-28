<div class="row">
        <div class="col-12 col-md-12">
            <div class="card card-body shadow-sm mb-4">
                <form wire:submit.prevent="submit">

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <x-forms.input
                                label="Vehicle Name"
                                model="vehicle.name"
                                lazy
                            />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <x-forms.input
                                label="License Plate Number"
                                model="vehicle.license_plate_number"
                            />
                        </div>
                    </div>

                    @unless($valid_vin)

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <x-forms.input
                                    label="Vehicle VIN"
                                    model="vehicle.vin"
                                />
                            </div>
                        </div>

                    @endif


                    <div class="row">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                <div class="list-group">
                                    <li class="list-group-item @unless($valid_vin) list-group-item-warning @else list-group-item-success @endunless">
                                        Vehicle Details @unless($valid_vin) - Enter Valid VIN to fetch vehicle data @else for VIN# {{$vehicle->vin}} <button wire:click="changeVIN" type="button" class="btn btn-sm btn-link">Change VIN</button> @endif
                                        @if($valid_vin)<span class="float-end"><a href="https://vpic.nhtsa.dot.gov/api/" target="_blank">Data from NHTSA</a></span>@endif
                                    </li>

                                    @if($valid_vin)
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

                                    @endif
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="mt-2 float-start">
                        <button type="submit" class="btn btn-success">
                            <div wire:loading wire:target="{{ (isset($vehicle) ? 'save' : 'submit')}}">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </div>

                            {{$button_text}}

                        </button>
                        <button type="button" wire:click="cancel" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
