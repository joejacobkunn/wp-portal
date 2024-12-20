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
                            <x-forms.input type="number" label="Order Number" model="form.sx_ordernumber" lazy />
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="form-group">
                            <x-forms.input type="number" label="Order Number Suffix" model="form.suffix" lazy />
                        </div>
                    </div>
                    @if($form->alertConfig['status'])
                    <div class="col-md-12 mb-1">
                        <p class="text-{{$form->alertConfig['class']}}"><i class="far {{$form->alertConfig['icon']}}"></i> {!! $form->alertConfig['message'] !!}
                            @if($form->alertConfig['show_url'])
                            <a target="_blank" href="{{route($form->alertConfig['url'])}}?{{$form->alertConfig['params']}}" target="_blank"> {{$form->alertConfig['urlText']}}<i
                                class="fas fa-external-link-alt"></i></a>
                            @endif
                        </p>
                    </div>
                    @endif
                    <div wire:loading wire:target="form.suffix" class="mb-3">
                        <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                        <span>Please wait, processing order...</span>
                    </div>

                    @if ($form->orderInfo && is_array($form->orderInfo->shipping_info))
                        <div class="col-md-12 mb-3" wire:loading.remove wire:target="form.suffix">
                            <div class="alert alert-light-primary color-primary" role="alert">
                                <span class="badge bg-light-warning float-end"><a target="_blank" href=""><i
                                            class="fas fa-external-link-alt"></i> CustNo
                                        #{{ $form->orderInfo?->sx_customer_number }}</a></span>
                                <h4 class="alert-heading">Service Address</h4>
                                <p>
                                <address class="ms-1">
                                    <Strong>{{ $form->orderInfo?->customer->name }}</Strong> <br>
                                    {{ $form->orderInfo?->customer->address }}<br>
                                    {{ $form->orderInfo?->customer->address2 }}<br>
                                    {{ $form->orderInfo?->customer->city }}, {{ $form->orderInfo?->customer->state }}
                                    {{ $form->orderInfo?->customer->zip }}<br>
                                    <i class="fa-solid fa-phone"></i>
                                    {{ $form->orderInfo?->customer->phone ? $form->orderInfo?->customer->phone : 'NA' }}
                                    <i class="fa-solid fa-envelope"></i>
                                    {{ $form->orderInfo?->customer->email ? $form->orderInfo?->customer->email : 'NA' }}<br>

                                </address>
                                </p>
                                <hr>
                                <p class="mb-0">Ship To : {{ $form->orderInfo->shipping_info['shipto'] ?: 'n/a' }}
                                </p>
                                <p class="mb-0">Shipping Instructions :
                                    {{ $form->orderInfo->shipping_info['instructions'] ?: 'n/a' }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($form->orderInfo?->line_items)
                        <div class="col-md-12 mb-3">
                            <ul class="list-group">
                                <li class="list-group-item list-group-item-primary">
                                    <span class="float-end fst-italic fw-light">Select all that apply</span>
                                    Line Items
                                </li>
                                @foreach ($form->orderInfo->line_items['line_items'] as $item)
                                    <li class="list-group-item">
                                        <x-forms.checkbox :label="$item['descrip'] . '(' . $item['shipprod'] . ')'" :name="'lineItems[]'" :value="$item['shipprod']"
                                            :model="'form.line_items'" />
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-forms.datepicker type="date" label="Schedule Date" model="form.schedule_date" :disabled="$form->scheduleDateDisable" lazy />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-forms.input type="time" prependIcon="fas fa-clock" label="Schedule Time"
                            :disabled="$form->scheduleDateDisable" model="form.schedule_time" lazy />
                        </div>
                    </div>
                </div>
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

                <li class="list-group-item"><strong>Stage</strong> <span class="float-end">{{$form->orderInfo->getStageCode()}}</span></li>
                <li class="list-group-item"><strong>Warehouse</strong> <span
                        class="float-end">{{ strtoupper($form->orderInfo->whse) }}</span></li>
                <li class="list-group-item"><strong>Placed</strong> <span
                    class="float-end"> {{$form->orderInfo->order_date?->format(config('app.default_datetime_format'))}}</span></li>
                <li class="list-group-item"><strong>Taken By</strong> <span
                    class="float-end">{{$form->orderInfo->taken_by}}</span></li>
                {{-- <li class="list-group-item"><strong>Amount</strong> <span
                    class="float-end">${{ number_format($form->orderInfo->sx_order->totordamt, 2) }}</span></li> --}}
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
                        <a href="{{route('service-area.zipcode.show', ['zipcode' => $form->zipcodeInfo->id])}}"><i
                            class="fas fa-external-link-alt"></i>
                        #{{ $form->zipcodeInfo?->getZone->name }}</a>
                    @else
                        <a href="{{route('service-area.index')}}?tab=zip_code"><i
                            class="fas fa-external-link-alt"></i>
                        create new</a>
                    @endif

                </span>
                Zipcode Info
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
        @if ($form->zipcodeInfo)

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr class="bg-light-primary">
                            <th colspan="4" class="text-center color-primary ">Slot Info</th>
                        </tr>
                        <tr>
                            <th class="bg-light">Day</th>
                            <th class="bg-light">AHM Slot</th>
                            <th class="bg-light">Delivery/Pickup Slot</th>
                            <th class="bg-light">Shift</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($form->zipcodeInfo?->getZone?->schedule_days as $key => $day)
                            @if ($day['enabled'])
                                <tr>
                                    <td>{{ ucfirst($key) }}</td>
                                    <td>{{ $day['ahm_slot'] }}</td>
                                    <td>{{ $day['pickup_delivery_slot'] }}</td>
                                    <td>{{ strtoupper(str_replace(['_'], ' ', $day['schedule'])) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</div>
