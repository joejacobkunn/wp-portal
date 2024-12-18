<x-page :breadcrumbs="$breadcrumbs">
    <x-slot:title>Schedule</x-slot>
    <x-slot:description>{{ $showModal ? 'Schedule Shipping' : 'Shipping Schedule list' }}</x-slot>
    <x-slot:content>
        <div class="card border-light shadow-sm schedule-tab">
            <div class="card-body" wire:ignore>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div id="calendar" class="w-100"></div>
                        <div id="calendar-dropdown-menu" class="dropdown-menu" >
                            @foreach ($scheduleOptions as $key => $value)

                            <a class="dropdown-item border-bottom" href="#" wire:click.prevent="create('{{$key}}')">{{$value}}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-modal :toggle="$showModal" size="xl" :closeEvent="'closeModal'">
            <x-slot name="title">Schedule Shipping</x-slot>
            <div class="row">
                <div class="col-8 ">
                    <div class="border rounded p-4">
                        <form wire:submit.prevent="submit()">
                            <div class="row w-100">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <x-forms.select label="Schedule" model="form.type" :options="$scheduleOptions"
                                            :hasAssociativeIndex="true" default-option-label="- None -" :selected="$form->type"
                                            :key="'schedule-' . now()" />
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <x-forms.input type="number" label="Order Number" model="form.sx_ordernumber" lazy />
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <x-forms.input type="number" label="Order Number Suffix" model="form.suffix" lazy />
                                    </div>
                                </div>
                                @if ($form->orderInfo?->line_items)
                                <div class="col-md-12 mb-3">
                                    <div class="accordion">
                                        <div class="accordion-item mb-2">
                                            <div class="row columnRow p-3">
                                                <h6 class="accordion-header mb-3">
                                                    Line Items
                                                </h6>
                                                @foreach ($form->orderInfo->line_items['line_items'] as $item)
                                                <div class="col-12 mb-3">
                                                    <x-forms.checkbox :label="$item['descrip'].'('.$item['shipprod'].')'"
                                                        :name="'lineItems[]'" :value="$item['shipprod']" :model="'form.line_items'" />
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-forms.datepicker type="date" label="Schedule Date" model="form.schedule_date" lazy />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-forms.input type="time" prependIcon="fas fa-clock" label="Schedule Time"
                                            model="form.schedule_time" lazy />
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                    <button class="btn btn-primary" type="submit">
                                        <div wire:loading wire:target="submit">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        </div>
                                        {{ $this->isEdit ? 'Update' : 'Schedule' }}
                                    </button>

                            </div>
                        </form>
                    </div>

                </div>
                <div class="col-4">
                    <div class="card border-light shadow-sm mb-4">
                        <div class="card-header bg-primary  border-bottom p-3">
                            <h4 class="h5 mb-0 text-white">Order Overview
                                {{$form->orderInfo ? '#'.$form->orderInfo->order_number.'-'.$form->orderInfo->order_number_suffix:'' }}</h4>
                        </div>
                        <div class="card-body warehouse-nav">
                            <ul class="list-group list-group-flush">
                                @if($form->orderInfo)
                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                        <div>
                                            <h3 class="h6 mb-1">Warehouse</h3>
                                            <p class="small pe-4">{{ $form->orderInfo->whse }}</p>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                        <div>
                                            <h3 class="h6 mb-1">Order Date :</h3>
                                            <p class="small pe-4">{{$form->orderInfo->order_date->format(config('app.default_datetime_format'))}}</p>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                        <div>
                                            <h3 class="h6 mb-1">Order Status :</h3>
                                            <p class="small pe-4">{{$form->orderInfo->status}}</p>
                                        </div>
                                    </li>
                                @else
                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                        <div>
                                            <h3 class="h6 mb-1">Order not selected </h3>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="card border-light shadow-sm mb-4">
                        <div class="card-header bg-primary  border-bottom p-3">
                            <h4 class="h5 mb-0 text-white">Customer Info</h4>
                        </div>
                        <div class="card-body warehouse-nav">
                            <ul class="list-group list-group-flush">
                                @if($form->orderInfo)

                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                        <div>
                                            <h3 class="h6 mb-1">Customer Name</h3>
                                            <p class="small pe-4">{{ $form->orderInfo->customer?->name }}</p>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                        <div>
                                            <h3 class="h6 mb-1">Customer SX Number</h3>
                                            <p class="small pe-4">{{$form->orderInfo->sx_customer_number}}</p>
                                        </div>
                                    </li>
                                    @else
                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                        <div>
                                            <h3 class="h6 mb-1">Order not selected </h3>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </x-modal>
    </x-slot>
</x-page>

@script
<script>
    let calendarEl = document.getElementById('calendar');
    let dropdownMenu = document.getElementById('calendar-dropdown-menu');
    let isDropdownVisible = false;

    let calendar = new FullCalendar.Calendar(calendarEl, {
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
