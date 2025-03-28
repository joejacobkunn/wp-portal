<div class="row">
    <div class="col-8 col-md-8 col-xxl-8">
        <div class="card border rounded shadow-sm mb-4">
            <div class="card-body schedule-view">
                @if ($form->schedule->status != 'completed')
                    <div class="alert alert-light-secondary color-secondary"> Actions
                        @include('livewire.scheduler.schedule.partial.ahm_button_grp')
                    </div>
                @endif
                <div class="collapse-container" wire:key="actionArea-{{ $form->schedule->status }}">
                    <div class="collapse @if ($actionStatus == 'cancel') show @endif p-4" id="cancelCollapse"
                        data-bs-parent=".collapse-container" wire:key="cancel-section-{{ $form->schedule->status }}">
                        <div class="card card-body mb-0 p-0">
                            You are cancelling this schedule. Provide a reason in below field
                            <div class="col-md-12 mt-3">
                                <x-forms.input label="Reason" model="form.cancel_reason" />
                                <x-forms.checkbox label="Notify Customer" name="notifyUser" :value="1"
                                    model="form.notifyUser" />
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
                    <div class="collapse @if ($actionStatus == 'uncacnel') show @endif p-4" id="undoCancelCollapse"
                        data-bs-parent=".collapse-container">
                        <div class="card card-body mb-0 p-0">
                            You are reinstating this schedule. Please ensure to confirm the SRO afterward.
                            <div class="col-md-12 mt-3">
                                <div class="mt-2 float-start">
                                    <button wire:click="undoCancel" class="btn btn-sm btn-warning">
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
                    <div class="collapse @if ($actionStatus == 'complete') show @endif p-4" id="completeCollapse"
                        data-bs-parent=".collapse-container">
                        <div class="card card-body mb-2 p-0">
                            Click the Complete button below to mark the schedule as complete.
                            <div class="col-md-12 mt-2">
                                <x-forms.checkbox label="Notify Customer" name="notifyUser" :value="1"
                                    model="form.notifyUser" />
                                <div class="mt-4 float-start">
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
                    <div class="collapse @if ($actionStatus == 'confirm') show @endif p-4" id="confirmCollapse"
                        data-bs-parent=".collapse-container">
                        <div class="card card-body mb-0 p-0">
                            @if ($orderErrorStatus && $sro_response && $actionStatus == 'confirm')
                                <div class="alert alert-light-danger color-danger"><i
                                        class="bi bi-exclamation-triangle"></i>
                                    The Order associated with SX #{{ $sro_response['sx_repair_order_no'] }}
                                    is not found.</div>
                            @endif
                            Confirm this schedule by clicking {{ $showConfirmMessage ? 'Proceed' : 'Confirm' }} below.
                            @if ($showConfirmMessage && $sro_response && $actionStatus == 'confirm')
                                <div class="alert alert-light-warning color-warning"><i
                                        class="bi bi-exclamation-triangle"></i>
                                    The warehouse associated with SX #{{ $sro_response['sx_repair_order_no'] }}
                                    is different from the truck assigned. Do you still want to proceed?.</div>
                            @endif

                            <div class="col-md-12">
                                <div class="mt-4 float-start">
                                    <button wire:click="confirmedSchedule"
                                        class="btn btn-sm btn-primary @if (!$showConfirmMessage) d-none @endif ">
                                        <div wire:loading wire:target="confirmedSchedule">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                        </div>
                                        <i class="far fa-calendar-check"></i> Proceed
                                    </button>
                                    <button wire:click="confirmSchedule"
                                        class="btn btn-sm btn-primary @if ($showConfirmMessage) d-none @endif">
                                        <div wire:loading wire:target="confirmSchedule">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                        </div>
                                        <i class="far fa-calendar-check"></i> Confirm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="collapse @if ($actionStatus == 'unlink') show @endif p-4" id="unlinkCollapse"
                        data-bs-parent=".collapse-container" wire:key="unlinksro-section">
                        <div class="card card-body mb-0 p-0">
                            You are about to unlink SRO Number. Click below unlink button to confirm.
                            <div class="col-md-12">
                                <div class="mt-4 float-start">
                                    <button wire:click="unlinkSro" class="btn btn-sm btn-primary">
                                        <div wire:loading wire:target="unlinkSro">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                        </div>
                                        <i class="fa fa-unlink"></i> Unlink
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="collapse @if ($actionStatus == 'unconfirm') show @endif p-4" id="unconfirmCollapse"
                        data-bs-parent=".collapse-container">
                        <div class="card card-body mb-0 p-0">
                            Unconfirm this schedule and unlink the SRO# by clicking unconfirm below.
                            <div class="col-md-12">
                                <div class="mt-4 float-start">
                                    <button wire:click="cancelConfirm" class="btn btn-sm btn-danger">
                                        <div wire:loading wire:target="cancelConfirm">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                        </div>
                                        <i class="fas fa-solid fa-xmark"></i> Unconfirm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="collapse @if ($actionStatus == 'start') show @endif p-4"
                        id="startScheduleCollapse" data-bs-parent=".collapse-container">
                        <div class="card card-body mb-0 p-0">
                            @if ($startedSchedules > 0)
                                <div class="alert alert-light-warning color-warning"><i
                                        class="bi bi-exclamation-triangle"></i> Cannot start when another appointment
                                    is in Progress</div>
                            @else
                                Click on the Start button below to Start the Schedule.
                                <div class="col-md-12 mt-2">
                                    <x-forms.checkbox label="Notify user" name="notifyUser" :value="1"
                                        model="form.notifyUser" />
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
                            @endif
                        </div>
                    </div>
                    <div class="collapse @if ($actionStatus == 'reschedule') show @endif p-4" id="rescheduleCollapse"
                        data-bs-parent=".collapse-container">
                        <div class="col-md-12 mb-3">
                            <div wire:loading wire:loading.target="changeStatus('reschedule')">
                                <span class="spinner-border spinner-border-sm mr-2" role="status"
                                    aria-hidden="true"></span>
                                <span>please wait loading schedule form ...</span>
                            </div>
                        </div>
                        <div class="card card-body mb-0 p-0 mb-2">
                            Reschedule this schedule to another date
                            <form wire:submit.prevent="save()">
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <x-forms.select label="Scheduling Priority" model="form.scheduleType"
                                                :options="$schedulePriority" :hasAssociativeIndex="true" :listener="'scheduleTypeChange'"
                                                default-option-label="- None -" :selected="$form->scheduleType" :key="'scheduleTypeKey-' . md5(uniqid())" />
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
                                    @if ($form->schedule_date)

                                        <div wire:loading.remove wire:target="updateFormScheduleDate"
                                            class="col-md-6 {{ $form->schedule_date && !$showTypeLoader ? '' : 'd-none' }} overflow-auto"
                                            style="height: 400px">
                                            <label class="form-label">Available Time Slots on
                                                {{ Carbon\Carbon::parse($form->schedule_date)->toFormattedDayDateString() }}</label>
                                            @if ($form->scheduleType == 'schedule_override')
                                                <p class="ps-2 bg-warning text-dark rounded"><i
                                                        class="fas fa-exclamation-triangle"></i> Schedule Override Mode
                                                    ON</p>
                                            @endif
                                            <div class="d-flex flex-column gap-2">

                                                @forelse($this->form->truckSchedules as $schedule)
                                                    <a href="javascript:void(0)"
                                                        wire:click.prevent="selectSlot({{ $schedule->id }})"
                                                        class="list-group-item list-group-item-action
                                                        @if ($schedule->schedule_count >= $schedule->slots && $form->scheduleType != 'schedule_override') d-none @endif">
                                                        <div
                                                            class="p-3 bg-light rounded border @if ($schedule->id == $form->schedule_time) border-3 border-primary @endif">
                                                            {{ $schedule->start_time . ' - ' . $schedule->end_time }}
                                                            @if ($form->scheduleType != 'schedule_override')
                                                                <span
                                                                    class="badge bg-secondary badge-pill badge-round ms-1 float-end">
                                                                    {{ $schedule->schedule_count }} /
                                                                    {{ $schedule->slots }}
                                                                </span>
                                                            @endif
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
                                    @endif
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
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <x-forms.checkbox label="Notify Customer" name="notifyUser"
                                                :value="1" model="form.notifyUser" />
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

                    </div>
                </div>

                <div class="alert alert-light-{{ $form->schedule->status_color_class }} color-{{ $form->schedule->status_color_class }}"
                    role="alert">
                    <h4 class="alert-heading">Schedule #{{ $form->schedule->scheduleId() }}</h4>
                    @if ($form->schedule->status == 'cancelled')
                        <p><i class="far fa-calendar-check"></i> AHM was <strong>Cancelled</strong> by
                            {{ $form->schedule->cancelledUser->name }} at
                            {{ \Carbon\Carbon::parse($form->schedule->cancelled_at)->toFormattedDayDateString() }}
                        </p>
                        <hr>
                        <p class="mb-0">
                            {{ $form->schedule->cancel_reason }}
                        </p>
                    @endif
                    @if (
                        $form->schedule->status == 'scheduled' ||
                            $form->schedule->status == 'confirmed' ||
                            $form->schedule->status == 'scheduled_linked')
                        <p><i class="far fa-calendar-check"></i>
                            {{ $form->schedule->status == 'scheduled_linked' ? 'AHM has been' : 'AHM is' }}
                            {{ App\Enums\Scheduler\ScheduleStatusEnum::tryFrom($form->schedule->status)->label() }} for
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
                        </p>
                        @if ($form->schedule->truckSchedule->driver_id)
                            <p class="mt-2">Driven by
                                <span class="badge bg-{{ $form->schedule->status_color_class }}">
                                    <i class="fas fa-user-tag"></i>
                                    {{ $form->schedule->truckSchedule->driver?->name }}</span>
                            </p>
                        @endif
                    @endif
                    @if ($form->schedule->status == 'completed')
                        <p><i class="far fa-calendar-check"></i> AHM scheduled in
                            <span class="badge bg-{{ $form->schedule->status_color_class }}"><i
                                    class="fas fa-globe"></i>
                                {{ $form->schedule->truckSchedule->zone->name }}</span>
                            between <strong>{{ $form->schedule->truckSchedule->start_time }} and
                                {{ $form->schedule->truckSchedule->end_time }}</strong> is Completed
                        </p>
                        <hr>
                        <p class="mb-0">Completed by <span
                                class="badge bg-{{ $form->schedule->status_color_class }}">
                                {{ $form->schedule->completedUser->name }}</span>
                            at <span class="badge bg-{{ $form->schedule->status_color_class }}">
                                {{ \Carbon\Carbon::parse($form->schedule->completed_at)->toDayDateTimeString() }}
                            </span>
                        </p>
                    @endif
                    @if ($form->schedule->status == 'out_for_delivery')
                        <p><i class="far fa-calendar-check"></i> Tech in Progress
                        </p>
                        <hr>
                        <p class="mb-0"><span class="badge bg-{{ $form->schedule->status_color_class }}"><i
                                    class="fas fa-truck"></i>
                                {{ $form->schedule->truckSchedule->truck->truck_name }}</span>
                            is serving <span class="badge bg-{{ $form->schedule->status_color_class }}"><i
                                    class="fas fa-globe"></i>
                                {{ $form->schedule->truckSchedule->zone->name }}</span>
                            on this day.</p>
                        @if ($form->schedule->truckSchedule->driver_id)
                            <p class="mt-2">Driven by
                                <span class="badge bg-{{ $form->schedule->status_color_class }}">
                                    <i class="fas fa-user-tag"></i>
                                    {{ $form->schedule->truckSchedule->driver?->name }}</span>
                            </p>
                        @endif
                    @endif
                </div>

                @if (!empty($sro_response) && $form->schedule->sro_number != null)
                    <div class="alert alert-secondary">
                        <h4 class="alert-heading"><i class="fas fa-check-circle"></i>
                            <span class="badge bg-secondary float-end"><a
                                    href="{{ config('sro.url') . 'dashboard/repair-orders/' . $sro_response['id'] }}"
                                    target="_blank"><i class="fas fa-external-link-alt"></i> SRO
                                    #{{ $form->schedule->sro_number }}</a></span>
                            {{ str($sro_response['first_name'] . ' ' . $sro_response['last_name'])->title() }}
                        </h4>

                        <ul class="list-group mt-4">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span> SX Order Number</span>
                                <span
                                    class="badge bg-light-secondary badge-pill badge-round ms-1">{{ $sro_response['sx_repair_order_no'] }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span> Equipment</span>
                                <span
                                    class="badge bg-light-secondary badge-pill badge-round ms-1">{{ $sro_response['brand'] }}
                                    {{ $sro_response['model'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span> Address</span>
                                <span
                                    class="badge bg-light-secondary badge-pill badge-round ms-1">{{ $sro_response['address'] }},
                                    {{ $sro_response['city'] }},
                                    {{ $sro_response['state'] }}, {{ $sro_response['zip'] }}</span>
                            </li>
                            @if (!empty($sro_response['note']))
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span> Note</span>
                                    <span
                                        class="badge bg-light-secondary badge-pill badge-round ms-1">{{ $sro_response['note'] }}</span>
                                </li>
                            @endif
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span> Status</span>
                                <span
                                    class="badge bg-light-secondary badge-pill badge-round ms-1">{{ $sro_response['status'] }}</span>
                            </li>
                        </ul>

                    </div>
                @endif
                @if (
                    $form->schedule->status == 'scheduled' && $form->schedule->sro_number == null)
                    <div class="col-12 col-md-12 col-xxl-12">
                        Tie this order with a SRO number to proceed with confirming and completing this schedule
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
                            @if ($orderErrorStatus)
                                <div class="alert alert-light-danger color-danger"><i
                                        class="bi bi-exclamation-triangle"></i>
                                    The Order associated with SX #{{ $sro_response['sx_repair_order_no'] }}
                                    is not found.</div>
                            @endif
                            @if ($showConfirmMessage)
                                <div class="alert alert-light-danger color-danger"><i
                                        class="bi bi-exclamation-triangle"></i>
                                    The warehouse associated with SX #{{ $sro_response['sx_repair_order_no'] }}
                                    is different from the truck assigned.</div>
                            @endif
                            @if (!$orderErrorStatus && !$showConfirmMessage)
                                <x-forms.checkbox label="SRO Info matches this scheduled AHM appointment"
                                    name="sro_verified" :value="1" model="sro_verified" />
                            @endif
                        @endif
                        <div class="mt-4 mb-4">
                            <button @if (!$sro_verified) disabled @endif wire:click="linkSRO"
                                class="btn btn-sm btn-success">
                                <div wire:loading wire:target="linkSRO">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                </div>
                                <i class="fas fa-link"></i> Link SRO
                            </button>
                        </div>

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

                    @unless ($form->schedule->sro_number)

                        <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                            <div>
                                <h3 class="h6 mb-1">
                                   Equipment</h3>
                                @if ($form->schedule->line_item)
                                    @foreach ($form->schedule->line_item as $key => $item)
                                        <p class="small pe-4">
                                            {{ $item }}
                                            ({{ $key }})
                                        </p>
                                    @endforeach
                                @else
                                    <p class="small pe-4"><em>Not purchased from Weingartz</em></p>
                                @endif
                            </div>
                        </li>
                    @endunless
                    @if (!empty($form->schedule->serial_no))
                        <li
                            class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                            <div>
                                <h3 class="h6 mb-1">Equipment Serial Number</h3>
                                <p class="small pe-4">{{ $form->schedule->serial_no }}</p>
                            </div>
                        </li>
                    @endif
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">SX Order Number</h3>
                            <p class="small pe-4">
                                <span class="badge bg-light-primary"> <a
                                        href="{{ route('order.show', $form->schedule->order->id) }}"
                                        target="_blank"><i class="fas fa-external-link-alt"></i>
                                        {{ $form->schedule->sx_ordernumber . '-' . $form->schedule->order_number_suffix }}</a>
                                </span>
                            </p>
                        </div>
                    </li>

                    @unless ($form->schedule->sro_number)
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                            <div>
                                <h3 class="h6 mb-1">SRO Number</h3>
                                <p class="small pe-4">
                                    @if ($form->schedule->sro_number == null)
                                        <span class="bg-warning text-dark"> Link SRO
                                            to view SRO Info</span>
                                    @else
                                        {{ $form->schedule->sro_number }}
                                    @endif
                                </p>
                            </div>
                        </li>
                    @endunless

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
                <span class="badge bg-light-info float-end"><a
                        href="{{ route('core.customer.show', $form->schedule->order?->customer?->id) }}"
                        target="_blank"><i class="fas fa-external-link-alt"></i> CustNo
                        #{{ $form->schedule->order->customer?->sx_customer_number }}</a></span>
                <h3 class="h5 mb-0">Customer Info</h3>
            </div>

            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Name</h3>
                            <p class="small pe-4">
                                {{ $form->schedule->order->customer?->name }}</p>
                        </div>
                    </li>
                    @if (!empty($form->phone) || !empty($form->schedule->order->customer->phone))
                        <li
                            class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                            <div>
                                <h3 class="h6 mb-1">Phone</h3>
                                @php
                                    $phone = $form->phone ?? $form->schedule->order?->customer?->phone;
                                @endphp
                                <p class="small pe-4 {{ $phone ? '' : 'text-danger' }}">
                                    {{ $phone ? format_phone($phone) : 'Contact Phone is missing' }}</p>
                            </div>
                        </li>
                    @endif
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Email</h3>
                            @php
                                $email = $form->email ?? $form->schedule->order->customer->email;
                            @endphp
                            <p class="small pe-4 {{ $email ? '' : 'text-danger' }}">
                                {!! $email ? $email : '<i class="fas fa-exclamation-triangle"></i> Email is missing' !!}
                            </p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Address</h3>
                            <p class="small pe-4">
                                {{ $form->schedule->order->customer?->getFullAddress() }}</p>
                        </div>
                    </li>
                    <a href="javascript:void(0)" class="mt-2 link-primary" wire:click="showEditContactModal">
                        <i class="fas fa-edit schedule-edit-icon"></i> Edit Contact
                    </a>
                </ul>
            </div>
        </div>
        <x-tabs :tabs="$this->tabs" tabId="schedule-comment-tabs" activeTabIndex="active">
            <x-slot:tab_content_comments component="x-comments" :entity="$form->schedule" :key="'schedule-comments'">
            </x-slot>

            <x-slot:tab_content_activity component="x-activity-log" :entity="$form->schedule" :key="'schedule-activity'">
            </x-slot>
        </x-tabs>
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
                    <button type="submit" class="btn btn-primary" wire:click="updateContactSchedule">
                        <div wire:loading wire:target="updateContactSchedule">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        Update
                    </button>
                </x-slot>
            </x-modal>
        @endif
    </div>
</div>
