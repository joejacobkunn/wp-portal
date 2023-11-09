<div class="row">
    <div class="col-12 col-md-12">
        <div class="card card-body shadow-sm mb-4">
            <form wire:submit.prevent="{{ !empty($location->id) ? 'save()' : 'submit()' }}">

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Name" model="location.name" lazy />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Phone" model="location.phone" prepend-icon="fas fa-phone" lazy />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.textarea label="Address" model="location.address" prepend-icon="fas fa-envelope"
                            lazy />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <x-forms.select label="Location" model="location.location" :options="$locations"
                                :listener="'location:changed'" :selected="$location->location ?? null" default-selectable default-option-label="- None -"
                                label-index="name" value-index="id" />
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <x-forms.select label="Fortis Location" model="location.fortis_location_id"
                                :options="$fortis_locations" :selected="$location->fortis_location_id ?? null" default-selectable label-index="name"
                                value-index="id" default-option-label="- None -" />
                            <small class="form-text text-muted">This links location with Fortis POS</small>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12 mb-1">
                        <x-forms.checkbox label="Activate Location" model="location.is_active" lazy />
                    </div>
                </div>

                <hr>

                <div class="mt-2 float-start">

                    <button type="submit" class="btn btn-success">
                        <div wire:loading wire:target="{{ !empty($role->id) ? 'save' : 'submit' }}">
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
