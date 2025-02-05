<x-page :breadcrumbs="$breadcrumbs">
    <x-slot:title>Schedule</x-slot>
    <x-slot:content>
        <ul class="nav nav-pills mb-2">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="javascript:;"><i class="far fa-calendar-alt"></i>
                    Calendar View</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('schedule.list.index') }}" wire:navigate><i class="fas fa-list"></i> List View</a>
            </li>
        </ul>
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
                @if (!empty($this->filteredSchedules))
                    @if (collect($this->filteredSchedules)->contains('driver_id', null))
                        <div class="alert alert-light-warning color-warning"><i class="fas fa-exclamation-triangle"></i>
                            Drivers not assigned
                            <button class="btn btn-sm btn-outline-success float-end" wire:click="openDriverModal">Assign
                                Driver</button>
                        </div>
                    @else
                        <div class="alert alert-light-success color-success"><i class="fas fa-check-circle"></i>
                            Drivers are assigned
                            <button class="btn btn-sm btn-outline-success float-end" wire:click="openDriverModal">Update
                                Driver</button>
                        </div>
                    @endif
                @endif

                {{-- truck and zone --}}
                <div class="card border-light shadow-sm schedule-tab">
                    <div class="card-body" wire:key="{{$this->activeWarehouse->short.'-'.$this->dateSelected.'trucks'}}">
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
                                                                class="fas fa-truck"></i>
                                                            {{ $truck['truck_name'] }}</span>
                                                    </a></div>
                                                <span class="me-2 text-muted" style="font-size: smaller;"><i
                                                        class="far fa-clock"></i>
                                                    {{ $truck['start_time'] }}
                                                    -
                                                    {{ $truck['end_time'] }}
                                                </span>
                                                @if ($truck['driver_id'] && $truck['driverName'])
                                                    <p class="me-2 text-muted" style="font-size: smaller;"><i
                                                            class="fa-solid fa-user"></i>
                                                        {{ $truck['driverName'] }}
                                                    </p>
                                                @endif
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
                        <h5 class="card-title mb-4">Events
                            @if (!collect($eventsData)->contains('travel_prio_number', null))

                                <span class="badge bg-light-info float-end"> Ordered by distance</span>
                            @endif
                            <div wire:loading wire:target="handleDateClick">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </div>
                        </h5>

                        <div class="list-group"  wire:key="{{$this->activeWarehouse->short.'-'.$this->dateSelected}}">
                            @forelse ($eventsData as $event)
                                <a href="#" class="list-group-item list-group-item-action"
                                    wire:click.prevent="handleEventClick({{ $event['id'] }})"
                                    wire:loading.attr="disabled" wire:target="handleEventClick({{ $event['id'] }})">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5><span class="badge bg-{{ $event['status_color'] }}">Order
                                                #{{ $event['sx_ordernumber'] }}-{{ $event['order_number_suffix'] }}</span>
                                            <div wire:loading wire:target="handleEventClick({{ $event['id'] }})">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
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
                                        <span class="badge bg-light-primary"><i class="fas fa-globe"></i>
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
                                    @if ($event['travel_prio_number'])
                                        <p class="font-small"><span class="badge bg-light-info"> expected delivery time : {{$event['expected_time']}}</span></p>
                                    @endif
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
        @if ($showDriverModal)
            <x-modal toggle="showDriverModal" size="md" :closeEvent="'closeDriverModal'">
                <x-slot name="title"> Assign drivers for
                    {{ Carbon\Carbon::parse($dateSelected)->toFormattedDayDateString() }}</x-slot>
                @include('livewire.scheduler.schedule.partial.drivers_form')
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
                    <div wire:loading wire:target="searchKey">
                        <div class="col-md-12 mb-2">
                            <span class="spinner-border spinner-border-sm mr-2" role="status"
                                aria-hidden="true"></span>
                            <span>Please wait, looking for schedules...</span>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="list-group " wire:loading.remove wire:target="searchKey">
                            @if ($searchData !== null)
                                @if (count($searchData) > 0)
                                    <div class="alert alert-light-success color-warning"><i
                                            class="fas fa-check-circle"></i>
                                        Showing results for {{ $searchKey }}</div>
                                @endif
                                @forelse ($searchData as $event)
                                    <a h ref="#" class="list-group-item list-group-item-action"
                                        wire:click.prevent="handleEventClick({{ $event['id'] }})">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">Order
                                                #{{ $event['sx_ordernumber'] }}-{{ $event['order_number_suffix'] }}
                                                <div wire:loading wire:target="handleEventClick({{ $event['id'] }})">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
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
                                        <small><i class="fas fa-clock fa-xs"></i> {{ $event['schedule_date'] }}
                                            , {{ $event['schedule_time'] }}</small>
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


        @if ($exportModal)
            <x-modal toggle="exportModal">
                <x-slot name="title">Export Schedules</x-slot>
                <div>
                    <x-forms.datepicker label="From Date" model="exportFromDate" />

                    <x-forms.datepicker label="To Date" model="exportToDate" />
                </div>

                <x-slot name="footer">
                    <x-button-submit class="btn-primary" method="exportSchedules" icon="fa-cloud-download-alt"
                        text="Download" />
                </x-slot>
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
                    eventOrder: 'sortIndex',
                    themeSystem: 'bootstrap5',
                    initialView: 'dayGridMonth',
                    height: 'auto',
                    contentHeight: 'auto',
                    headerToolbar: {
                        left: 'prev,next today exportBtn searchBtn',
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
                            text: currentButtonTexts.warehouse,
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
                            text: currentButtonTexts.schedule,
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
                        },
                        exportBtn: {
                            text: '',
                            click: function(e) {

                                let btn = e.target;
                                if (e.target.matches('i')) {
                                    btn = e.target.closest('button')
                                }

                                btn.querySelector('i').classList.remove('bi', 'bi-download')
                                btn.querySelector('i').classList.add('fa', 'fa-spinner', 'fa-spin')
                                btn.setAttribute("disabled", true);

                                $wire.showExportModal().then(() => {
                                    setTimeout(() => {
                                        btn.removeAttribute('disabled');
                                        btn.querySelector('i').classList.remove('fa', 'fa-spinner', 'fa-spin')
                                        btn.querySelector('i').classList.add('bi', 'bi-download')
                                    }, 200)
                                })
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
                            let currentMonth = info.view.currentStart.getMonth();
                            let currentYear = info.view.currentStart.getFullYear();
                            let firstDayOfMonth = new Date(currentYear, currentMonth, 1);

                            const today = new Date();

                            function formatDate(date) {
                                let year = date.getFullYear();
                                let month = String(date.getMonth() + 1).padStart(2, '0');
                                let day = String(date.getDate()).padStart(2, '0');
                                return `${year}-${month}-${day}`;
                            }

                            if (today.getMonth() === currentMonth && today.getFullYear() ===
                                currentYear) {
                                $wire.handleDateClick(formatDate(today));
                            } else {
                                let formattedDate = formatDate(firstDayOfMonth);
                                const clickedDateCell = document.querySelector(
                                    `[data-date="${formattedDate}"]`);

                                if (clickedDateCell) {
                                    clickedDateCell.classList.add('highlighted-date');
                                }
                                $wire.handleDateClick(formatDate(firstDayOfMonth));
                            }
                            const scheduleButton = document.querySelector(
                                '.fc-scheduleBtn-button');
                            const zoneButton = document.querySelector('.fc-zoneBtn-button');
                            const warehouseButton = document.querySelector(
                                '.fc-warehouseBtn-button');
                            scheduleButton.innerHTML = currentButtonTexts.schedule;
                            zoneButton.textContent = currentButtonTexts.zone;
                            warehouseButton.textContent = currentButtonTexts.warehouse;
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
                const exportButton = document.querySelector('.fc-exportBtn-button');
                if (exportButton) {
                    const icon = document.createElement('i');
                    icon.className = 'bi bi-download';
                    exportButton.appendChild(icon);
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
                Livewire.on('add-event-calendar', (eventData) => {
                    if (eventData.newEvent) {
                        calendar.addEvent(eventData.newEvent);
                        calendar.render();
                    }
                });
                Livewire.on('add-event-calendar', (eventData) => {
                    if (eventData.newEvent) {
                        let existingEvent = calendar.getEventById(eventData.newEvent.id);

                        if (!existingEvent) {
                            calendar.addEvent(eventData.newEvent);
                        }
                    }
                });
                Livewire.on('remove-event-calendar', (schedule) => {
                    let event = calendar.getEventById(schedule.eventId);
                    event.remove();
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
                Livewire.on('calender-remove-driver-span', date => {
                    const cell = document.querySelector(`[data-date="${date.date}"]`);

                    if (cell) {
                        const span = cell.querySelector('.driver-assigned-span');
                        if (span) {
                            span.remove();
                        }
                    }
                });

                function setZoneInDayCells() {
                    document.querySelectorAll('.zoneinfo-span').forEach(span => {
                        span.remove();
                    });
                    document.querySelectorAll('.driver-assigned-span').forEach(span => {
                        span.remove();
                    });
                    document.querySelectorAll('.fc-daygrid-day').forEach(dayCell => {
                        let truckinfo = $wire.truckInfo
                        let cellDate = dayCell.getAttribute('data-date');
                        let cellDateObj = new Date(cellDate);
                        let driverNotAssigned = false;

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

                                // for driver span
                                if (truckData.driver_id === null) {
                                    driverNotAssigned = true;
                                }
                            }
                        });
                        if (driverNotAssigned) {

                            let driverAssignedSpan = document.createElement('span');
                            driverAssignedSpan.classList.add('driver-assigned-span', 'float-end');
                            driverAssignedSpan.innerHTML =
                                `<i class="fa-solid fa-triangle-exclamation text-danger"></i>`;
                            dayCell.insertBefore(driverAssignedSpan, dayCell.firstChild);
                            let zoneSpan = dayCell.querySelector('.zoneinfo-span');
                            if (zoneSpan) {
                                zoneSpan.insertAdjacentElement('afterbegin', driverAssignedSpan);
                            }
                        }
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
