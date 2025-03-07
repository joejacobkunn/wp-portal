<div class="row">
    <form wire:submit.prevent="submit">
        <div class="col-12 col-md-12">
            <div class="card card-body shadow-sm mb-2">
                <h4 class="card-title mb-2">Add Truck Basic Info</h4>
                <div class="row">
                    <!-- Truck Name -->
                    <div class="col-md-6 mb-3">
                        <x-forms.input label="Truck Name" model="truck.truck_name" lazy />
                    </div>

                    <!-- VIN Number -->
                    <div class="col-md-6 mb-3">
                        <x-forms.input label="VIN Number" model="truck.vin_number" lazy />
                    </div>
                </div>

                <div class="row">
                    <!-- Shift Type -->
                    <div class="col-md-6 mb-3">
                        <x-forms.select label="Shift Type" model="truck.shift_type" :options="['Full Time', 'Part Time']" :selected="$truck->shift_type ?? null"
                            default-selectable default-option-label="- Select Shift -" />
                    </div>

                    <!-- Service Type -->
                    <div class="col-md-6 mb-3">
                        <x-forms.select label="Service Type" model="truck.service_type" :options="$serviceTypes"
                            :selected="$truck->service_type ?? null" :hasAssociativeIndex="true" default-selectable
                            default-option-label="- Select Shift -" />
                    </div>
                </div>

            </div>
            <div class="card card-body shadow-sm mb-2">
                <h4 class="card-title mb-2">Cargo Dimensions</h4>

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <x-forms.input label="Height" model="truck.height" appendText="ft" lazy />
                    </div>
                    <div class="col-md-4 mb-2">
                        <x-forms.input label="Width" model="truck.width" appendText="ft" lazy />
                    </div>
                    <div class="col-md-4 mb-2">
                        <x-forms.input label="Length" model="truck.length" appendText="ft" lazy />
                    </div>
                </div>
            </div>
            <div class="card card-body shadow-sm mb-2">
                <h4 class="card-title mb-2">Additional Info</h4>

                <!-- Model and Make -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Model and Make" model="truck.model_and_make" lazy />
                    </div>
                </div>

                <!-- Year -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Year" model="truck.year" type="number" lazy />
                    </div>
                </div>

                <!-- Color -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Color" model="truck.color" lazy />
                    </div>
                </div>
                <!-- Color -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <h3 class="h5 mb-3"><i class="fas fa-truck me-2"></i>Truck Image</h3>
                            <x-forms.media field-id="truck_image" model="truckImage" :entity="$truck"
                                collection="truck_image" editable rules="mimes:jpeg,png,webp" />
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.textarea label="Notes" model="truck.notes" lazy />
                    </div>
                </div>

                <hr>

                <!-- Submit and Cancel Buttons -->
                <div class="mt-2 float-start">
                    <button type="submit" class="btn btn-success">
                        <div wire:loading wire:target="submit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        {{ $button_text }}
                    </button>
                    <button type="button" wire:click="cancel" class="btn btn-light-secondary">Cancel</button>
                </div>
            </div>
        </div>
    </form>
</div>
