<x-page :breadcrumbs="$breadcrumbs">
    <x-slot:title>Schedule</x-slot>
    <x-slot:content>
        @if (!empty($this->announcements))
            @foreach ($this->announcements as $item)
                <div class="alert alert-light-warning color-warning">
                    <i class="fas fa-info-circle"></i> {{ $item['message'] }}
                    @can('scheduler.announcement.manage')
                        <button class="btn btn-sm btn-outline-danger float-end"
                            wire:click="cancelAnnouncement({{ $item['id'] }})">
                            <div wire:loading wire:target="cancelAnnouncement({{ $item['id'] }})">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </div>
                            Remove
                        </button>
                    @endcan
                </div>
            @endforeach
        @endif

        <ul class="nav nav-pills mb-2">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="javascript:;"><i class="far fa-calendar-alt"></i>
                    Calendar View</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('schedule.list.index', ['whse' => $this->activeWarehouse->id]) }}"
                    wire:navigate><i class="fas fa-list"></i>
                    List View</a>
            </li>
        </ul>
        <div class="row">
            <div class="col-9">
                <div class="card border-light shadow-sm schedule-tab mb-2">
                    <div class="card-body">
                        <div id="calendar" class="w-100" wire:ignore></div>
                        <div class="legend-section mt-2">
                            @foreach (App\Enums\Scheduler\ScheduleStatusEnum::cases() as $status)
                                <span class="badge bg-{{ $status->colorClass() }}"> {{ $status->label() }}</span>
                            @endforeach

                        </div>
                        <div id="calendar-dropdown-menu" class="dropdown-menu">
                            <div id="schedule-options">
                                @foreach ($scheduleOptions as $key => $value)
                                    <a class="dropdown-item border-bottom @if ($key != 'at_home_maintenance') bg-light-secondary  anchor-disabled @endif"
                                        href="#"
                                        wire:click.prevent="create('{{ $key }}')">{!! $value !!}</a>
                                @endforeach
                            </div>
                            <div id="warehouse-wrap">
                                @foreach ($this->warehouses as $whse)
                                    <a class="dropdown-item border-bottom " href="#"
                                        wire:click.prevent="changeWarehouse('{{ $whse->id }}')">{{ $whse->title }}</a>
                                @endforeach
                            </div>
                            <div id="type-wrap">
                                <a class="dropdown-item border-bottom" href="#"
                                    wire:click.prevent="changeScheduleType('')">All Services</a>
                                @foreach ($scheduleOptions as $key => $value)
                                    <a class="dropdown-item border-bottom  @if ($key != 'at_home_maintenance') bg-light-secondary  anchor-disabled @endif"
                                        href="#"
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
                            <div id="settings-wrap">
                                <a class="dropdown-item border-bottom" href="#"
                                    wire:click.prevent="openAnnouncementModal()">Announcement</a>
                            </div>
                        </div>
                    </div>
                </div>
                <a target="_blank" href="https://forms.clickup.com/8465859/f/82be3-6774/OCD97BGC2IIOUWC8T1">Give
                    Feedback</a>
            </div>
            <div class="col-3" wire:key="schedule-sidebar">
                <h4>Overview for {{ Carbon\Carbon::parse($dateSelected)->toFormattedDayDateString() }}</h4>
                @if (!empty($this->filteredSchedules))
                    @if (collect($this->filteredSchedules)->contains('driver_id', null))
                        <div class="alert alert-light-danger color-warning"><i class="fas fa-exclamation-triangle"></i>
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

                <h5 class="card-title mb-2">Active Trucks and Zones</h5>

                @if (count($this->filteredSchedules) > 0)
                    @foreach ($this->filteredSchedules as $truck)
                        <div class="card border-light shadow-sm schedule-tab">
                            <div class="card-body">
                                <p class="fs-6 fw-bold" style="margin;margin-bottom: 3px;">
                                    <span
                                        class="badge bg-primary rounded-pill float-end">{{ $truck['scheduled_count'] }}
                                        /
                                        {{ $truck['slots'] }}</span>
                                    <i class="fas fa-globe"></i> {{ $truck['zone'] }} =>
                                    <i class="fas fa-truck"></i> {{ $truck['truck_name'] }}
                                </p>

                                <span class="text-muted" style="font-size: smaller;"><i class="far fa-clock"></i>
                                    {{ $truck['start_time'] }}
                                    -
                                    {{ $truck['end_time'] }}
                                </span> =>
                                <span class="text-muted" style="font-size: smaller;">
                                    <i class="fa-solid fa-user"></i>
                                    {!! $truck['driverName'] ?? '<span class="text-warning">Not Assigned</span>' !!}
                                </span>
                                @if (!empty($truck['events']) && $truck['events'][0]['travel_prio_number'])
                                    <div class="mb-1 p-1 bg-light-info text-primary"><i class="fas fa-route"></i>
                                        Showing
                                        optimal route suggested by Google</div>
                                @endif
                                <div class="list-group mt-2 border border-3">
                                    <ul class="list-group">

                                        @forelse ($truck['events'] as $event)
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-start">
                                                <a href="#" class="text-black w-100"
                                                    wire:click.prevent="handleEventClick({{ $event['id'] }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="handleEventClick({{ $event['id'] }})">

                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="text-break">
                                                            <span
                                                                class=" d-inline-block text-wrap badge bg-{{ $event['status_color'] }}">
                                                                #{{ $event['schedule_id'] }} - OE
                                                                #{{ $event['sx_ordernumber'] }}-{{ $event['order_number_suffix'] }}</span>
                                                            <div wire:loading
                                                                wire:target="handleEventClick({{ $event['id'] }})">
                                                                <span class="spinner-border spinner-border-sm"
                                                                    role="status" aria-hidden="true"></span>
                                                            </div>
                                                        </h6>

                                                    </div>
                                                    <p class="mb-1">
                                                        {{ str($event['customer_name'])->title() }} - CustNo
                                                        #{{ $event['sx_customer_number'] }}
                                                    </p>
                                                    @if (isset($event['shipping_info']))
                                                        <small>{{ $event['shipping_info'] }}</small>
                                                    @endif
                                                    @if (!empty($event['latest_comment']))
                                                        <div class="p-1 mt-2 mb-2 bg-light-dark color-warning"> <i
                                                                class="far fa-comment-dots"></i>
                                                            {{ str($event['latest_comment']->comment)->limit(30, ' ...') }}
                                                        </div>
                                                    @endif
                                                    @if ($event['travel_prio_number'])
                                                        <p class="font-small"><span class="badge bg-light-info">
                                                                <i class="far fa-clock"></i> ETA :
                                                                {{ Carbon\Carbon::parse($event['expected_time'])->format('h:i A') }}
                                                                => DURATION : ~1HR</span>
                                                        </p>
                                                    @endif
                                                </a>
                                            </li>
                                            @php
                                                $data = collect($truckReturnInfo)->firstWhere(
                                                    'schedule_id',
                                                    $event['id'],
                                                );
                                            @endphp
                                            @if ($data)
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-start">
                                                    <a href="#" class="text-black w-100 disabled">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <h6 class="">
                                                                {{ $data['warehouse_name'] }} Warehouse
                                                            </h6>
                                                        </div>
                                                        <small>{{ $data['warehouse_address'] }}</small>

                                                        <p class="font-small"><span class="badge bg-light-info">
                                                                <i class="far fa-clock"></i> ETA :
                                                                {{ Carbon\Carbon::parse($data['expected_arrival_time'])->format('h:i A') }}
                                                            </span>
                                                        </p>
                                                    </a>
                                                </li>
                                            @endif

                                        @empty
                                            <li class="list-group-item list-group-item-warning">
                                                <em>No events scheduled</em>
                                            </li>
                                        @endforelse

                                    </ul>
                                </div>

                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-light-warning color-warning"><i class="bi bi-exclamation-triangle"></i> No
                        active trucks and zones
                    </div>
                @endif
            </div>
        </div>
        @if ($showModal)
            <x-modal toggle="showModal" size="xl" :closeEvent="'closeModal'">
                <x-slot name="title">Schedule
                    {{ App\Enums\Scheduler\ScheduleEnum::tryFrom($selectedType)->label() }}</x-slot>

                <livewire:scheduler.schedule.schedule-order lazy wire:key="create" :page="$this->showView" :selectedType="$selectedType"
                    :selectedSchedule="$selectedSchedule" :activeWarehouse="$this->activeWarehouse">
            </x-modal>
        @endif
        @if ($showDriverModal)
            <x-modal toggle="showDriverModal" size="md" :closeEvent="'closeDriverModal'">
                <x-slot name="title"> Assign drivers for
                    {{ Carbon\Carbon::parse($dateSelected)->toFormattedDayDateString() }}</x-slot>
                @include('livewire.scheduler.schedule.partial.drivers_form')
            </x-modal>
        @endif

        @if ($announceModal)
            <x-modal toggle="announceModal" size="md" :closeEvent="'closeAnnouncementModal'">
                <x-slot name="title"> Create Announcement </x-slot>
                <x-forms.textarea label="Message" rows="5" model="announcementForm.message" lazy />

                <x-slot name="footer">
                    <button type="button" wire:click="createAnnouncement" class="btn btn-primary">
                        <div wire:loading wire:target="createAnnouncement">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        Create Announcement
                    </button>
                </x-slot>
            </x-modal>
        @endif

        {{-- search modal --}}
        @if ($showSearchModal)
            <x-modal toggle="showSearchModal" size="md" :closeEvent="'closeSearchModal'">
                <x-slot name="title">
                    <div class="list-group list-group-horizontal-sm mb-1 text-center" role="tablist">
                        <a class="list-group-item list-group-item-action @if ($activeSearchKey == 'schedule') active @endif"
                            id="list-schedules-list" data-bs-toggle="list" href="#list-schedules" role="tab"
                            aria-selected="true">Schedules</a>
                        <a class="list-group-item list-group-item-action @if ($activeSearchKey == 'zone') active @endif"
                            id="list-zones-list" data-bs-toggle="list" href="#list-zones" role="tab"
                            aria-selected="false" tabindex="-1">Zones</a>
                    </div>
                </x-slot>

                <div class="tab-content text-justify">
                    <div class="tab-pane fade  @if ($activeSearchKey == 'schedule') active show @endif "
                        id="list-schedules" role="tabpanel" aria-labelledby="list-schedules-list">
                        <div class="row w-100">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <x-forms.input type="text" label="Search Schedule" model="searchKey"
                                        hint="Search by order number, sro number, schedule id, name, email or phone"
                                        lazy />
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
                                                        <div wire:loading
                                                            wire:target="handleEventClick({{ $event['id'] }})">
                                                            <span class="spinner-border spinner-border-sm"
                                                                role="status" aria-hidden="true"></span>
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
                                                <small><i class="fas fa-clock fa-xs"></i>
                                                    {{ $event['schedule_date'] }}
                                                    , {{ $event['schedule_time'] }}</small>
                                                <p class="mb-1">
                                                    {{ $event['customer'] }} - SX#
                                                    {{ $event['sx_customer_number'] }}
                                                </p>
                                                @if (isset($event['shipping_info']))
                                                    <small>{{ $event['shipping_info']['line'] . ', ' . $event['shipping_info']['city'] . ', ' . $event['shipping_info']['state'] . ', ' . $event['shipping_info']['zip'] }}</small>
                                                @endif
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
                    </div>
                    <div class="tab-pane fade @if ($activeSearchKey == 'zone') active show @endif" id="list-zones"
                        role="tabpanel" aria-labelledby="list-zones-list">
                        <div class="row w-100">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <x-forms.input type="text" label="Search by ZIP Code" model="searchZoneKey"
                                        lazy />
                                </div>
                            </div>
                        </div>
                        <div class="row w-100">
                            <div wire:loading wire:target="searchZoneKey">
                                <div class="col-md-12 mb-2">
                                    <span class="spinner-border spinner-border-sm mr-2" role="status"
                                        aria-hidden="true"></span>
                                    <span>Please wait, looking for Zones...</span>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="list-group " wire:loading.remove wire:target="searchZoneKey">
                                    @if ($searchZoneData !== null)
                                        @if (count($searchZoneData) > 0)
                                            <div class="alert alert-light-success color-warning"><i
                                                    class="fas fa-check-circle"></i>
                                                Showing results for {{ $searchZoneKey }}</div>
                                        @endif
                                        @forelse ($searchZoneData as $zone)
                                            <a href="{{ route('service-area.zones.show', ['zone' => $zone['id']]) }}"
                                                class="list-group-item list-group-item-action" wire:navigate>
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h5 class="mb-1">{{ $zone['name'] }}
                                                    </h5>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="alert alert-light-warning color-warning"><i
                                                    class="bi bi-exclamation-triangle"></i>
                                                Zones not found for {{ $searchZoneKey }}</div>
                                        @endforelse
                                    @endif
                                </div>
                            </div>
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
                        left: 'prev,next today exportBtn searchBtn settingsBtn',
                        center: 'title',
                        right: 'warehouseBtn scheduleBtn zoneBtn dropdownButton'
                    },
                    buttonText: {
                        today: 'Today'
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
                                    document.getElementById('settings-wrap').style.display = 'none';
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
                        settingsBtn: {
                            text: '',
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
                                    document.querySelectorAll('.calendar-button').forEach(element => {
                                        element.classList.add('d-none');
                                    });
                                    document.getElementById('schedule-options').style.display = 'none';
                                    document.getElementById('warehouse-wrap').style.display = 'none';
                                    document.getElementById('type-wrap').style.display = 'none';
                                    document.getElementById('zones-wrap').style.display = 'none';
                                    document.getElementById('settings-wrap').style.display = 'block';
                                }
                                e.stopPropagation();
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
                                    document.getElementById('settings-wrap').style.display = 'none';

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
                                    document.getElementById('settings-wrap').style.display = 'none';

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
                                    document.getElementById('settings-wrap').style.display = 'none';
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
                                        btn.querySelector('i').classList.remove('fa',
                                            'fa-spinner', 'fa-spin')
                                        btn.querySelector('i').classList.add('bi',
                                            'bi-download')
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
                const settingsButton = document.querySelector('.fc-settingsBtn-button');
                if (settingsButton) {
                    const icon = document.createElement('i');
                    icon.className = 'fas fa-cog';
                    settingsButton.appendChild(icon);
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
                Livewire.on('calender-remove-driver-warning', date => {
                    const cell = document.querySelector(`[data-date="${date.date}"]`);

                    if (cell) {
                        cell.classList.remove('bg-light-danger');
                    }
                });

                function setZoneInDayCells() {
                    let today = new Date();
                    document.querySelectorAll('.zoneinfo-span').forEach(span => {
                        span.remove();
                    });

                    document.querySelectorAll('.fc-daygrid-day').forEach(dayCell => {
                        let truckinfo = $wire.truckInfo
                        let cellDate = dayCell.getAttribute('data-date');
                        let cellDateObj = new Date(cellDate);
                        let driverNotAssigned = false;
                        dayCell.classList.remove('bg-light-danger');

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
                            if (cellDateObj >= today) {
                                dayCell.classList.add('bg-light-danger');
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
                        if (document.getElementById('settings-wrap').style.display === 'block') {
                            buttonClass = '.fc-settingsBtn-button';
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
                        if (document.getElementById('settings-wrap').style.display === 'block') {
                            buttonClass = '.fc-settingsBtn-button';
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
