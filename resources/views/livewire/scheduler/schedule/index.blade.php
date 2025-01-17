<x-page :breadcrumbs="$breadcrumbs">
    <x-slot:title>Schedule</x-slot>
    <x-slot:content>
        <div class="card border-light shadow-sm schedule-tab">
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
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
                    <div class="col-3">
                        <div class="card" wire:key="order-info-panel-{{ $orderInfoStrng }}">
                            @if (isset($shifts))
                                <div class="card-body">
                                    <h5 class="card-title">Shift Information</h5>

                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-primary">Shifts
                                            and
                                            Slots</button>
                                        @php
                                            $selectedDay = strtolower($dateSelected->format('l'));
                                            $month = strtolower($dateSelected->format('F'));
                                            $status = true;
                                        @endphp
                                        @foreach ($shifts as $shift)
                                            @if (isset($shift->shift[$month]) && isset($shift->shift[$month][$selectedDay]))
                                                @php $status = false; @endphp
                                                @foreach ($shift->shift[$month][$selectedDay] as $key => $data)
                                                    <button type="button"
                                                        class="list-group-item list-group-item-action">
                                                        @if ($shift->type == 'ahm')
                                                            AHM :
                                                        @elseif ($shift->type == 'delivery_pickup')
                                                            P/D :
                                                        @endif
                                                        {{ $data['shift'] }}
                                                        <span
                                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">{{ $data['slots'] }}</span>
                                                    </button>
                                                @endforeach
                                            @endif
                                        @endforeach
                                        @if ($status)
                                            <button type="button" class="list-group-item list-group-item-action"> No
                                                shifts available this day</button>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action">
                                            Number of Events <span
                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">{{$eventCount}}</span>
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action">
                                            Number of Shifts <span
                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">{{$this->shiftRotation->count()}}</span>
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">Truck information</h5>
                                <div class="list-group">
                                    @if (count($this->shiftRotation)>0)
                                        <button type="button" class="list-group-item list-group-item-action d-flex p-0">
                                            <span class="flex-grow-1 text-center border-end py-2">Truck Name</span>
                                            <span class="flex-grow-1 text-center border-end py-2">VIN Number</span>
                                            <span class="flex-grow-1 text-center py-2">Driver</span>
                                        </button>
                                        @foreach ($this->shiftRotation as $shift)
                                            <button type="button" class="list-group-item list-group-item-action d-flex p-0">
                                                <span class="flex-grow-1 text-center border-end py-2">{{$shift->truck->truck_name}}</span>
                                                <span class="flex-grow-1 text-center border-end py-2">{{$shift->truck->vin_number}}</span>
                                                <span class="flex-grow-1 text-center py-2">{{$shift->truck->driverName->name}}</span>
                                            </button>
                                        @endforeach
                                    @else
                                    <button type="button" class="list-group-item list-group-item-action">
                                        Trucks not available this day
                                    </button>
                                    @endif

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if ($showModal || $isEdit)
            <x-modal :toggle="$showModal" size="xl" :closeEvent="'closeModal'">
                <x-slot name="title">Schedule
                    {{ Illuminate\Support\Str::of($form->type)->replace('_', ' ')->title() }}</x-slot>
                @if (!$this->showView)
                    @include('livewire.scheduler.schedule.partial.form')
                @else
                    @include('livewire.scheduler.schedule.partial.view')
                @endif
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
                            let truckDateObj = new Date(truckData.scheduled_date);
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
