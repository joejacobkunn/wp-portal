<x-page :breadcrumbs="$breadcrumbs">
    <x-slot:title>Schedule</x-slot>
    <x-slot:content>
        <div class="row">
            <div class="col-9">
                <div class="card border-light shadow-sm schedule-tab">
                    <div class="card-body">
                        <div id="calendar" class="w-100" wire:ignore></div>
                        <div id="calendar-dropdown-menu" class="dropdown-menu">
                            <div id="schedule-options">
                                @foreach ($scheduleOptions as $key => $value)
                                    <a class="dropdown-item border-bottom" href="#"
                                        wire:click.prevent="create('{{ $key }}')">{!! $value !!}</a>
                                @endforeach
                            </div>
                            <div id="warehouse-wrap">
                                @foreach ($warehouses as $whse)
                                    <a class="dropdown-item border-bottom" href="#"
                                        wire:click.prevent="changeWarehouse('{{ $whse->id }}')">{{ $whse->title }}</a>
                                @endforeach
                            </div>
                            <div id="type-wrap">
                                <a class="dropdown-item border-bottom" href="#"
                                    wire:click.prevent="changeScheduleType('')">All Services</a>
                                @foreach ($scheduleOptions as $key => $value)
                                    <a class="dropdown-item border-bottom" href="#"
                                        wire:click.prevent="changeScheduleType('{{ $key }}')">{!! $value !!}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-3">
                <h4>Overview for {{ Carbon\Carbon::parse($dateSelected)->toFormattedDayDateString() }}</h4>


                <div class="card border-light shadow-sm schedule-tab">
                    <div class="card-body">
                        <h5 class="card-title">Active Trucks and Zones</h5>
                        @if (count($this->filteredSchedules) > 0)
                            <div class="list-group">
                                <ul class="list-group">
                                    @foreach ($this->filteredSchedules as $truck)
                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold"><a href="{{ route('service-area.zones.show', ['zone' => $truck['zone_id']]) }}">
                                                    <span class="badge bg-light-primary"><i class="fas fa-globe"></i>
                                                        {{ $truck['zone'] }}</span></a>
                                                    => <a href="{{ route('scheduler.truck.show', ['truck' => $truck['truck_id']]) }}">
                                                            <span class="badge bg-light-secondary"><i
                                                            class="fas fa-truck"></i>
                                                        {{ $truck['truck_name'] }}</span></div>
                                                <span class="me-2 fst-italic text-muted" style="font-size: smaller;"><i
                                                        class="far fa-clock"></i>
                                                    {{$truck['start_time']}}
                                                    -
                                                    {{$truck['end_time']}}
                                                    </span>
                                            </div>
                                            <span class="badge bg-primary rounded-pill">{{$truck['scheduled_count']}} /
                                                {{$truck['slots']}}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="alert alert-light-warning color-warning"><i
                                    class="bi bi-exclamation-triangle"></i> No active trucks or zones
                            </div>
                        @endif
                    </div>

                </div>

                <div class="card border-light shadow-sm schedule-tab">
                    <div class="card-body">
                        <h5 class="card-title">Events</h5>

                        <div class="list-group">
                            @forelse ($eventsData as $event)
                                <a href="#" class="list-group-item list-group-item-action" wire:click.prevent="handleEventClick({{$event->id}})">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">Order
                                            #{{ $event->sx_ordernumber }}-{{ $event->order->order_number_suffix }}
                                        </h5>
                                        <small>
                                            <span class="badge bg-light-primary badge-pill badge-round ms-1 float-end">
                                                @if ($event->type == 'at_home_maintenance')
                                                    AHM
                                                @elseif($event->type == 'pickup' || $event->type == 'delivery')
                                                    P / D
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </small>
                                    </div>
                                    <p class="mb-1">
                                        {{ $event->order->customer->name }} - SX#
                                        {{ $event->order->customer->sx_customer_number }}
                                    </p>
                                    <small>{{ $event->order->shipping_info['line'] . ', ' . $event->order->shipping_info['city'] . ', ' . $event->order->shipping_info['state'] . ', ' . $event->order->shipping_info['zip'] }}</small>
                                </a>
                            @empty
                                <div class="alert alert-light-warning color-warning"><i
                                        class="bi bi-exclamation-triangle"></i>
                                    No events for today</div>
                            @endforelse

                        </div>

                    </div>
                </div>
            </div>
        </div>
        @if ($showModal || $isEdit)
            <x-modal toggle="showModal" size="xl" :closeEvent="'closeModal'">
                <x-slot name="title">Schedule
                    {{ Illuminate\Support\Str::of($form->type)->replace('_', ' ')->title() }}</x-slot>
                @if (!$this->showView)
                    @include('livewire.scheduler.schedule.partial.form')
                @else
                    @include('livewire.scheduler.schedule.partial.view')
                @endif
            </x-modal>
        @endif
        @if ($showSlotModal)
            <x-modal toggle="showSlotModal" size="md" :closeEvent="'closeSlotModal'">
                <x-slot name="title">Update Slots</x-slot>
                <form wire:submit.prevent="updateSlot()">
                    <div class="row w-100">
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <x-forms.input type="number" label="Slots" model="truckScheduleForm.slots" lazy />
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 float-start">
                        <button type="submit" class="btn btn-primary">
                            <div wire:loading wire:target="updateSlot">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </div>
                            Update
                        </button>
                    </div>
                </form>
            </x-modal>
        @endif
    </x-slot>
