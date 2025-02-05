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
                                    :key="'schedule-' . now()" />
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
                                <span>Please wait, processing order...</span>
                            </div>
                        </div>

                    </div>

                    @if ($form->orderInfo)
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

                            @if ($form->orderInfo && is_array($form->orderInfo->shipping_info))
                                <div class="col-md-12" wire:loading.remove wire:target="form.suffix">
                                    <div class="alert alert-light-primary color-primary" role="alert">
                                        <span class="badge bg-light-warning float-end"><a target="_blank"
                                                href=""><i class="fas fa-external-link-alt"></i> CustNo
                                                #{{ $form->orderInfo?->sx_customer_number }}</a></span>

                                        <h4 class="alert-heading">Service Address  <a href="javascript:void(0)"  wire:click="showAddressModal"><i class="fas fa-edit schedule-edit-icon" ></i></a></h4>
                                        <p>
                                        <address class="ms-1">
                                            <Strong>{{ $form->orderInfo?->shipping_info['name'] }}</Strong> <br>
                                                {{$form->service_address}}
                                            <br>
                                            <i class="fa-solid fa-phone"></i>
                                            {{ $form->orderInfo?->customer->phone ? $form->orderInfo?->customer->phone : 'n/a' }}
                                            <i class="fa-solid fa-envelope"></i>
                                            {{ $form->orderInfo?->customer->email ? $form->orderInfo?->customer->email : 'n/a' }}<br>

                                        </address>
                                        </p>

                                        <a href="#" wire:click.prevent="showAdrress()"
                                            class="btn btn-link text-primary fw-semibold d-inline-flex align-items-center">
                                            Use recommended address
                                        </a>
                                        <div wire:loading wire:target="showAdrress">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                        </div>
                                        <hr>
                                        <p class="mb-0"><Strong>Ship To</Strong>
                                        </p>
                                        <p class="mb-0">
                                            {{ $form->orderInfo->shipping_info['line'] .
                                                ', ' .
                                                $form->orderInfo->shipping_info['line2'] .
                                                ', ' .
                                                $form->orderInfo->shipping_info['city'] .
                                                ', ' .
                                                $form->orderInfo->shipping_info['state'] .
                                                ', ' .
                                                $form->orderInfo->shipping_info['zip'] }}
                                        </p>
                                        <p class="mb-0">Shipping Instructions :
                                            {{ $form->orderInfo->shipping_info['instructions'] ?? 'n/a' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-1">
                                    <p class="text-success"><i class="fas fa-truck"></i>
                                        Per google, the driving distance is {{ $form->shipping['distance'] }} and takes
                                        roughly
                                        {{ $form->shipping['duration'] }}
                                    </p>
                                </div>
                            @endif
                            @if ($this->form->saveRecommented)
                                <div class="col-md-12 mb-3">
                                    <p class="text-success"><i class="fas fa-check-circle"></i>
                                        Using recommended address from google
                                    </p>
                                </div>
                            @endif
                            {{-- line items --}}
                            @if ($form->orderInfo?->line_items)
                                <div class="col-md-12 mb-2">
                                    <ul class="list-group">
                                        <li class="list-group-item list-group-item-primary">
                                            Line Items
                                        </li>
                                        @foreach ($form->orderInfo->line_items['line_items'] as $item)
                                            <li class="list-group-item">
                                                <x-forms.radio :label="$item['descrip'] . '(' . $item['shipprod'] . ')'" :name="'lineitem'" :value="$item['shipprod']"
                                                    :model="'form.line_item'" />

                                            </li>
                                        @endforeach
                                    </ul>
                                    @error('form.line_item')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                            {{-- end of line items --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <x-forms.select label="Scheduling Priority" model="form.scheduleType"
                                        :options="[
                                            'next_avail' => 'Next Available Date',
                                            'one_year' => 'One Year from Now',
                                        ]" :hasAssociativeIndex="true" :listener="'scheduleTypeChange'"
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
                                        <input type="text" wire:key="scheduleDateKey" id="datepicker" class="form-control"
                                            wire:model.defer="form.schedule_date" x-data="{
                                                enabledDates: @js($form->enabledDates ?? []),
                                                flatpickrInstance: null
                                            }"
                                            x-init="flatpickrInstance = flatpickr($el, {
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
                                                        flatpickrInstance.set('enable', $event.detail.enabledDates);
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
                                class="col-md-6 {{ $form->schedule_date && !$showTypeLoader ? '' : 'd-none' }}">
                                <label class="form-label">Available Time Slots on
                                    {{ Carbon\Carbon::parse($form->schedule_date)->toFormattedDayDateString() }}</label>
                                <div class="d-flex flex-column gap-2">

                                    @forelse($this->form->truckSchedules as $schedule)
                                        <a href="javascript:void(0)"
                                            wire:click.prevent="selectSlot({{ $schedule->id }})"
                                            class="list-group-item list-group-item-action
                                            @if ($schedule->schedule_count >= $schedule->slots) disabled text-muted time-slot-full @endif">
                                            <div
                                                class="p-3 bg-light rounded border @if ($schedule->id == $form->schedule_time) border-3 border-primary @endif">
                                                {{ $schedule->start_time . ' - ' . $schedule->end_time }}
                                                <span class="badge bg-secondary badge-pill badge-round ms-1 float-end">
                                                    {{ $schedule->schedule_count }} / {{ $schedule->slots }}
                                                </span>
                                                <p class="me-2 fst-italic text-muted" style="font-size: smaller;"><i
                                                        class="fas fa-globe"></i>
                                                    {{ $schedule->zone_name }} => <i
                                                        class="fas fa-truck"></i>{{ $schedule->truck_name }}
                                                </p>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="p-3 bg-light rounded border">
                                            <button type="button" class="list-group-item list-group-item-action">No
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
                        </div>
                    @endif
                    <div class="row w-100">
                        <div class="col-md-12">
                            <x-forms.textarea label="Notes" model="form.notes" lazy />
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
        <ul class="list-group mb-3">
            <li class="list-group-item list-group-item-primary">
                <span class="badge bg-light-warning float-end">
                    @if ($form->zipcodeInfo)
                        <a href="{{ route('service-area.zipcode.show', ['zipcode' => $form->zipcodeInfo->id]) }}"><i
                                class="fas fa-external-link-alt"></i>
                            #{{ $form->zipcodeInfo?->zip_code }}</a>
                    @else
                        <a href="{{ route('service-area.index') }}?tab=zip_code"><i
                                class="fas fa-external-link-alt"></i>
                            create new</a>
                    @endif

                </span>
                ZIP Code Info
            </li>
            @if ($form->zipcodeInfo)
                <li class="list-group-item"><strong>ZIP Code</strong> <span
                        class="float-end">{{ $form->orderInfo?->shipping_info['zip'] }}</span></li>
                <li class="list-group-item"><strong>Delivery Rate</strong> <span
                        class="float-end">${{ $form->zipcodeInfo?->delivery_rate }}</span></li>
                <li class="list-group-item"><strong>Pickup Rate</strong> <span
                        class="float-end">${{ $form->zipcodeInfo?->pickup_rate }}</span></li>
            @else
                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <p class="text-muted ms-1">Need valid zipcode</p>
                    </div>
                </li>
            @endif
        </ul>
        @if (isset($form->zipcodeInfo))
            <ul class="list-group mb-3">
                <li class="list-group-item list-group-item-primary">
                    Zones
                </li>
                @foreach ($form->zipcodeInfo->zones as $zone)
                    <li class="list-group-item"><strong>{{ $zone->name }}</strong>
                        <span class="badge bg-light-warning float-end">{{ strtoupper($zone->service) }}</span>
                        <small></small>
                    </li>
                @endforeach
            </ul>
        @endif

        {{-- address modal --}}
        @if ($addressModal)
            <x-modal toggle="addressModal" size="md" :closeEvent="'closeAddress'">
                <x-slot name="title">Recommended Address </x-slot>
                <div class="mb-4">
                    <h6 class="text-primary">Current Address</h6>
                    <p>
                        {{ $form->service_address }}
                    </p>
                </div>
                <hr>
                <div>
                    <h6 class="text-success">Recommended Address</h6>
                    <p>
                    <address class="ms-1">
                        {{ $form->recommendedAddress['formattedAddress'] }}
                    </address>
                    </p>
                </div>
                <div class="mt-2 float-start">
                    <button type="submit" class="btn btn-primary" wire:click="setAddress">
                        <div wire:loading wire:target="setAddress">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        Use Recommended Address
                    </button>
                </div>
            </x-modal>
        @endif
        @if ($serviceAddressModal)
            <x-modal toggle="serviceAddressModal" size="md" :closeEvent="'closeServiceAddressModal'">
                <x-slot name="title">Update Address </x-slot>
                <div class="col-md-12 mb-2">
                    <div class="form-group">
                        <x-forms.textarea label="Service Address"  model="form.service_address"
                            :key="'service-address'.$form->addressKey" />
                    </div>
                </div>
                <hr>
                <div class="mt-2 float-start">
                    <button type="submit" class="btn btn-primary" wire:click="updateAddress">
                        <div wire:loading wire:target="updateAddress">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        Update Address
                    </button>
                </div>
            </x-modal>
        @endif

    </div>
    {{-- end of sidebar --}}
</div>
