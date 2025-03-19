<div class="row">
    <div class="col-8 schedule-form">
        <div class="border rounded">
            <form wire:submit.prevent="submit()">
                <div class="modal-body">
                    <div class="row w-100">
                        <div class="col-md-12">
                            <div class="form-group">
                                <x-forms.select label="Schedule" model="form.type" :options="$scheduleOptions" :hasAssociativeIndex="true"
                                    :listener="'typeCheck'" default-option-label="- None -" :selected="$form->type"
                                    :key="'schedule-options'" />
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="form-group">
                                <x-forms.input type="number" label="Order Number" model="form.sx_ordernumber" />
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="form-group">
                                <x-forms.input type="number" label="Order Suffix" model="form.suffix" :live="true"
                                    lazy />
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <div wire:loading wire:target="form.suffix" class="mb-3">
                                <span class="spinner-border spinner-border-sm mr-2" role="status"
                                    aria-hidden="true"></span>
                                <span>Please wait, fetching order from SX...</span>
                            </div>
                        </div>

                    </div>

                    <div class="row w-100" wire:loading.remove wire:target="form.suffix">
                        @if ($form->alertConfig['status'])
                            <div class="col-md-12 mb-1">
                                <p class="text-{{ $form->alertConfig['class'] }}"><i
                                        class="far {{ $form->alertConfig['icon'] }}"></i> {!! $form->alertConfig['message'] !!}
                                    @if ($form->alertConfig['show_url'])
                                        <a class="float-end" target="_blank"
                                            href="{{ route($form->alertConfig['url']) }}?{{ $form->alertConfig['params'] }}"
                                            target="_blank"> {{ $form->alertConfig['urlText'] }} <i
                                                class="fas fa-external-link-alt"></i></a>
                                    @endif
                                </p>
                            </div>
                        @endif

                        @if ($form->contactError)
                            <p class="text-danger"><i class="fas fa-times-circle"></i>
                                   {{$form->contactError}}
                            </p>
                        @endif
                        @if ($form->orderInfo && is_array($form->orderInfo->shipping_info))
                            <div class="col-md-12" wire:loading.remove wire:target="form.suffix">
                                <div class="alert alert-light-primary color-primary" role="alert">
                                    <span class="badge bg-light-warning float-end">
                                        <a target="_blank" href="">
                                            <i class="fas fa-external-link-alt"></i> CustNo #{{ $form->orderInfo?->sx_customer_number }}
                                        </a>
                                    </span>

                                    <h5 class="alert-heading text-decoration-underline text-muted">Service Address</h5>
                                    <p>
                                        <a class="float-end link-secondary" href="javascript:void(0)" wire:click="showAddressModal">
                                            <i class="fas fa-edit schedule-edit-icon"></i> Edit Address
                                        </a>
                                    </p>

                                    <address class="ms-1">
                                        <strong>{{ $form->orderInfo?->shipping_info['name'] }}</strong> <br>
                                        {{ $form->service_address }} <br>

                                        <div>
                                            <i class="fa-solid fa-phone me-2"></i>
                                            {{ $form->phone ? format_phone($form->phone) : 'n/a' }}
                                            <i class="fa-solid fa-envelope ms-3 me-2"></i>
                                            {{ $form->email ? $form->email : 'n/a' }}

                                            <a href="javascript:void(0)" class="float-end link-secondary" wire:click="showEditContactModal">
                                                <i class="fas fa-edit schedule-edit-icon"></i> Edit Contact
                                            </a>
                                        </div>
                                    </address>

                                    <hr>

                                    <p class="mb-0 text-muted"><strong>Ship To (from SX)</strong></p>
                                    <p class="mb-0">
                                        {{ $form->orderInfo->shipping_info['line'] . ', ' .
                                            $form->orderInfo->shipping_info['line2'] . ', ' .
                                            $form->orderInfo->shipping_info['city'] . ', ' .
                                            $form->orderInfo->shipping_info['state'] . ', ' .
                                            $form->orderInfo->shipping_info['zip'] }}
                                    </p>
                                    <p class="mb-0">Shipping Instructions (from SX) :
                                        {{ $form->orderInfo->shipping_info['instructions'] ?: 'n/a' }}
                                    </p>
                                </div>


                            </div>
                            @if ($form->addressVerified)
                                <p class="text-success"><i class="fas fa-check-circle"></i>
                                    Service address verified and formatted by Google
                                </p>
                            @endif
                            @if ($form->zipcodeInfo)
                                @if (isset($form->shipping))
                                    <div class="col-md-12 mb-1">
                                        <p class="text-success"><i class="fas fa-truck"></i>
                                            Per google, the driving distance is {{ $form->shipping['distance'] }} and
                                            takes
                                            roughly
                                            {{ $form->shipping['duration'] }}
                                        </p>
                                    </div>
                                @endif
                            @endif
                            @if (!in_array($form->type,['pickup', 'delivery']))
                                <div class="col-md-12 mb-4">
                                    <x-forms.checkbox model="form.not_purchased_via_weingartz"
                                        label="Equipment not purchased via Weingartz" />
                                </div>
                            @endif
                            {{-- line items --}}

                            @if (!empty($form->orderInfo?->line_items['line_items']) && !$form->not_purchased_via_weingartz)
                                <div class="col-md-12 mb-2">
                                    <ul class="list-group">
                                        <li class="list-group-item list-group-item-primary">
                                            Line Items
                                        </li>
                                        @foreach ($form->orderInfo->line_items['line_items'] ?? [] as $item)
                                            @php
                                                $appendSerial = '';
                                                $serial = collect($form->serialNumbers)->firstWhere(
                                                    'prod',
                                                    $item['shipprod'],
                                                );
                                                if (isset($serial)) {
                                                    $appendSerial = $serial->serialno;
                                                }
                                                $cargoString = '';
                                                if(isset($form->prodDimension)) {

                                                    $cargo = collect($form->prodDimension)->firstWhere(
                                                        fn($dimension) => strtolower($dimension['category']) === strtolower($item['prodcat'])
                                                    );
                                                    if ($cargo && isset($cargo['length'], $cargo['width'], $cargo['height'])) {
                                                        $cargoString = "{$cargo['length']}ft x {$cargo['width']}ft x {$cargo['height']}ft";
                                                    }
                                                }
                                            @endphp
                                            <li class="list-group-item">
                                                @if (!empty($appendSerial))
                                                    <span class="badge bg-light-secondary float-end">SN :
                                                        {{ $appendSerial }}</span>
                                                @endif
                                                <span class="badge bg-light-primary float-end me-1">{{$cargoString}}</span>
                                                @if ($form->type == 'at_home_maintenance')

                                                    <x-forms.radio :label="$item['descrip'] . '(' . $item['shipprod'] . ')'" :name="'lineitem'" :value="$item['shipprod']"
                                                        :model="'form.line_item'" />
                                                @else
                                                    <x-forms.checkbox :label="$item['descrip'] . '(' . $item['shipprod'].')'" :name="'lineitem[]'" :value="$item['shipprod']"
                                                    :model="'form.line_item'" />
                                                @endif

                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @error('form.line_item')
                                <div class="col-md-12 mb-2">
                                    <span class="text-danger">{{ $message }}</span>
                                </div>
                            @enderror
                            {{-- end of line items --}}
                            @if ($form->ServiceStatus )
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <x-forms.select label="Scheduling Priority" model="form.scheduleType"
                                            :options="$schedulePriority" :hasAssociativeIndex="true" :listener="'scheduleTypeChange'"
                                            default-option-label="- None -" :selected="$form->scheduleType" :key="'scheduleTypeKey'" />
                                    </div>
                                </div>
                                @if ($showTypeLoader)
                                    <div class="col-md-12 mb-3">
                                        <span class="spinner-border spinner-border-sm mr-2" role="status"
                                            aria-hidden="true"></span>
                                        <span>Please wait,fetching schedule dates ...</span>
                                    </div>
                                @endif
                                {{-- schedule date field --}}
                                <div class="col-md-6 {{ !$showTypeLoader && $form->scheduleType ? '' : 'd-none' }}">
                                    <div class="form-group">
                                        <label for="datepicker" class="form-label">Select Date</label>
                                        <div wire:ignore>
                                            <input type="text" wire:key="scheduleDateKey" id="datepicker"
                                                class="form-control" wire:model.defer="form.schedule_date"
                                                x-data="{
                                                    enabledDates: @js($form->enabledDates ?? []),
                                                    flatpickrInstance: null
                                                }" x-init="flatpickrInstance = flatpickr($el, {
                                                    inline: true,
                                                    dateFormat: 'Y-m-d',
                                                    defaultDate: '{{ $form->schedule_date }}',
                                                    enable: enabledDates,
                                                    minDate: new Date(),
                                                    onChange: function(selectedDates, dateStr) {
                                                        $wire.updateFormScheduleDate(dateStr);
                                                    }
                                                });"
                                                x-on:enable-date-update.window="
                                                    if (flatpickrInstance) {
                                                        let dates = Array.isArray($event.detail.enabledDates) ? $event.detail.enabledDates : [];
                                                        flatpickrInstance.set('enable', dates);
                                                    }
                                                "
                                                x-on:set-current-date.window="
                                                        if (flatpickrInstance) {
                                                            flatpickrInstance.jumpToDate($event.detail.activeDay);
                                                        }
                                                ">

                                        </div>
                                        @error('form.schedule_date')
                                            <span class="text-danger"> {{ $message }}</span>
                                        @enderror

                                    </div>
                                </div>
                                <div wire:loading wire:target="updateFormScheduleDate" class="col-md-6">
                                    <div class="d-flex justify-content-center align-items-center h-100 w-100 py-3">
                                        <div class="text-center">
                                            <div class="spinner-grow text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <div class="spinner-grow text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <div class="spinner-grow text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <div class="spinner-grow text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <div class="spinner-grow text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- schedule date end --}}
                                {{-- timeslots listing --}}
                                <div wire:loading.remove wire:target="updateFormScheduleDate"
                                    class="col-md-6 {{ $form->schedule_date && !$showTypeLoader ? '' : 'd-none' }} overflow-auto"
                                    style="height: 400px">
                                    <label class="form-label">Available Time Slots on
                                        {{ Carbon\Carbon::parse($form->schedule_date)->toFormattedDayDateString() }}</label>
                                    @if ($form->scheduleType == 'schedule_override')
                                        <p class="ps-2 bg-warning text-dark rounded"><i
                                                class="fas fa-exclamation-triangle"></i> Schedule Override Mode ON
                                        </p>
                                    @endif
                                    <div class="d-flex flex-column gap-2">

                                        @forelse($this->form->truckSchedules as $schedule)
                                            <a href="javascript:void(0)"
                                                wire:click.prevent="selectSlot({{ $schedule->id }})"
                                                class="list-group-item list-group-item-action
                                                @if ($schedule->schedule_count >= $schedule->slots && $form->scheduleType != 'schedule_override') d-none disabled text-muted time-slot-full @endif">
                                                <div
                                                    class="p-3 bg-light rounded border @if ($schedule->id == $form->schedule_time) border-3 border-primary @endif">
                                                    {{ $schedule->start_time . ' - ' . $schedule->end_time }}
                                                    @if ($form->scheduleType != 'schedule_override')
                                                        <span
                                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">
                                                            {{ $schedule->schedule_count }} / {{ $schedule->slots }}
                                                        </span>
                                                    @endif
                                                    <p class="me-2 fst-italic text-muted" style="font-size: smaller;">
                                                        <i class="fas fa-globe"></i>
                                                        {{ $schedule->zone_name }} => <i
                                                            class="fas fa-truck"></i>{{ $schedule->truck_name }}
                                                    </p>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="p-3 bg-light rounded border">
                                                <button type="button"
                                                    class="list-group-item list-group-item-action">No
                                                    Slots
                                                    Available</button>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                                {{-- end of timeslots listing --}}

                                @error('form.schedule_time')
                                    <div class="col-md-12">
                                        <span class="text-danger">{{ $message }}</span>
                                    </div>
                                @enderror
                            @endif

                        @endif
                    </div>
                    <div class="row w-100">
                        <div class="col-md-12">
                            <x-forms.textarea label="Notes" model="form.notes" lazy />
                        </div>
                        <div class="col-md-12">
                            <x-forms.checkbox label="Notify Customer" name="notifyUser" :value="1"
                                model="form.notifyUser" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" @if (!$form->schedule_time) disabled @endif>
                        <div wire:loading wire:target="submit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        Schedule
                    </button>
                </div>
            </form>
        </div>

    </div>

    {{-- sidebar --}}
    <div class="col-4">
        <ul class="list-group mb-3">
            <li class="list-group-item list-group-item-primary">
                <span class="badge bg-light-warning float-end"><a href=""><i
                            class="fas fa-external-link-alt"></i>
                        #{{ $form->orderInfo?->order_number }}-{{ $form->orderInfo?->order_number_suffix }}</a></span>
                Order Info
            </li>
            @if ($form->orderInfo)

                <li class="list-group-item"><strong>Stage</strong> <span
                        class="float-end">{{ $form->orderInfo->getStageCode() }}</span></li>
                <li class="list-group-item"><strong>Warehouse</strong> <span
                        class="float-end">{{ strtoupper($form->orderInfo->whse) }}</span></li>
                <li class="list-group-item"><strong>Placed</strong> <span class="float-end">
                        {{ $form->orderInfo->order_date?->format(config('app.default_datetime_format')) }}</span></li>
                <li class="list-group-item"><strong>Taken By</strong> <span
                        class="float-end">{{ $form->orderInfo->taken_by }}</span></li>
                @if (isset($this->form->orderTotal['total_invoice_amount']))
                    <li class="list-group-item"><strong>Amount</strong> <span
                            class="float-end">${{ number_format($this->form->orderTotal['total_invoice_amount']) }}</span>
                    </li>
                @endif
                <li class="list-group-item"><strong>Status</strong> <span
                        class="float-end">{{ $form->orderInfo->status }}</span></li>
            @else
                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <p class="text-muted ms-1">No order entered</p>
                    </div>
                </li>
            @endif
        </ul>
        @if ( $form->type !='at_home_maintenance')
            @include('livewire.scheduler.schedule.partial.schedule_form_zipcode_info')
        @endif
        @if (isset($form->zipcodeInfo))
            <ul class="list-group mb-3">
                @php
                    $zoneFlag = true;
                @endphp
                @foreach ($form->zipcodeInfo as $zipcode)
                    @foreach ($zipcode['zones'] as $zone)
                        @if($zoneFlag)
                            <li class="list-group-item list-group-item-primary">
                                Zones
                            </li>
                            @php
                                $zoneFlag = false;
                            @endphp
                        @endif
                        <li class="list-group-item"><strong>{{ $zone['name'] }}</strong>
                            <span class="badge bg-light-warning float-end">{{ App\Enums\Scheduler\ScheduleTypeEnum::tryFrom($zone['service'])->abbreviation() }}</span>
                            <small></small>
                        </li>
                    @endforeach
                @endforeach
            </ul>
        @endif
        @if (!empty($scheduledTruckInfo))
            <ul class="list-group mb-3">
                <li class="list-group-item list-group-item-primary">
                    Truck Info
                </li>
                <li class="list-group-item"><strong>Truck Name</strong> <span
                        class="float-end">{{ $scheduledTruckInfo['truck_name'] }}</span></li>
                <li class="list-group-item"><strong>Vin Number</strong> <span
                        class="float-end">{{ $scheduledTruckInfo['vin_number'] }}</span></li>
                <li class="list-group-item"><strong>Driver Name</strong> <span
                        class="float-end">{{ $scheduledTruckInfo['driver_name'] }}</span></li>
                <li class="list-group-item"><strong>Model Make, Year</strong> <span
                        class="float-end">{{ $scheduledTruckInfo['make_model'] }},
                        {{ $scheduledTruckInfo['year'] }}</span></li>
                <li class="list-group-item"><strong>Shift type</strong> <span
                        class="float-end">{{ $scheduledTruckInfo['shiftType'] }}</span></li>
                <li class="list-group-item"><strong>{{ $scheduledTruckInfo['notes'] }}</strong> </li>

            </ul>
        @endif

        {{-- address validation modal --}}
        @if ($form->showAddressModal)
            <x-modal toggle="form.showAddressModal" size="md" :closeEvent="'closeAddressValidation'">
                <x-slot name="title"> Address Verification Failed </x-slot>
                <div class="mb-4">
                    <h6 class="text-primary">Current Address</h6>
                    <ul class="list-group">
                        <li class="list-group-item">
                            {{ $form->service_address }}
                        </li>
                    </ul>

                </div>
                <div class="mb-4">
                    <h6 class="text-primary">Suggested Address</h6>
                    <ul class="list-group">
                        <li class="list-group-item">
                            {{ $form->recommendedAddress }}
                        </li>
                    </ul>

                </div>

                <div>
                    <h6 class="text-danger">Issues Found via Google</h6>
                    <div class="alert alert-light-warning color-warning">
                        @foreach ($form->unconfirmedAddressTypes as $type)
                            <p> <i class="bi bi-exclamation-triangle"></i><strong>
                                    {{ Str::title(str_replace('_', ' ', $type)) }}
                                </strong> is Missing/Incorrect</p>
                        @endforeach
                    </div>
                    <div class="form-group">
                        <x-forms.textarea label="Service Address" model="form.recommendedAddress" :hint="'Showing verified address from google. Make necessary changes and verify'"
                            :key="'fix-service-address'" />
                    </div>
                </div>
                <x-slot name="footer">
                    <button type="submit" class="btn btn-light-secondary" wire:click="useCurrentAddress">
                        <div wire:loading wire:target="useCurrentAddress">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        Use Current Address
                    </button>
                    <button type="submit" class="btn btn-primary"
                        wire:click="fixAddress">
                        <div wire:loading wire:target="fixAddress">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        Verify Address
                    </button>
                </x-slot>
            </x-modal>
        @endif
        {{-- service address modal  --}}
        @if ($serviceAddressModal)
            <x-modal toggle="serviceAddressModal" size="md" :closeEvent="'closeServiceAddressModal'">
                <x-slot name="title">Update Address </x-slot>
                <div class="col-md-12 mb-2">
                    <div class="form-group">
                        <x-forms.textarea label="Service Address" model="form.service_address_temp"
                            :key="'service-address' . $form->addressKey" />
                    </div>
                </div>
                <x-slot name="footer">
                    <button type="submit" class="btn btn-primary" wire:click="updateAddress">
                        <div wire:loading wire:target="updateAddress">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        Update Address
                    </button>
                    <button type="submit" class="btn btn-secondary"
                        @if ($form->addressFromOrder == $form->service_address_temp) disabled @endif wire:click="revertAddress">
                        <div wire:loading wire:target="revertAddress">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        Reset Address
                    </button>
                </x-slot>
            </x-modal>
        @endif

    </div>
    {{-- end of sidebar --}}
    @if ($contactModal)
        <x-modal toggle="contactModal" size="md" :closeEvent="'closeContactModal'">
            <x-slot name="title">Update Contact </x-slot>
            <div class="col-md-12 mb-2">
                <div class="form-group">
                    <x-forms.input type="text" label="Phone" model="tempPhone" />

                </div>
            </div>
            <div class="col-md-12 mb-2">
                <div class="form-group">
                    <x-forms.input type="text" label="Email" model="tempEmail" />

                </div>
            </div>
            <x-slot name="footer">
                <button type="submit" class="btn btn-secondary" wire:click="updateContact">
                    <div wire:loading wire:target="updateContact">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    Update
                </button>
            </x-slot>
        </x-modal>
    @endif