</x-page>

@script
    <script>
        (function() {
            window.initializeCalendar = function() {
                let calendarEl = document.getElementById('calendar');
                let dropdownMenu = document.getElementById('calendar-dropdown-menu');
                let isDropdownVisible = false;
                let schedulesData = @json($schedules);
                let calendar = new FullCalendar.Calendar(calendarEl, {
                    themeSystem: 'bootstrap5',
                    initialView: 'dayGridMonth',
                    height: 'auto',
                    headerToolbar: {
                        left: 'prev,next today dayGridMonth,listDay',
                        center: 'title',
                        right: 'warehouseBtn scheduleBtn dropdownButton'
                    },

                    customButtons: {
                        dropdownButton: {
                            text: 'Schedule',
                            click: function(e) {
                                const button = e.currentTarget;
                                const buttonRect = button.getBoundingClientRect();
                                const calendarRect = calendarEl.getBoundingClientRect();

                                if (isDropdownVisible) {
                                    dropdownMenu.style.display = 'none';
                                    isDropdownVisible = false;
                                } else {
                                    const dropdownWidth = 160;
                                    dropdownMenu.style.top = (buttonRect.bottom + 5) + 'px';
                                    const leftPosition = Math.min(
                                        buttonRect.left,
                                        calendarRect.right - dropdownWidth - 10
                                    );
                                    dropdownMenu.style.left = leftPosition + 'px';
                                    dropdownMenu.style.display = 'block';
                                    isDropdownVisible = true;
                                    document.getElementById('schedule-options').style.display = 'block';
                                    document.getElementById('warehouse-wrap').style.display = 'none';
                                    document.getElementById('type-wrap').style.display = 'none';

                                }
                                e.stopPropagation();
                            }
                        },
                        warehouseBtn: {
                            text: '{{ $activeWarehouse->title }}',
                            click: function(e) {
                                const button = e.currentTarget;
                                const buttonRect = button.getBoundingClientRect();
                                const calendarRect = calendarEl.getBoundingClientRect();

                                if (isDropdownVisible) {
                                    dropdownMenu.style.display = 'none';
                                    isDropdownVisible = false;
                                } else {
                                    const dropdownWidth = 160;
                                    dropdownMenu.style.top = (buttonRect.bottom + 5) + 'px';
                                    const leftPosition = Math.min(
                                        buttonRect.left,
                                        calendarRect.right - dropdownWidth - 10
                                    );
                                    dropdownMenu.style.left = leftPosition + 'px';
                                    dropdownMenu.style.display = 'block';
                                    isDropdownVisible = true;
                                    document.getElementById('warehouse-wrap').style.display = 'block';
                                    document.getElementById('type-wrap').style.display = 'none';
                                    document.getElementById('schedule-options').style.display = 'none';
                                }
                                e.stopPropagation();
                            }
                        },
                        scheduleBtn: {
                            text: 'All Services',
                            click: function(e) {
                                const button = e.currentTarget;
                                const buttonRect = button.getBoundingClientRect();
                                const calendarRect = calendarEl.getBoundingClientRect();

                                if (isDropdownVisible) {
                                    dropdownMenu.style.display = 'none';
                                    isDropdownVisible = false;
                                } else {
                                    const dropdownWidth = 160;
                                    dropdownMenu.style.top = (buttonRect.bottom + 5) + 'px';
                                    const leftPosition = Math.min(
                                        buttonRect.left,
                                        calendarRect.right - dropdownWidth - 10
                                    );
                                    dropdownMenu.style.left = leftPosition + 'px';
                                    dropdownMenu.style.display = 'block';
                                    isDropdownVisible = true;
                                    document.getElementById('warehouse-wrap').style.display = 'none';
                                    document.getElementById('schedule-options').style.display = 'none';
                                    document.getElementById('type-wrap').style.display = 'block';
                                }
                                e.stopPropagation();
                            }
                        }
                    },
                    eventSources: [{
                            events: schedulesData,
                            color: '#3788d8',

                        },
                        {
                            events: @json($holidays),
                            color: '#e74c3c',
                            textColor: 'white',

                        }
                    ],
                    eventContent: function(arg) {
                        let eventEl = document.createElement('div');
                        if (arg.event.extendedProps.description == 'holiday') {
                            eventEl.innerHTML = `
                        <div>
                            <strong> ${arg.event.title}</strong><br>
                        </div>`;
                        } else {
                            eventEl.innerHTML = `
                            <div>
                                <strong>${arg.event.extendedProps.icon} ${arg.event.title}</strong><br>
                            </div>
                        `;
                        }
                        return {
                            domNodes: [eventEl]
                        };
                    },
                    datesSet: function(info) {

                        $wire.onDateRangeChanges(info.startStr, info.endStr).then(() => {
                            calendar.removeAllEvents();
                            calendar.addEventSource($wire.schedules);
                            calendar.addEventSource($wire.holidays);
                            setZoneInDayCells();
                        });
                        if (info.view.type === 'listDay') {
                            $wire.handleDateClick(info.startStr);

                        }

                    },
                    dateClick: function(info) {

                        const todayCell = document.querySelector('.fc-day-today');
                        if (todayCell) {
                            todayCell.classList.remove('fc-day-today');
                        }
                        // Highlight clicked date
                        document.querySelectorAll('.highlighted-date').forEach(cell => {
                            cell.classList.remove('highlighted-date');
                        });

                        const clickedDateCell = document.querySelector(`[data-date="${info.dateStr}"]`);
                        if (clickedDateCell) {
                            clickedDateCell.classList.add('highlighted-date');
                        }

                        $wire.handleDateClick(info.dateStr);
                    },
                    eventClick: function(info) {

                        if (info.event.extendedProps.description == 'holiday') {
                            return;
                        }
                        $wire.handleEventClick(info.event.id).then(() => {});
                    },
                });

                calendar.render();
                const scheduleButton = document.querySelector('.fc-dropdownButton-button');
                if (scheduleButton) {
                    const icon = document.createElement('i');
                    icon.className = 'far fa-calendar-plus';
                    icon.style.marginRight = '4px';
                    const text = scheduleButton.textContent;
                    scheduleButton.textContent = text;
                    scheduleButton.prepend(icon);
                }

                const warehouseButton = document.querySelector('.fc-warehouseBtn-button');
                if (warehouseButton) {
                    const icon = document.createElement('i');
                    icon.className = 'fas fa-map-marker-alt';
                    icon.style.marginRight = '4px';
                    const text = warehouseButton.textContent;
                    warehouseButton.textContent = text;
                    warehouseButton.prepend(icon);
                }

                Livewire.on('calendar-needs-update', (activeWarehouse) => {
                    calendar.removeAllEvents();
                    calendar.addEventSource($wire.schedules);
                    calendar.addEventSource($wire.holidays);
                    const button = document.querySelector('.fc-warehouseBtn-button');
                    button.textContent = activeWarehouse;
                    setZoneInDayCells()
                });
                Livewire.on('calendar-type-update', (title) => {
                    calendar.removeAllEvents();
                    calendar.addEventSource($wire.schedules);
                    calendar.addEventSource($wire.holidays);
                    const button = document.querySelector('.fc-scheduleBtn-button');
                    button.innerHTML = title;
                    setZoneInDayCells()
                });

                function setZoneInDayCells() {
                    document.querySelectorAll('.zoneinfo-span').forEach(span => {
                        span.remove();
                    });
                    document.querySelectorAll('.fc-daygrid-day').forEach(dayCell => {
                        let truckinfo = $wire.truckInfo
                        let cellDate = dayCell.getAttribute('data-date');
                        let cellDateObj = new Date(cellDate);
                        truckinfo.forEach(truckData => {
                            let truckDateObj = new Date(truckData.schedule_date);
                            if (cellDateObj.toISOString().split('T')[0] === truckDateObj
                                .toISOString().split('T')[0]) {

                                let span = document.createElement('span');
                                span.classList.add('badge', 'bg-light-info', 'zoneinfo-span');
                                span.style.fontSize = 'x-small';
                                span.innerHTML = `
                                    <i class="fas fa-globe"></i> ${truckData.spanText}
                                `;
                                dayCell.insertBefore(span, dayCell.firstChild);
                            }
                        });
                    });
                }
                // Add click outside listener to close dropdown
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.fc-dropdownButton-button') &&
                        !e.target.closest('.fc-warehouseBtn-button')
                    ) {
                        dropdownMenu.style.display = 'none';
                        isDropdownVisible = false;
                    }
                });

                function updateDropdownPosition(button, calendarEl, dropdownMenu) {
                    const buttonRect = button.getBoundingClientRect();
                    const calendarRect = calendarEl.getBoundingClientRect();
                    const dropdownWidth = 160;

                    dropdownMenu.style.top = (buttonRect.bottom + 5) + 'px';
                    const leftPosition = Math.min(
                        buttonRect.left,
                        calendarRect.right - dropdownWidth - 10
                    );
                    dropdownMenu.style.left = leftPosition + 'px';
                }

                window.addEventListener('scroll', function() {
                    if (isDropdownVisible) {
                        let buttonClass = '';
                        if (document.getElementById('schedule-options').style.display === 'block') {
                            buttonClass = '.fc-dropdownButton-button';
                        }

                        if (document.getElementById('warehouse-wrap').style.display === 'block') {
                            buttonClass = '.fc-warehouseBtn-button';
                        }
                        if (document.getElementById('type-wrap').style.display === 'block') {
                            buttonClass = '.fc-scheduleBtn-button';
                        }

                        const button = document.querySelector(buttonClass);
                        if (button) {
                            const buttonRect = button.getBoundingClientRect();
                            const calendarRect = calendarEl.getBoundingClientRect();
                            const dropdownWidth = 160;
                            dropdownMenu.style.top = (buttonRect.bottom + 5) + 'px';
                            const leftPosition = Math.min(
                                buttonRect.left,
                                calendarRect.right - dropdownWidth - 10
                            );
                            dropdownMenu.style.left = leftPosition + 'px';
                        }
                    }
                });

                window.addEventListener('resize', function() {
                    if (isDropdownVisible) {
                        let buttonClass = '';
                        if (document.getElementById('schedule-options').style.display === 'block') {
                            buttonClass = '.fc-dropdownButton-button';
                        }

                        if (document.getElementById('warehouse-wrap').style.display === 'block') {
                            buttonClass = '.fc-warehouseBtn-button';
                        }
                        if (document.getElementById('type-wrap').style.display === 'block') {
                            buttonClass = '.fc-scheduleBtn-button';
                        }
                        const button = document.querySelector(buttonClass);
                        if (button) {
                            const buttonRect = button.getBoundingClientRect();
                            const calendarRect = calendarEl.getBoundingClientRect();
                            const dropdownWidth = 160;
                            dropdownMenu.style.top = (buttonRect.bottom + 5) + 'px';
                            const leftPosition = Math.min(
                                buttonRect.left,
                                calendarRect.right - dropdownWidth - 10
                            );
                            dropdownMenu.style.left = leftPosition + 'px';
                        }
                    }
                });

            };

            initializeCalendar();
        })();
    </script>
@endscript
