<div class="row">
    <div class="col-8 ">
        <div class="border rounded p-4">
            <form wire:submit.prevent="submit()">
                <div class="row w-100">
                    <div class="col-md-12">
                        <div class="form-group">
                            <x-forms.select label="Schedule" model="form.type" :options="$scheduleOptions" :hasAssociativeIndex="true"
                                :listener="'typeCheck'" default-option-label="- None -" :selected="$form->type" :key="'schedule-' . now()" />
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <x-forms.input type="number" label="Order Number" model="form.sx_ordernumber" :live="true"
                                 lazy />
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
                            <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
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
                                        <span class="badge bg-light-warning float-end"><a target="_blank" href=""><i
                                                    class="fas fa-external-link-alt"></i> CustNo
                                                #{{ $form->orderInfo?->sx_customer_number }}</a></span>
                                        <h4 class="alert-heading">Service Address</h4>
                                        <p>
                                        <address class="ms-1">
                                            <Strong>{{ $form->orderInfo?->shipping_info['name'] }}</Strong> <br>
                                            {{ $form->orderInfo?->shipping_info['line'] }}<br>
                                            @if ($form->orderInfo?->shipping_info['line2'])
                                                {{ $form->orderInfo?->customer->address2 }} <br>
                                            @endif
                                            {{ $form->orderInfo?->shipping_info['city'] }},
                                            {{ $form->orderInfo?->shipping_info['state'] }}
                                            {{ $form->orderInfo?->shipping_info['zip'] }}<br>
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
                                        @if ($form->recommendedAddress)
                                            <p class="mb-0"> {{ $form->recommendedAddress['formattedAddress'] }}</p>
                                        @else
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
                                        @endif


                                        <p class="mb-0">Shipping Instructions :
                                            {{ $form->orderInfo->shipping_info['instructions'] ?? 'n/a' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-1">
                                    <p class="text-success"><i class="fas fa-truck"></i>
                                        Per google, the driving distance is {{ $form->shipping['distance'] }} and takes roughly
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
                            @if ($form->orderInfo?->line_items)
                                <div class="col-md-12 mb-2">
                                    <ul class="list-group">
                                        <li class="list-group-item list-group-item-primary">
                                            Line Items
                                        </li>
                                        @foreach ($form->orderInfo->line_items['line_items'] as $item)
                                            <li class="list-group-item">
                                                <p>{{$item['descrip'] . '(' . $item['shipprod'] . ')' }}</p>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="col-md-12">
                                <div class="form-group">
                                    <x-forms.select label="Scheduling Priority" model="form.scheduleType" :options="[
                                        'next_avail' => 'Next Available Date',
                                        'one_year' => 'One Year from Now',
                                    ]"
                                        :hasAssociativeIndex="true" :listener="'scheduleTypeChange'" default-option-label="- None -" :selected="$form->scheduleType"
                                        :key="'schedule-' . now()" />
                                </div>
                            </div>
                                <div class="col-md-6 {{$form->scheduleType ? '' : 'd-none'}}">
                                    <div class="form-group">
                                        <label for="datepicker" class="form-label">Select Date</label>
                                        <div wire:ignore>
                                            <input type="text" id="datepicker" class="form-control"
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
                                                    flatpickrInstance.setDate($event.detail.activeDay, true);
                                                }
                                        ">

                                    </div>
                                    @error('form.schedule_date')
                                        <span class="text-danger"> {{ $message }}</span>
                                    @enderror

                                    </div>
                                </div>
                                <div class="col-md-6 {{$form->scheduleType ? '' : 'd-none'}}">
                                    <label class="form-label">Available Time Slots on {{ Carbon\Carbon::parse($form->schedule_date)->toFormattedDayDateString() }}</label>
                                    <div class="d-flex flex-column gap-2">

                                        @forelse($this->form->truckSchedules as $schedule)
                                            <div class="p-3 bg-light rounded border">
                                                <button type="button" wire:click="selectSlot({{$schedule->id}})"
                                                    class="list-group-item list-group-item-action ">{{$schedule->start_time. ' - '.$schedule->end_time}}
                                                    <span
                                                        class="badge bg-secondary badge-pill badge-round ms-1 float-end">{{$schedule->schedule_count }} / {{$schedule->slots}}</span>
                                                    <p class="me-2 fst-italic text-muted" style="font-size: smaller;"><i
                                                        class="fas fa-globe"></i>
                                                            {{$schedule->zone_name}} => <i
                                                            class="fas fa-truck"></i>{{$schedule->truck_name}}
                                                    </p>
                                                </button>
                                            </div>
                                        @empty
                                            <div class="p-3 bg-light rounded border">
                                                <button type="button"  class="list-group-item list-group-item-action" >No Slots
                                                    Available</button>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                                @if($form->shiftMsg )
                                    <div class="col-md-12">
                                        <p class="text-success"><i class="far fa-check-circle"></i> {{$form->shiftMsg}}
                                        </p>
                                    </div>
                                @endif
                                @error('form.schedule_time')
                                    <div class="col-md-12">
                                        <span class="text-danger">{{$message}}</span>
                                    </div>
                                @enderror
                        </div>
                    @endif

                <div class="mt-2">
                    <button class="btn btn-primary" type="submit">
                        <div wire:loading wire:target="submit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        {{ $this->isEdit ? 'Update' : 'Schedule' }}
                    </button>

                </div>
            </form>
        </div>

    </div>
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
                <li class="list-group-item"><strong>{{$zone->name}}</strong>
                    <span class="badge bg-light-warning float-end">{{strtoupper($zone->service)}}</span>
                    <small></small>
                </li>
                @endforeach
            </ul>
        @endif

        @if ($addressModal)
            <x-modal :toggle="$addressModal" size="md" :closeEvent="'closeAddress'">
                <x-slot name="title">Recommended Address </x-slot>
                <div class="mb-4">
                    <h6 class="text-primary">Current Address</h6>
                    <p>
                    <address class="ms-1">
                        <Strong>{{ $form->orderInfo?->customer->name }}</Strong> <br>
                        {{ $form->orderInfo?->customer->address }}<br>
                        {{ $form->orderInfo?->customer->address2 }}<br>
                        {{ $form->orderInfo?->customer->city }}, {{ $form->orderInfo?->customer->state }}
                        {{ $form->orderInfo?->customer->zip }}<br>
                        <i class="fa-solid fa-phone"></i>
                        {{ $form->orderInfo?->customer->phone ? $form->orderInfo?->customer->phone : 'n/a' }}
                        <i class="fa-solid fa-envelope"></i>
                        {{ $form->orderInfo?->customer->email ? $form->orderInfo?->customer->email : 'n/a' }}<br>

                    </address>
                    </p>
                </div>
                <hr>
                <div>
                    <h6 class="text-success">Recommended Address</h6>
                    <p>
                    <address class="ms-1">
                        <Strong>{{ $form->orderInfo?->customer->name }}</Strong> <br>
                        {{ $form->recommendedAddress['formattedAddress'] }}<br>
                        <i class="fa-solid fa-phone"></i>
                        {{ $form->orderInfo?->customer->phone ? $form->orderInfo?->customer->phone : 'n/a' }}
                        <i class="fa-solid fa-envelope"></i>
                        {{ $form->orderInfo?->customer->email ? $form->orderInfo?->customer->email : 'n/a' }}<br>

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

    </div>

</div>
