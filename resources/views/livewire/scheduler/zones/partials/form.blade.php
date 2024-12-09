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
                <div class="col-md-12 mb-3">
                    <div class="form-group x-input">
                        <div class="form-group">
                            <x-forms.textarea label="Description" model="description" rows="5" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="row columnRow">
                <div class="col-md-6">
                    <div class="accordion">
                        <div class="accordion-item mb-2">
                            <h2 class="accordion-header" id="headingtemplate">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-template" aria-expanded="true" aria-controls="collapse-template">
                                    Schedule Days
                                </button>
                            </h2>
                            <div id="collapse-template" class="accordion-collapse collapse show" aria-labelledby="headingtemplate">
                                <div class="accordion-body">
                                    <div class="row">
                                        @foreach($days as $day => $values)
                                            <div class="col-12 mb-3">
                                                <x-forms.checkbox
                                                    :label="ucfirst($day)"
                                                    :name="'days.' . $day . '.enabled'"
                                                    :value="true"
                                                    :model="'days.' . $day . '.enabled'"
                                                />
                                            </div>
                                            @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($showTimeSlotSection)
                    <div class="col-md-6">
                        <div class="accordion">
                            <div class="accordion-item mb-2">
                                <h2 class="accordion-header" id="headingArrangeColumns">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-arrange-columns" aria-expanded="true" aria-controls="collapse-arrange-columns">
                                        Time Slot Details
                                    </button>
                                </h2>
                                <div id="collapse-arrange-columns" class="accordion-collapse collapse show" aria-labelledby="headingArrangeColumns">
                                    <div class="accordion-body time-slot-wrap" >

                                        @foreach($days as $day => $values)

                                            @if($values['enabled'])
                                                <div class="border p-3 rounded mb-2">
                                                    <h5 class="mb-3">{{ucfirst($day)}}</h5>
                                                    <div class="row">
                                                        <!-- Two Integer Fields -->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-forms.input type="number" label="AHM Slot" model="days.{{ $day }}.ahm_slot" lazy />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-forms.input type="number" label="Pickup/Delivery Slot" model="days.{{ $day }}.pickup_delivery_slot" lazy />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Select Field -->
                                                    <div class="form-group mt-3">
                                                        <x-forms.select label="Shift"
                                                            model="days.{{ $day }}.schedule"
                                                            :options="$scheduleOptions"
                                                            :selected="$days[$day]['schedule']"
                                                            hasAssociativeIndex
                                                            default-option-label="- None -"
                                                            :key="'am-pm' . now()" />
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
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
