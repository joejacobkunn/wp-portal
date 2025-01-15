<x-page :breadcrumbs="$breadcrumbs">
    <x-slot:title>Schedule</x-slot>
    <x-slot:description>{{ $showModal ? 'Schedule Shipping' : 'Shipping Schedule list' }}</x-slot>
    <x-slot:content>
        <div class="card border-light shadow-sm schedule-tab">
            <div class="card-body" >
                <div class="row">
                    <div class="col-9" >
                        <div id="calendar" class="w-100" wire:ignore></div>
                        <div id="calendar-dropdown-menu" class="dropdown-menu">
                            <div id="schedule-options">
                                @foreach ($scheduleOptions as $key => $value)
                                    <a class="dropdown-item border-bottom" href="#"
                                        wire:click.prevent="create('{{ $key }}')">{!! $value !!}</a>
                                @endforeach
                            </div>
                            <div id="warehouse-wrap">
                                    @foreach ($warehouses as  $whse)
                                        <a class="dropdown-item border-bottom" href="#"
                                            wire:click.prevent="changeWarehouse('{{ $whse->id }}')">{{ $whse->title }}</a>
                                    @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card" wire:key="order-info-panel-{{$orderInfoStrng}}" >
                            @if (isset($shifts))
                            <div class="card-body" >
                                <h5 class="card-title">Shift Information</h5>

                                <div class="list-group">
                                    <button type="button" class="list-group-item list-group-item-primary">Shifts and
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
                                                <button type="button" class="list-group-item list-group-item-action">
                                                    @if ($shift->type == 'ahm')
                                                        AHM :
                                                    @elseif ($shift->type == 'delivery_pickup')
                                                        P/D :
                                                    @endif
                                                        {{$data['shift']}}
                                                        <span
                                                        class="badge bg-secondary badge-pill badge-round ms-1 float-end">{{$data['slots']}}</span></button>
                                            @endforeach
                                            @endif
                                        @endforeach
                                        @if($status)
                                            <button type="button" class="list-group-item list-group-item-action"> No shifts available this day</button>
                                        @endif

                                    {{-- <button type="button" class="list-group-item list-group-item-action">Type<span
                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">{!! $scheduleOptions[$form->schedule->type] !!}</span></button>
                                    <button type="button" class="list-group-item list-group-item-action">Status<span
                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">{{ $form->schedule->status }}</span></button>
                                    <button type="button" class="list-group-item list-group-item-action">Schedule Date<span
                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">{{ $form->schedule->schedule_date }}</span></button>
                                    <button type="button" class="list-group-item list-group-item-action">Schedule Time<span
                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">{{ $form->schedule->schedule_time }}</span></button> --}}

                                </div>
                            </div>
                            @endif

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
                    left: 'prev,next today',
                    center: 'title',
                    right: 'warehouseBtn dayGridMonth,listMonth dropdownButton'
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
                            }
                            e.stopPropagation();
                        }
                    },
                    warehouseBtn: {
                        text: '{{$activeWarehouse->title}}',
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
                                document.getElementById('schedule-options').style.display = 'none';
                            }
                            e.stopPropagation();
                        }
                    }
                },
                eventSources: [
                    {
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
                    if(arg.event.extendedProps.description == 'holiday') {
                        eventEl.innerHTML = `
                        <div>
                            <strong> ${arg.event.title}</strong><br>
                        </div>
                    `;
                    } else {
                        eventEl.innerHTML = `
                            <div>
                                <strong>${arg.event.extendedProps.icon} ${arg.event.title}</strong><br>
                            </div>
                        `;
                    }
                    return { domNodes: [eventEl] };
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

                    // Call Livewire component function
                    $wire.handleDateClick(info.dateStr);
                },
                eventClick: function(info) {

                    if(info.event.extendedProps.description == 'holiday') {
                        return;
                    }
                    $wire.handleEventClick(info.event.id).then(() => {
                    });
                },
            });

            calendar.render();
            Livewire.on('calendar-needs-update', (activeWarehouse) => {
                calendar.removeAllEvents();
                calendar.addEventSource($wire.schedules);
                calendar.addEventSource($wire.holidays);
                const button = document.querySelector('.fc-warehouseBtn-button');
                button.textContent = activeWarehouse;
            });

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
