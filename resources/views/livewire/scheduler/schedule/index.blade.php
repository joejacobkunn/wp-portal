<x-page :breadcrumbs="$breadcrumbs">
    <x-slot:title>Schedule</x-slot>
    <x-slot:description>{{ $showModal ? 'Schedule Shipping' : 'Shipping Schedule list' }}</x-slot>
    <x-slot:content>
        <div class="card border-light shadow-sm schedule-tab">
            <div class="card-body" wire:ignore>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div id="calendar" class="w-100"></div>
                        <div id="calendar-dropdown-menu" class="dropdown-menu">
                            @foreach ($scheduleOptions as $key => $value)
                                <a class="dropdown-item border-bottom" href="#"
                                    wire:click.prevent="create('{{ $key }}')">{{ $value }}</a>
                            @endforeach
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

        let calendar = new FullCalendar.Calendar(calendarEl, {
            themeSystem: 'bootstrap5',
            initialView: 'dayGridMonth',
            height: 600,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay dropdownButton'
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