</div>

@script
    <script>
        (function() {
            let autocomplete;
            let addressField;

            function initAutocomplete() {
                if (typeof(google) == 'undefined') return;

                setTimeout(() => {
                    addressField = document.getElementById("form.service_address_temp_textarea-field");

                    if (autocomplete) {
                        google.maps.event.clearInstanceListeners(autocomplete);

                        if (document.querySelector('.pac-container')) {
                            document.querySelector('.pac-container').remove()
                        }
                    }

                    autocomplete = new google.maps.places.Autocomplete(addressField, {
                        componentRestrictions: {
                            country: ["us", "ca"]
                        },
                    });

                    autocomplete.addListener("place_changed", fillInAddress);
                }, 500)
            }

            if (typeof(google) == 'undefined') {
                loadScript(
                    'https://maps.googleapis.com/maps/api/js?key={{ config('google.api_key') }}&libraries=places&v=weekly',
                    () => {})
            }

            function fillInAddress() {
                let place = autocomplete.getPlace();
                let postcode;

                for (const component of place.address_components) {
                    const componentType = component.types[0];

                    switch (componentType) {
                        case "postal_code": {
                            postcode = `${component.long_name}`;
                            break;
                        }
                    }
                }

                let selectedValue = document.getElementById("form.service_address_temp_textarea-field").value;
                if (postcode && !selectedValue.includes(postcode)) {
                    let country = selectedValue.includes('Canada') ? 'Canada' : 'USA';
                    selectedValue = selectedValue.replace(/(, [^,]+), (USA|Canada)$/, `$1 ${postcode}, ${country}`)
                    document.getElementById("form.service_address_temp_textarea-field").value = selectedValue
                }

                $wire.set('form.service_address_temp', selectedValue);
            }

            document.addEventListener('browser:show-edit-address', initAutocomplete);
            document.addEventListener('livewire:navigating', unbindEvents);

            function unbindEvents() {
                document.removeEventListener('browser:show-edit-address', initAutocomplete);
                document.removeEventListener('livewire:navigating', unbindEvents)
            }

        })();
    </script>
@endscript
