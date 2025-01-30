<div class="row">
    <div class="col-8 col-md-8 col-xxl-8">
        <div class="card border rounded shadow-sm mb-4">

            <div class="card-body">

                <div class="alert alert-light-secondary color-secondary"> Actions
                    <div class="btn-group mt-n1 mb-3 float-end" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-sm btn-danger"><i class="far fa-calendar-times"></i>
                            Cancel</button>

                        <button type="button" class="btn btn-sm btn-warning"><i class="fas fa-redo"></i>
                            Reschedule</button>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="collapse"
                            data-bs-target="#confirmCollapse" aria-expanded="false" aria-controls="confirmCollapse"><i
                                class="fas fa-check-double"></i>
                            Confirm</button>
                    </div>

                </div>

                <div class="collapse @if ($sro_number) show @endif" id="confirmCollapse">
                    <div class="card card-body">
                        Confirm this schedule by linking the correct SRO# and click confirm below. Click on confirm
                        again to cancel
                        <div class="col-md-12 mt-3">
                            <x-forms.input label="SRO Number" model="sro_number" live />
                            @if (!empty($sro_response))
                                <div class="alert alert-secondary">
                                    <h4 class="alert-heading"><i class="fas fa-check-circle"></i>
                                        {{ $sro_response['first_name'] }} {{ $sro_response['last_name'] }}</h4>
                                    <p><span class="badge bg-light-secondary"><i class="fas fa-tractor"></i>
                                            {{ $sro_response['brand'] }} {{ $sro_response['model'] }}</span></p>
                                    <p><span class="badge bg-light-secondary"><i class="fas fa-map-marker-alt"></i>
                                            {{ $sro_response['address'] }}, {{ $sro_response['city'] }},
                                            {{ $sro_response['state'] }}, {{ $sro_response['zip'] }}</span></p>
                                </div>

                                <x-forms.checkbox label="SRO Info matches this scheduled AHM appointment"
                                    name="sro_verified" :value="1" model="sro_verified" />
                            @endif
                            <div class="mt-4 float-start">
                                <button @if (!$sro_verified) disabled @endif wire:click="linkSRO"
                                    class="btn btn-sm btn-success">
                                    <div wire:loading>
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                    </div>
                                    <i class="fas fa-calendar-check"></i> Link SRO and Confirm AHM
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="alert alert-light-primary color-primary" role="alert">
                    <h4 class="alert-heading">Schedule #{{ $form->schedule->scheduleId() }}</h4>
                    <p><i class="far fa-calendar-check"></i> AHM is scheduled for
                        <strong>{{ $form->schedule->schedule_date->toFormattedDayDateString() }}</strong> between
                        <strong>{{ $form->schedule->truckSchedule->start_time }} and
                            {{ $form->schedule->truckSchedule->end_time }}</strong>
                    </p>
                    <hr>
                    <p class="mb-0"><span class="badge bg-light-secondary"><i class="fas fa-truck"></i>
                            {{ $form->schedule->truckSchedule->truck->truck_name }}</span>
                        is serving <span class="badge bg-light-secondary"><i class="fas fa-globe"></i>
                            {{ $form->schedule->truckSchedule->zone->name }}</span>
                        on this day</p>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Service Address</h3>
                            <p class="small pe-4">
                                {!! $form->schedule->service_address !!}
                            </p>
                        </div>
                    </li>

                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Equipment</h3>
                            <p class="small pe-4">
                                {{ head($form->schedule->line_item) }}
                                ({{ array_keys($form->schedule->line_item)[0] }})
                            </p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">SX Order Number</h3>
                            <p class="small pe-4">
                                {{ $form->schedule->sx_ordernumber . '-' . $form->schedule->order_number_suffix }}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">SRO Number</h3>
                            <p class="small pe-4">
                                @if ($form->schedule->status == 'Scheduled')
                                    <span class="bg-warning text-dark">Confirm
                                        schedule to view SRO Info</span>
                                @endif
                            </p>
                        </div>
                    </li>

                    @if ($form->schedule->notes)
                        <li
                            class="list-group-item d-flex justify-content-between align-items-center px-0 border-bottom">
                            <div>
                                <h3 class="h6 mb-1">Notes</h3>
                                <p class="small mb-0">{{ $form->schedule->notes }}</p>
                            </div>

                        </li>
                    @endif

                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Created By</h3>
                            <p class="small pe-4">{{ $form->schedule->user->name ?? '---' }} on
                                {{ $form->schedule->created_at->toDayDateTimeString() }}</p>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-xxl-4">
        <div class="card border rounded shadow-sm mb-4">
            <div class="card-header border-gray-300 p-3 mb-4 mb-md-0" :key="'bew'.time()">
                <span class="badge bg-light-info float-end">CustNo
                    #{{ $form->schedule->order->customer->sx_customer_number }}</span>
                <h3 class="h5 mb-0">Customer Info</h3>
            </div>

            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Name</h3>
                            <p class="small pe-4">
                                {{ $form->schedule->order->customer->name }}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Phone</h3>
                            <p class="small pe-4">{{ $form->schedule->order->customer->phone }}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Email</h3>
                            <p class="small pe-4">
                                {{ $form->schedule->order->customer->email }}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Address</h3>
                            <p class="small pe-4">
                                {{ $form->schedule->order->customer->getFullAddress() }}</p>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </div>
</div>
