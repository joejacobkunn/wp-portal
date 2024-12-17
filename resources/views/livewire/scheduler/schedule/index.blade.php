<div>
    <x-page :breadcrumbs="$breadcrumbs">
        <x-slot:title>Schedule</x-slot>
        <x-slot:description>{{ $showModal ? 'Schedule Shipping' : 'Shipping Schedule list' }}</x-slot>
        <x-slot:content>
            <div class="card border-light shadow-sm warranty-tab">
                <div class="card-body" wire:ignore>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div id='calendar' class="w-100"></div>

                        </div>
                    </div>
                </div>
            </div>

            <x-modal :toggle="$showModal" size="xl" :closeEvent="'closeModal'">
                <x-slot name="title">Schedule Shipping</x-slot>
                <form wire:submit.prevent="submit()">
                            <div class="row w-100">
                                <div class="col-md-12 ">
                                    <div class="form-group">
                                        <x-forms.select label="Schedule" model="form.type" :options="$scheduleOptions"
                                             :hasAssociativeIndex="true" default-option-label="- None -" :selected="$form->type" :key="'schedule-' . now()" />
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <x-forms.input type="number" label="Order Number" model="form.sx_ordernumber" lazy />
                                        @if($form->orderInfo)
                                            <span class="badge bg-light-success">Warehouse : {{$form->orderInfo->whse}}</span>
                                            <span class="badge bg-light-success">Order Date : {{$form->orderInfo->order_date->format(config('app.default_datetime_format'))}}</span>
                                            <span class="badge bg-light-success">Order Status : {{$form->orderInfo->status}}</span>
                                            <span class="badge bg-light-warning">Customer SX Number : {{$form->orderInfo->sx_customer_number}}</span>
                                            <span class="badge bg-light-warning">Customer Name : {{$form->orderInfo->customer->name}}</span>
                                        @endif
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
                                                            <x-forms.checkbox :label="$item['descrip'].'('.$item['shipprod'].')'" :name="'lineItems[]'" :value="$item['shipprod']"
                                                                :model="'form.line_items'" />
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
                                        <x-forms.input type="time" prependIcon="fas fa-clock" label="Schedule Time" model="form.schedule_time" lazy />
                                    </div>
                                </div>
                            </div>
                    <div class="mt-2 float-start">
                        <button type="submit" class="btn btn-primary">
                            <div wire:loading wire:target="submit">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </div>
                            {{ $this->isEdit ? 'Update' : 'Schedule' }}
                        </button>
                    </div>
                </form>
            </x-modal>
        </x-slot>
    </x-page>
    @script
    <script>
        let calendarEl = document.getElementById('calendar');
        let calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 600,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'scheduleButton dayGridMonth,timeGridWeek,timeGridDay'
            },
            customButtons: {
                scheduleButton: {
                    text: 'Schedule',
                    click: function() {
                        $wire.create()
                    }
                }
            },
            events: @json($schedules),
            eventClick: function(info) {
            $wire.handleEventClick(info.event.id);
            },
        });
        calendar.render();
    </script>
    @endscript
</div>
