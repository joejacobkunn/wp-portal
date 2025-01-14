<x-page :breadcrumbs="$breadcrumbs">
    <x-slot:title>Schedule</x-slot>
    <x-slot:description>{{ $showModal ? 'Schedule Shipping' : 'Shipping Schedule list' }}</x-slot>
    <x-slot:content>
        <div class="card border-light shadow-sm schedule-tab">
            <div class="card-body" >
                <div class="row">
                    <div class="{{isset($form->schedule) ? 'col-9' : 'col-12'}}" >
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
                    <div class="{{isset($form->schedule) ? 'col-3' : ''}}">
                        <div class="card" wire:key="order-info-panel-{{$orderInfoStrng}}" >
                            @if (isset($form->schedule))
                            <div class="card-body" >
                                <h5 class="card-title">Schedule Information</h5>

                                <div class="list-group">
                                    <button type="button" class="list-group-item list-group-item-action">Order Number<span
                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">{{ $form->schedule->sx_ordernumber.'-'. $form->schedule->order_number_suffix}}</span></button>
                                    <button type="button" class="list-group-item list-group-item-action">Type<span
                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">{!! $scheduleOptions[$form->schedule->type] !!}</span></button>
                                    <button type="button" class="list-group-item list-group-item-action">Status<span
                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">{{ $form->schedule->status }}</span></button>
                                    <button type="button" class="list-group-item list-group-item-action">Schedule Date<span
                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">{{ $form->schedule->schedule_date }}</span></button>
                                    <button type="button" class="list-group-item list-group-item-action">Schedule Time<span
                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">{{ $form->schedule->schedule_time }}</span></button>

                                </div>
                            </div>
                            <div class="card-body">
                                {{-- <div class="alert alert-light-primary color-primary" role="alert">
                                    <i class="fas fa-map-marker-alt"></i> Allocated Zone : <strong>U1.A</strong>
                                </div> --}}
                                <div class="list-group">
                                    <button type="button" class="list-group-item list-group-item-primary">Customer Info</button>
                                    <button type="button" class="list-group-item list-group-item-action"><strong>Name : </strong>{{$form->schedule->order->customer?->name}}</button>
                                    <button type="button" class="list-group-item list-group-item-action"><strong>Email : </strong>{{$form->schedule->order->customer?->email}}</button>
                                    <button type="button" class="list-group-item list-group-item-action"><strong>Phone : </strong>{{$form->schedule->order->customer?->phone}}</button>
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
        // Declare initializeCalendar in the global scope
        window.initializeCalendar = function() {
            let calendarEl = document.getElementById('calendar');
            let dropdownMenu = document.getElementById('calendar-dropdown-menu');
            let isDropdownVisible = false;
            let schedulesData = @json($schedules);
            // const style = document.createElement('style');
            // style.innerHTML = `
            //     .highlighted-date {
            //         background-color: #f3ebbc !important;
            //         border: 1px solid #fffadf !important;
            //     }
            //     .fc-event {
            //         cursor: pointer;
            //     }`;
            // document.head.appendChild(style);

            let calendar = new FullCalendar.Calendar(calendarEl, {
                themeSystem: 'bootstrap5',
                initialView: 'dayGridMonth',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listMonth dropdownButton,warehouseBtn'
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
                eventClick: function(info) {

                    if(info.event.extendedProps.description == 'holiday') {
                        return;
                    }
                    // Remove previous highlights
                    document.querySelectorAll('.highlighted-date').forEach(cell => {
                        cell.classList.remove('highlighted-date');
                    });

                    const eventDate = info.event.startStr;
                    const cell = document.querySelector(`[data-date="${eventDate}"]`);
                    if (cell) {
                        cell.classList.add('highlighted-date');
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
