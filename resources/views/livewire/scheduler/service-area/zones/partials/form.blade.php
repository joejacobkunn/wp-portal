<div class="row service-area">
    <div class="col-12 col-md-12">
        <form wire:submit.prevent="submit()">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <x-forms.input label="Zone Name" model="name" lazy />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group x-input">
                        <div class="form-group">
                            <x-forms.select label="Service" model="service" :options="['' => 'Please Select', 'ahm' => 'AHM', 'pickup-delivery' => 'Pickup/Delivery']" :selected="$service"
                                :defaultOption=false :key="'service-' . now()" />

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group x-input">
                        <div class="form-group">
                            <x-forms.textarea label="Description" model="description" rows="5" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-light-primary color-primary">
                        <div class="form-check form-switch" wire:key="{{ now() }}">
                            <input type="checkbox" class="form-check-input" role="switch" id="active"
                                wire:key="active-{{ now() }}" wire:model="is_active"
                                @checked($is_active)>
                            <label class="form-check-label" for="active">
                                Enable this Zone
                            </label>
                            @error('form.is_active')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>

            <hr>
            <div class="mt-2 float-start">
                <button type="submit" class="btn btn-success">
                    <div wire:loading wire:target="submit">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    {{ $button_text }}
                </button>
                <button type="button" wire:click="cancel" class="btn btn-light-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>
