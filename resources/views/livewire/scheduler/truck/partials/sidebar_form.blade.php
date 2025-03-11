
<form wire:submit.prevent="{{ !empty($this->form->truckSchedule) ? 'save()' : 'submit()' }}">
<div class="modal-content">
        <div class="modal-body">
            {{-- Zones --}}
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <select class="form-select" wire:model="form.zone">
                            <option value=""> Select Zone</option>
                            @foreach ($zones as $zone)
                                <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                            @endforeach
                        </select>
                        @error('form.zone')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            {{-- start time --}}
            <div class="row">
                <div class="col-md-12 mb-2">

                    <label> Start Time</label>
                    <div class="input-group">
                        <!-- Time Input -->
                        <input type="time" wire:model="form.start_time" class="form-control"
                            placeholder="Select Time" aria-label="Time input">

                        <!-- AM/PM Select Dropdown -->
                        <div class="input-group-append">
                            <select class="form-select" id="timePeriod" wire:model="form.timePeriod"
                                name="timePeriod">
                                <option value="AM">AM</option>
                                <option value="PM">PM</option>
                            </select>
                        </div>

                    </div>
                    @error('form.start_time')
                        <span class="text-danger">{{ $message }}</span>
                        <br>
                    @enderror
                    @error('form.timePeriod')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                {{-- end time --}}
                <div class="col-md-12 mb-2">
                    <label> End Time</label>
                    <div class="input-group">
                        <!-- Time Input -->
                        <input type="time" wire:model="form.end_time" class="form-control">
                        <!-- AM/PM Select Dropdown -->
                        <div class="input-group-append">
                            <select class="form-select" wire:model="form.timePeriodEnd">
                                <option value="AM">AM</option>
                                <option value="PM">PM</option>
                            </select>
                        </div>
                    </div>
                    @error('form.end_time')
                        <span class="text-danger">{{ $message }}</span>
                        <br>
                    @enderror
                    @error('form.timePeriodEnd')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-2">
                    <div class="form-group">
                        <x-forms.input type="number" label="Slots" model="form.slots" lazy />
                    </div>
                </div>
                @if ($this->truck->service_type == 'Delivery / Pickup')
                    <div class="col-md-12 mb-2">
                        <div class="form-group">
                            <label for=""> Delivery Method</label>
                            <select class="form-select" wire:model="form.delivery_method">
                                <option value=""> Select Zone</option>
                                <option value="pickup" @selected($form->delivery_method == 'pickup')> Pickup</option>
                                <option value="delivery" @selected($form->delivery_method == 'delivery')> Delivery</option>

                            </select>
                            @error('form.delivery_method')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="modal-footer">
            <hr>
            <button type="button" class="btn" wire:click="closeUpdateForm()">
                <span class=" d-sm-block ">Cancel</span>
            </button>
            <button class="btn btn-primary ms-2" type="submit">
                <div wire:loading wire:target="submit">
                    <span class="spinner-border spinner-border-sm" role="status"
                        aria-hidden="true"></span>
                </div>
                {{ !empty($this->form->truckSchedule) ? 'Update' : 'Schedule' }}
            </button>
        </div>
    </div>
</form>

