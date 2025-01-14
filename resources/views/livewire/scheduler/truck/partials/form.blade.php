<div class="row">
    <div class="col-12 col-md-12">
        <div class="card card-body shadow-sm mb-4">
            <form wire:submit.prevent="{{ !empty($truck->id) ? 'save()' : 'submit()' }}">

                <!-- Truck Name -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Truck Name" model="truck.truck_name" lazy />
                    </div>
                </div>

                <!-- VIN Number -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="VIN Number" model="truck.vin_number" lazy />
                    </div>
                </div>
                <!-- Driver -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.select label="Driver" model="truck.driver" :options="$drivers" :selected="$truck->driver ?? null"
                            default-selectable default-option-label="- Select Driver -" label-index="name"
                            value-index="id" />
                    </div>
                </div>

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
                <!-- Storage Space -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Cubic Storage Space" model="truck.cubic_storage_space" type="text"
                            lazy />
                    </div>
                </div>

                <!-- Color -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Color" model="truck.color" lazy />
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
                        <div wire:loading wire:target="{{ !empty($truck->id) ? 'save' : 'submit' }}">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        {{ $button_text }}
                    </button>
                    <button type="button" wire:click="cancel" class="btn btn-light-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
