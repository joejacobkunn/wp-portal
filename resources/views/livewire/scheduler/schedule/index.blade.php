<x-page :breadcrumbs="$breadcrumbs">
    <x-slot:title>Schedule</x-slot>
    <x-slot:description>{{ $showModal ? 'Schedule Shipping' : 'Shipping Schedule list' }}</x-slot>
    <x-slot:content>
        <div class="card border-light shadow-sm schedule-tab">
            <div class="card-body" >
                <div class="row">
                    <div class="col-8" wire:ignore>
                        <div id="calendar" class="w-100"></div>
                        <div id="calendar-dropdown-menu" class="dropdown-menu">
                            @foreach ($scheduleOptions as $key => $value)
                                <a class="dropdown-item border-bottom" href="#"
                                    wire:click.prevent="create('{{ $key }}')">{!! $value !!}</a>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card" wire:key="order-info-panel-{{$orderInfoStrng}}" >
                            @if (isset($form->schedule))
                            <div class="card-body" >
                                <h5 class="card-title">Order Information</h5>

                                <div class="list-group">
                                    <button type="button" class="list-group-item list-group-item-primary">{{ $form->schedule->sx_ordernumber.'-'. $form->schedule->order_number_suffix}}</button>
                                    <button type="button" class="list-group-item list-group-item-action">Order Number<span
                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">{{ $form->schedule->sx_ordernumber.'-'. $form->schedule->order_number_suffix}}</span></button>
                                    <button type="button" class="list-group-item list-group-item-action">P/D : 1pm -
                                        6pm<span
                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">2/5</span></button>
                                    <button type="button" class="list-group-item list-group-item-action">S/I : 1pm -
                                        6pm<span
                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">4/8</span></button>

                                </div>
                            </div>
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">Zone Information</h5>
                                <div class="alert alert-light-primary color-primary" role="alert">
                                    <i class="fas fa-map-marker-alt"></i> Allocated Zone : <strong>U1.A</strong>
                                </div>
                                <div class="list-group">
                                    <button type="button" class="list-group-item list-group-item-primary">Shifts and
                                        Slots</button>
                                    <button type="button" class="list-group-item list-group-item-action">AHM : 9am -
                                        1pm<span
                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">1/10</span></button>
                                    <button type="button" class="list-group-item list-group-item-action">P/D : 1pm -
                                        6pm<span
                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">2/5</span></button>
                                    <button type="button" class="list-group-item list-group-item-action">S/I : 1pm -
                                        6pm<span
                                            class="badge bg-secondary badge-pill badge-round ms-1 float-end">4/8</span></button>

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
        let calendarEl = document.getElementById('calendar');
        let dropdownMenu = document.getElementById('calendar-dropdown-menu');
        let isDropdownVisible = false;
        const style = document.createElement('style');
        style.innerHTML = `
            .highlighted-date {
                background-color: #f3ebbc !important; /* Highlight color */
                border: 1px solid #fffadf !important; /* Optional border color */
            }
            .fc-event {
                cursor: pointer; /* Pointer cursor on hover */
            }`;
        document.head.appendChild(style);
        let calendar = new FullCalendar.Calendar(calendarEl, {
            themeSystem: 'bootstrap5',
            initialView: 'dayGridMonth',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth dropdownButton'
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
                        }
                        e.stopPropagation();
                    }
                }
            },
            events: @json($schedules),
            eventClick: function(info) {
            // Remove previous highlights
            document.querySelectorAll('.highlighted-date').forEach(cell => {
                console.log(cell)
                cell.classList.remove('highlighted-date');
            });

            // Find and highlight the corresponding date cell
            const eventDate = info.event.startStr; // ISO string of the event date
            const cell = document.querySelector(`[data-date="${eventDate}"]`);
            if (cell) {
                cell.classList.add('highlighted-date');
            }
                $wire.handleEventClick(info.event.id);
            },
        });

        calendar.render();

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.fc-dropdownButton-button') && isDropdownVisible) {
                dropdownMenu.style.display = 'none';
                isDropdownVisible = false;
            }
        });

        // Handle window scroll and resize
        window.addEventListener('scroll', function() {
            if (isDropdownVisible) {
                const button = document.querySelector('.fc-dropdownButton-button');
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
                const button = document.querySelector('.fc-dropdownButton-button');
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
    </script>
@endscript
