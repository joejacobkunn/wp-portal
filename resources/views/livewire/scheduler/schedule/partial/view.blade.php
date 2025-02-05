<div class="row">
    <div class="col-8 col-md-8 col-xxl-8">
        <div class="card border rounded shadow-sm mb-4">

            <div class="card-body">
                @if ($form->schedule->status != 'Completed')
                    <div class="alert alert-light-secondary color-secondary"> Actions
                        <div class="btn-group mt-n1 mb-3 float-end" role="group" aria-label="Basic example">
                            @can('scheduler.schedule.manage')
                                @if ($form->schedule->status != 'Cancelled' && $form->schedule->status != 'Completed')
                                    <button type="button" class="btn btn-sm btn-danger" wire:click="hideScheduleSection"
                                        data-bs-toggle="collapse" data-bs-target="#cancelCollapse" aria-expanded="false"
                                        aria-controls="cancelCollapse"><i class="far fa-calendar-times"></i>
                                        Cancel</button>
                                @endif

                                @if ($form->schedule->status == 'Cancelled')
                                    <button type="button" class="btn btn-sm btn-success" wire:click="hideScheduleSection"
                                        data-bs-toggle="collapse" data-bs-target="#undoCancelCollapse" aria-expanded="false"
                                        aria-controls="undoCancelCollapse"><i class="fas fa-undo"></i>
                                        Uncancel</button>
                                @endif
                                @if ($form->schedule->status == 'Scheduled')
                                    <button type="button" class="btn btn-sm btn-warning" wire:click="scheduleDateInitiate"
                                        data-bs-toggle="collapse" data-bs-target="#rescheduleCollapse" aria-expanded="false"
                                        aria-controls="rescheduleCollapse"><i class="fas fa-redo"></i>
                                        Reschedule</button>
                                @endif
                                @if ($form->schedule->status == 'Scheduled')
                                    <button type="button" class="btn btn-sm btn-primary" wire:click="hideScheduleSection"
                                        data-bs-toggle="collapse" data-bs-target="#confirmCollapse" aria-expanded="false"
                                        aria-controls="confirmCollapse"><i class="fas fa-check-double"></i>
                                        Confirm</button>
                                @endif
                                @if ($form->schedule->status == 'Confirmed')
                                    <button type="button" class="btn btn-sm btn-secondary" wire:click="hideScheduleSection"
                                        data-bs-toggle="collapse" data-bs-target="#unconfirmCollapse" aria-expanded="false"
                                        aria-controls="unconfirmCollapse"><i class="fas fa-solid fa-xmark"></i>
                                        Unconfirm</button>
                                @endif
                            @endcan
                            @can('scheduler.driver')
                                @if ($form->schedule->status == 'Confirmed')
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="collapse"
                                        data-bs-target="#startScheduleCollapse" aria-expanded="false"
                                        aria-controls="startScheduleCollapse"><i class="fas fa-check-circle"></i>
                                        Start</button>
                                @endif
                            @endcan
                            @canany(['scheduler.driver', 'scheduler.schedule.manage'])
                                @if ($form->schedule->status == 'Out for Delivery')
                                    <button type="button" class="btn btn-sm btn-success" wire:click="hideScheduleSection"
                                        data-bs-toggle="collapse" data-bs-target="#completeCollapse" aria-expanded="false"
                                        aria-controls="completeCollapse"><i class="fas fa-check-circle"></i>
                                        Complete</button>
                                @endif
                            @endcan

                        </div>

                    </div>
                @endif
                <div class="collapse-container" wire:key="actionArea-{{ $form->schedule->status }}">
                    <div class="collapse p-4" id="cancelCollapse" data-bs-parent=".collapse-container"
                        {!! $form->schedule->status != 'Cancelled' ? 'wire:ignore.self' : '' !!} wire:key="cancel-section-{{ $form->schedule->status }}">
                        <div class="card card-body mb-0 p-0">
                            You are cancelling this schedule. Provide a reason in below field
                            <div class="col-md-12 mt-3">
                                <x-forms.input label="Reason" model="form.cancel_reason" />
                                <div class="mt-4 float-start">
                                    <button wire:click="cancelSchedule" class="btn btn-sm btn-danger">
                                        <div wire:loading wire:target="cancelSchedule">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                        </div>
                                        <i class="far fa-calendar-times"></i> Cancel Schedule
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="collapse p-4" id="undoCancelCollapse" data-bs-parent=".collapse-container"
                        {!! $form->schedule->status == 'Cancelled' ? 'wire:ignore.self' : '' !!}>
                        <div class="card card-body mb-0 p-0">
                            You are reinstating this schedule. Please ensure to confirm the SRO afterward.
                            <div class="col-md-12 mt-3">
                                <div class="mt-2 float-start">
                                    <button wire:click="undoCancel" class="btn btn-sm btn-success">
                                        <div wire:loading wire:target="undoCancel">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                        </div>
                                        <i class="fas fa-undo"></i> Undo Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="collapse p-4" id="completeCollapse" data-bs-parent=".collapse-container"
                        {!! $form->schedule->status != 'Completed' ? 'wire:ignore.self' : '' !!}>
                        <div class="card card-body mb-0 p-0">
                            Click the Complete button below to mark the schedule as complete.
                            <div class="col-md-12 mt-3">
                                <div class="mt-2 float-start">
                                    <button wire:click="completeSchedule" class="btn btn-sm btn-success">
                                        <div wire:loading wire:target="completeSchedule">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                        </div>
                                        <i class="fas fa-check-circle"></i> Complete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="collapse p-4" id="confirmCollapse" data-bs-parent=".collapse-container"
                        wire:ignore.self>
                        <div class="card card-body mb-0 p-0">
                            @if ($form->schedule->status == 'Scheduled')
                                Confirm this schedule by linking the correct SRO# and click confirm below. Click on
                                confirm
                                again to cancel
                                <div class="col-md-12 mt-3">
                                    <x-forms.input label="SRO Number" model="sro_number" live />
                                    @if (!empty($sro_response))
                                        <div class="alert alert-secondary">
                                            <h4 class="alert-heading"><i class="fas fa-check-circle"></i>
                                                {{ $sro_response['first_name'] }} {{ $sro_response['last_name'] }}</h4>
                                            <p><span class="badge bg-light-secondary"><i class="fas fa-tractor"></i>
                                                    {{ $sro_response['brand'] }} {{ $sro_response['model'] }}</span></p>
                                            <p><span class="badge bg-light-secondary"><i
                                                        class="fas fa-map-marker-alt"></i>
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
                            @endif
                        </div>
                    </div>
                    <div class="collapse p-4" id="unconfirmCollapse" data-bs-parent=".collapse-container"
                        wire:ignore.self>
                        <div class="card card-body mb-0 p-0">
                            Unconfirm this schedule and unlink the SRO# by clicking unconfirm below.
                            <div class="col-md-12">
                                <div class="mt-4 float-start">
                                    <button wire:click="cancelConfirm" class="btn btn-sm btn-danger">
                                        <div wire:loading wire:target="cancelConfirm">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                        </div>
                                        <i class="far fa-calendar-times"></i> Unconfirm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="collapse p-4" id="startScheduleCollapse" data-bs-parent=".collapse-container"
                        wire:ignore.self>
                        <div class="card card-body mb-0 p-0">
                            Click on the Start button below to Start the Delivery Process.
                            <div class="col-md-12">
                                <div class="mt-4 float-start">
                                    <button wire:click="startSchedule" class="btn btn-sm btn-warning">
                                        <div wire:loading wire:target="startSchedule">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                        </div>
                                        <i class="far fa-check-circle"></i> Start
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="collapse @if ($viewScheduleTypeCollapse) show @endif p-4" id="rescheduleCollapse"
                        data-bs-parent=".collapse-container">
                        <div class="col-md-12 mb-3">
                            <div wire:loading wire:loading.target="scheduleDateInitiate">
                                <span class="spinner-border spinner-border-sm mr-2" role="status"
                                    aria-hidden="true"></span>
                                <span>please wait loading schedule form ...</span>
                            </div>
                        </div>
                        @if ($viewScheduleTypeCollapse)
                            <div class="card card-body mb-0 p-0">
                                Reschedule this schedule to another date select the schedule type then choose date and
                                timeslot.
                                <form wire:submit.prevent="save()">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <x-forms.select label="Scheduling Priority" model="form.scheduleType"
                                                    :options="[
                                                        'next_avail' => 'Next Available Date',
                                                        'one_year' => 'One Year from Now',
                                                    ]" :hasAssociativeIndex="true" :listener="'scheduleTypeChange'"
                                                    default-option-label="- None -" :selected="$form->scheduleType"
                                                    :key="'scheduleTypeKey'" />
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
                                        <div
                                            class="col-md-6 {{ !$showTypeLoader && $form->scheduleType ? '' : 'd-none' }}">
                                            <div class="form-group">
                                                <label for="datepicker" class="form-label">Select Date</label>
                                                <div wire:ignore>
                                                    <input type="text" wire:key="scheduleDateKey" id="datepicker"
                                                        class="form-control" wire:model.defer="form.schedule_date"
                                                        x-data="{
                                                            enabledDates: @js($this->getEnabledDates()),
                                                            flatpickrInstance: null
                                                        }" x-init="flatpickrInstance = flatpickr($el, {
                                                            inline: true,
                                                            dateFormat: 'Y-m-d',
                                                            defaultDate: '{{ $form->schedule_date }}',
                                                            enable: enabledDates,
                                                            minDate: new Date(),
                                                            setDate: '{{ $form->schedule_date }}',
                                                            onChange: function(selectedDates, dateStr) {
                                                                $wire.updateFormScheduleDate(dateStr);
                                                            }
                                                        });"
                                                        x-on:enable-date-update.window="
                                                                if (flatpickrInstance) {
                                                                    flatpickrInstance.set('enable', $event.detail.enabledDates);
                                                                }
                                                            "
                                                        x-on:set-enabled-dates.window="
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
                                            <div
                                                class="d-flex justify-content-center align-items-center h-100 w-100 py-3">
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
                                                            <span
                                                                class="badge bg-secondary badge-pill badge-round ms-1 float-end">
                                                                {{ $schedule->schedule_count }} /
                                                                {{ $schedule->slots }}
                                                            </span>
                                                            <p class="me-2 fst-italic text-muted"
                                                                style="font-size: smaller;"><i
                                                                    class="fas fa-globe"></i>
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
                                        @error('form.schedule_time')
                                            <div class="col-md-12">
                                                <span class="text-danger">{{ $message }}</span>
                                            </div>
                                        @enderror
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <x-forms.input type="text" label="Reason"
                                                    model="form.reschedule_reason" />
                                            </div>
                                        </div>
                                        {{-- end of timeslots listing --}}
                                        <div class="col-md-12">
                                            <div class="mt-4 float-start">
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    <div wire:loading wire:target="save">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                    </div>
                                                    <i class="fas fa-redo"></i> Reschedule
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="alert alert-light-{{ $form->schedule->status_color_class }} color-{{ $form->schedule->status_color_class }}"
                    role="alert">
                    <h4 class="alert-heading">Schedule #{{ $form->schedule->scheduleId() }}</h4>
                    @if ($form->schedule->status == 'Cancelled')
                        <p><i class="far fa-calendar-check"></i> AHM was <strong>Cancelled</strong> by
                            {{ $form->schedule->cancelledUser->name }} at
                            {{ \Carbon\Carbon::parse($form->schedule->cancelled_at)->toFormattedDayDateString() }}
                        </p>
                        <hr>
                        <p class="mb-0">
                            {{ $form->schedule->cancel_reason }}
                        </p>
                    @endif
                    @if ($form->schedule->status == 'Scheduled' || $form->schedule->status == 'Confirmed')
                        <p><i class="far fa-calendar-check"></i> AHM is {{ $form->schedule->status }} for
                            <strong>{{ $form->schedule->schedule_date->toFormattedDayDateString() }}</strong> between
                            <strong>{{ $form->schedule->truckSchedule->start_time }} and
                                {{ $form->schedule->truckSchedule->end_time }}</strong>
                        </p>
                        <hr>
                        <p class="mb-0"><span class="badge bg-{{ $form->schedule->status_color_class }}"><i
                                    class="fas fa-truck"></i>
                                {{ $form->schedule->truckSchedule->truck->truck_name }}</span>
                            is serving <span class="badge bg-{{ $form->schedule->status_color_class }}"><i
                                    class="fas fa-globe"></i>
                                {{ $form->schedule->truckSchedule->zone->name }}</span>
                            on this day.
                            @if ($form->schedule->truckSchedule->driver_id)
                                Driven by
                                <span class="badge bg-{{ $form->schedule->status_color_class }}">
                                    <i class="fas fa-user-tag"></i>
                                    {{ $form->schedule->truckSchedule->driver?->name }}</span>
                            @endif
                        </p>
                    @endif
                    @if ($form->schedule->status == 'Completed')
                        <p><i class="far fa-calendar-check"></i> AHM is Completed
                        </p>
                        <hr>
                        <p class="mb-0">Completed by <span
                                class="badge bg-{{ $form->schedule->status_color_class }}">
                                {{ $form->schedule->completedUser->name }}</span>
                            Completed at <span class="badge bg-{{ $form->schedule->status_color_class }}">
                                {{ \Carbon\Carbon::parse($form->schedule->completed_at)->toFormattedDayDateString() }}
                            </span>
                        </p>
                    @endif
                    @if ($form->schedule->status == 'Out for Delivery')
                        <p><i class="far fa-calendar-check"></i> Delivery Process initiated
                        </p>
                        <hr>
                        <p class="mb-0"><span class="badge bg-{{ $form->schedule->status_color_class }}"><i
                                    class="fas fa-truck"></i>
                                {{ $form->schedule->truckSchedule->truck->truck_name }}</span>
                            is serving <span class="badge bg-{{ $form->schedule->status_color_class }}"><i
                                    class="fas fa-globe"></i>
                                {{ $form->schedule->truckSchedule->zone->name }}</span>
                            on this day.
                            @if ($form->schedule->truckSchedule->driver_id)
                                Driven by
                                <span class="badge bg-{{ $form->schedule->status_color_class }}">
                                    <i class="fas fa-user-tag"></i>
                                    {{ $form->schedule->truckSchedule->driver?->name }}</span>
                            @endif
                        </p>
                    @endif
                </div>

                @if (!empty($sro_response) && $form->schedule->status != 'Scheduled')
                    <div class="alert alert-secondary">
                        <h4 class="alert-heading"><i class="fas fa-check-circle"></i>
                            {{ $sro_response['first_name'] }} {{ $sro_response['last_name'] }}</h4>
                        <p><span class="badge bg-light-secondary"><i class="fas fa-tractor"></i>
                                {{ $sro_response['brand'] }} {{ $sro_response['model'] }}</span></p>
                        <p><span class="badge bg-light-secondary"><i class="fas fa-map-marker-alt"></i>
                                {{ $sro_response['address'] }}, {{ $sro_response['city'] }},
                                {{ $sro_response['state'] }}, {{ $sro_response['zip'] }}</span></p>
                    </div>
                @endif
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
                                @else
                                    {{ $form->schedule->sro_number }}
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
                    @if (!empty($form->schedule->order->customer->phone))
                        <li
                            class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                            <div>
                                <h3 class="h6 mb-1">Phone</h3>
                                <p class="small pe-4">{{ format_phone($form->schedule->order->customer->phone) }}</p>
                            </div>
                        </li>
                    @endif
                    @if (!empty($form->schedule->order->customer->email))
                        <li
                            class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                            <div>
                                <h3 class="h6 mb-1">Email</h3>
                                <p class="small pe-4">
                                    {{ $form->schedule->order->customer->email }}</p>
                            </div>
                        </li>
                    @endif
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
        <x-tabs :tabs="$tabs" tabId="schedule-comment-tabs" activeTabIndex="active">
            <x-slot:tab_content_comments component="x-comments" :entity="$form->schedule" :key="'comments' . time()">
            </x-slot>

            <x-slot:tab_content_activity component="x-activity-log" :entity="$form->schedule" recordType="floor-model"
                :key="'activity-' . time()">
            </x-slot>
        </x-tabs>
    </div>
</div>
