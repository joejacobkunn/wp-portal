<div class="row service-area">
    <div class="col-12 col-md-12">
        <form wire:submit.prevent="submit()">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <x-forms.input type="number" label="Zip code" model="form.zip_code" lazy key={{now()}}/>
                        @if ($form->zipDescription)
                            <div class="alert alert-light-primary color-primary">
                                {{$form->zipDescription}}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="accordion">
                        <div class="accordion-item mb-2">
                            <div class="row columnRow p-3">
                                <h6 class="accordion-header mb-2">
                                    Service
                                </h6>
                                <div class="col-6 mb-3">
                                    <x-forms.checkbox
                                        :label="'At Home Maintenance'"
                                        :name="'service[]'"
                                        :value="'at_home_maintenance'"
                                        :model="'form.service'"
                                    />
                                </div>
                                <div class="col-6 mb-3">
                                    <x-forms.checkbox
                                        :label="'Delivery/Pickup'"
                                        :name="'service[]'"
                                        :value="'delivery_pickup'"
                                        :model="'form.service'"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <x-forms.select label="Zone"
                        model="form.zone"
                        :options="$this->form->zones"
                        :selected="$form->zone"
                        hasAssociativeIndex
                        default-option-label="- None -"
                        :key="'zone' . now()" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <x-forms.input
                        label="Delivery Rate"
                        model="form.delivery_rate"
                        lazy />
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <x-forms.input
                            label="Pickup Rate"
                            model="form.pickup_rate"
                        lazy />
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <x-forms.textarea label="Note" model="form.notes" rows="5" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="alert alert-light-primary color-primary">
                        <div class="form-check form-switch" wire:key="{{now()}}">
                            <input type="checkbox"
                                   class="form-check-input"
                                   role="switch"
                                   id="active"
                                   wire:key="active-{{now()}}"
                                   wire:model="form.is_active"
                                   @checked($form->is_active)>
                            <label class="form-check-label" for="active">
                                Enable the switch to make this active
                            </label>
                            @error('form.is_active')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="mt-2 float-start">
                <button type="submit" class="btn btn-primary">
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
