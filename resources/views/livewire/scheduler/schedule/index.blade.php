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
                                @foreach ($this->warehouses as $whse)
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
                            <div id="zones-wrap">
                                <a class="dropdown-item border-bottom" href="#"
                                    wire:click.prevent="changeZone('')">All Zones</a>
                                @foreach ($this->activeWarehouse->zones->sortBy('name') as $zone)
                                    <a class="dropdown-item border-bottom" href="#"
                                        wire:click.prevent="changeZone('{{ $zone->id }}')">{{ $zone->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-3">
                <h4>Overview for {{ Carbon\Carbon::parse($dateSelected)->toFormattedDayDateString() }}</h4>

                {{-- truck and zone --}}
                <div class="card border-light shadow-sm schedule-tab">
                    <div class="card-body">
                        <h5 class="card-title">Active Trucks and Zones</h5>
                        @if (count($this->filteredSchedules) > 0)
                            <div class="list-group">
                                <ul class="list-group">
                                    @foreach ($this->filteredSchedules as $truck)
                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold"><a
                                                        href="{{ route('service-area.zones.show', ['zone' => $truck['zone_id']]) }}">
                                                        <span class="badge bg-light-primary"><i
                                                                class="fas fa-globe"></i>
                                                            {{ $truck['zone'] }}</span></a>
                                                    => <a
                                                        href="{{ route('scheduler.truck.show', ['truck' => $truck['truck_id']]) }}">
                                                        <span class="badge bg-light-secondary"><i
                                                                class="fas fa-truck"></i>{{ $truck['truck_name'] }}</span>
                                                    </a></div>
                                                <span class="me-2 fst-italic text-muted" style="font-size: smaller;"><i
                                                        class="far fa-clock"></i>
                                                    {{ $truck['start_time'] }}
                                                    -
                                                    {{ $truck['end_time'] }}
                                                </span>
                                            </div>
                                            <span class="badge bg-primary rounded-pill">{{ $truck['scheduled_count'] }}
                                                /
                                                {{ $truck['slots'] }}</span>
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
                {{-- truck and zone end  --}}
                <div class="card border-light shadow-sm schedule-tab">
                    <div class="card-body">
                        <h5 class="card-title">Events
                            <div wire:loading wire:target="handleDateClick">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </div>
                        </h5>

                        <div class="list-group">
                            @forelse ($eventsData as $event)
                                <a href="#" class="list-group-item list-group-item-action"
                                    wire:click.prevent="handleEventClick({{ $event['id'] }})"
                                    wire:loading.attr="disabled"
                                    wire:target="handleEventClick({{ $event['id'] }})">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5><span class="badge bg-secondary">Order
                                                #{{ $event['sx_ordernumber'] }}-{{ $event['order_number_suffix'] }}</span>
                                                <div wire:loading wire:target="handleEventClick({{ $event['id'] }})">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                </div>
                                        </h5>
                                        <small>
                                            <span class="badge bg-light-primary badge-pill badge-round ms-1 float-end">
                                                @if ($event['type'] == 'at_home_maintenance')
                                                    AHM
                                                @elseif($event['type'] == 'pickup' || $event['type'] == 'delivery')
                                                    P / D
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </small>
                                    </div>
                                    <p>
                                        <span class="badge bg-light-primary"><i
                                                class="fas fa-globe"></i>
                                            {{ $event['zone'] }}</span>
                                        =>
                                        <span class="badge bg-light-secondary"><i
                                                class="fas fa-truck"></i>{{ $event['truckName'] }}</span>
                                   </p>
                                    <p class="mb-1">
                                        {{ $event['customer_name'] }} - CustNo
                                        #{{ $event['sx_customer_number'] }}
                                    </p>
                                    <small>{{ $event['shipping_info']['line'] . ', ' . $event['shipping_info']['city'] . ', ' . $event['shipping_info']['state'] . ', ' . $event['shipping_info']['zip'] }}</small>
                                </a>
                            @empty
                                <div class="alert alert-light-warning color-warning"><i
                                        class="bi bi-exclamation-triangle"></i>
                                    No events for today</div>
                            @endforelse

                        </div>

                    </div>
                </div>
                {{-- events end --}}
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
        {{-- search modal --}}
        @if ($showSearchModal)
            <x-modal toggle="showSearchModal" size="md" :closeEvent="'closeSearchModal'">
                <x-slot name="title">Search for Event</x-slot>
                <div class="row w-100">
                    <div class="col-md-12">
                        <div class="form-group">
                            <x-forms.input type="text" label="Search Schedule" model="searchKey"
                                hint="Search by order number, name, email or phone" lazy />
                        </div>
                    </div>
                </div>
                <div class="row w-100">
                    <div wire:loading wire:target="searchKey" >
                        <div class="col-md-12 mb-2">
                            <span class="spinner-border spinner-border-sm mr-2" role="status"
                                aria-hidden="true"></span>
                            <span>Please wait, looking for schedules...</span>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="list-group " wire:loading.remove wire:target="searchKey">
                            @if ($searchData !== null)
                                @if (count($searchData)>0)
                                    <div class="alert alert-light-success color-warning"><i class="fas fa-check-circle"></i>
                                        Showing results for {{ $searchKey }}</div>
                                @endif
                                @forelse ($searchData as $event)
                                    <a h ref="#" class="list-group-item list-group-item-action"
                                        wire:click.prevent="handleEventClick({{ $event['id'] }})">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">Order
                                                #{{ $event['sx_ordernumber'] }}-{{ $event['order_number_suffix'] }}
                                                <div wire:loading wire:target="handleEventClick({{ $event['id'] }})">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                </div>
                                            </h5>
                                            <small>
                                                <span
                                                    class="badge bg-light-primary badge-pill badge-round ms-1 float-end">
                                                    @if ($event['type'] == 'at_home_maintenance')
                                                        AHM
                                                    @elseif($event['type'] == 'pickup' || $event['type'] == 'delivery')
                                                        P / D
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                            </small>
                                        </div>
                                        <small><i class="fas fa-clock fa-xs"></i> {{$event['schedule_date']}}
                                            , {{$event['schedule_time']}}</small>
                                        <p class="mb-1">
                                            {{ $event['customer'] }} - SX#
                                            {{ $event['sx_customer_number'] }}
                                        </p>
                                        <small>{{ $event['shipping_info']['line'] . ', ' . $event['shipping_info']['city'] . ', ' . $event['shipping_info']['state'] . ', ' . $event['shipping_info']['zip'] }}</small>
                                    </a>
                                @empty
                                    <div class="alert alert-light-warning color-warning"><i
                                            class="bi bi-exclamation-triangle"></i>
                                        Schedules not found for {{ $searchKey }}</div>
                                @endforelse
                            @endif
                        </div>
                    </div>
                </div>
            </x-modal>
        @endif
        {{-- search modal end --}}
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
                let currentButtonTexts = {
                    schedule: 'All Services',
                    warehouse: '{{ $this->activeWarehouse->title }}',
                    zone: 'All Zones',
                };

                // Create loader function
                const createLoader = (className) => {
                    const loader = document.createElement('div');
                    loader.className = `loader-overlay ${className}`;
                    loader.innerHTML = `
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    `;
                    return loader;
                };

                // Create and add loader
                const loader = createLoader('schedule-tab-viewport-loader');
                document.body.appendChild(loader);
                loader.style.display = 'none';

                let calendar = new FullCalendar.Calendar(calendarEl, {
                    themeSystem: 'bootstrap5',
                    initialView: 'dayGridMonth',
                    height: 'auto',
                    contentHeight: 'auto',
                    headerToolbar: {
                        left: 'prev,next today searchBtn',
                        center: 'title',
                        right: 'warehouseBtn scheduleBtn zoneBtn dropdownButton'
                    },
                    titleFormat: {
                        month: 'short',
                        year: 'numeric'
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
                                    document.getElementById('zones-wrap').style.display = 'none';
                                }
                                e.stopPropagation();
                            }
                        },
                        searchBtn: {
                            text: '',
                            click: function(e) {
                                $wire.showSearchModalForm();
                            }
                        },
                        warehouseBtn: {
                            text:  currentButtonTexts.warehouse,
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
                                    document.getElementById('zones-wrap').style.display = 'none';

                                }
                                e.stopPropagation();
                            }
                        },
                        scheduleBtn: {
                            text:  currentButtonTexts.schedule,
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
                                    document.getElementById('zones-wrap').style.display = 'none';

                                }
                                e.stopPropagation();
                            }
                        },
                        zoneBtn: {
                            text: currentButtonTexts.zone,
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
                                    document.getElementById('type-wrap').style.display = 'none';
                                    document.getElementById('zones-wrap').style.display = 'block';
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
                            const scheduleButton = document.querySelector('.fc-scheduleBtn-button');
                            const zoneButton = document.querySelector('.fc-zoneBtn-button');
                            const warehouseButton = document.querySelector('.fc-warehouseBtn-button');
                            scheduleButton.innerHTML=currentButtonTexts.schedule;
                            zoneButton.textContent=currentButtonTexts.zone;
                            warehouseButton.textContent=currentButtonTexts.warehouse;
                            if (warehouseButton) {
                                const icon = document.createElement('i');
                                icon.className = 'fas fa-map-marker-alt';
                                icon.style.marginRight = '4px';
                                const text = warehouseButton.textContent;
                                warehouseButton.textContent = text;
                                warehouseButton.prepend(icon);
                            }
                        });
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
                        loader.style.display = 'flex';

                        if (info.event.extendedProps.description == 'holiday') {
                            return;
                        }
                        $wire.handleEventClick(info.event.id).then(() => {});
                    },
                });

                calendar.render();
                const searchButton = document.querySelector('.fc-searchBtn-button');
                if (searchButton) {
                    const icon = document.createElement('i');
                    icon.className = 'fas fa-search';
                    searchButton.appendChild(icon);
                }
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
                Livewire.on('modalContentLoaded', () => {
                    loader.style.display = 'none';
                });

                Livewire.on('calendar-needs-update', (activeWarehouse) => {
                    calendar.removeAllEvents();
                    calendar.addEventSource($wire.schedules);
                    calendar.addEventSource($wire.holidays);
                    const button = document.querySelector('.fc-warehouseBtn-button');
                    button.textContent = activeWarehouse;
                    currentButtonTexts.warehouse = activeWarehouse;
                    if (button) {
                        const icon = document.createElement('i');
                        icon.className = 'fas fa-map-marker-alt';
                        icon.style.marginRight = '4px';
                        const text = button.textContent;
                        button.textContent = text;
                        button.prepend(icon);
                    }

                    setZoneInDayCells()
                });
                Livewire.on('calendar-type-update', (title) => {
                    calendar.removeAllEvents();
                    calendar.addEventSource($wire.schedules);
                    calendar.addEventSource($wire.holidays);
                    const button = document.querySelector('.fc-scheduleBtn-button');
                    button.innerHTML = title;
                    currentButtonTexts.schedule = title;
                    setZoneInDayCells()
                });
                Livewire.on('calendar-zone-update', (title) => {
                    calendar.removeAllEvents();
                    calendar.addEventSource($wire.schedules);
                    calendar.addEventSource($wire.holidays);
                    const button = document.querySelector('.fc-zoneBtn-button');
                    button.innerHTML = '';
                    button.innerHTML = title;
                    currentButtonTexts.zone = title;

                    setZoneInDayCells()
                });
                Livewire.on('jump-to-date', ({
                    activeDay
                }) => {
                    calendar.gotoDate(activeDay);

                    // Clear any existing highlights
                    document.querySelectorAll('.highlighted-date').forEach(cell => {
                        cell.classList.remove('highlighted-date');
                    });

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

                        if (document.getElementById('zones-wrap').style.display === 'block') {
                            buttonClass = '.fc-zoneBtn-button';
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
                        if (document.getElementById('zones-wrap').style.display === 'block') {
                            buttonClass = '.fc-zoneBtn-button';
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
