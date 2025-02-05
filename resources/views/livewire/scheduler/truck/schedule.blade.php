<div>
    <div class="row">
        <div class="col-8 col-md-8 col-xxl-8">
            <div class="card border-light shadow-sm mb-4 schedule-truck">
                <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                    <h3 class="h5 mb-0">
                        Truck Schedule
                        <button type="button" wire:click="importDataModal"
                            class="btn btn-outline-secondary btn-sm float-end">
                            <i class="fa fa-file-import" aria-hidden="true"></i> Import from CSV
                        </button>
                    </h3>
                    <hr class="mb-0" />
                </div>
                <div class="card-body">
                    <div id="calendar" wire:ignore></div>
                </div>
            </div>
        </div>
        <div class="col-4 col-md-4 col-xxl-4">
            <div class="card border-light shadow-sm mb-4">
                <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                    @if (!$showForm)
                        <button wire:click='createSchedule' class="btn btn-sm btn-outline-primary float-end"><i
                            class="fas fa-plus"></i>
                        Add New</button>
                    @endif
                    <h3 class="h5 mb-0">
                        @if ($showForm)
                            {{ empty($this->form->truckSchedule) ? ' Create Schedule for ' : 'Update Schedule ' }}
                        @else
                            Schedules #
                        @endif
                        {{ Carbon\Carbon::parse($this->form->schedule_date)->toFormattedDayDateString() }}
                    </h3>
                    <hr class="mb-0" />
                </div>
                <div class="card-body">
                    @if ($showForm)
                        @include('livewire.scheduler.truck.partials.sidebar_form')
                    @else
                        @include('livewire.scheduler.truck.partials.sidebar_view')
                    @endif
                </div>
            </div>
        </div>
    </div>
    <x-modal toggle="showImportForm" size="lg" :closeEvent="'closeImportForm'">
        <x-slot name="title"> Import Truck Schedules </x-slot>
        <form wire:submit.prevent="importTruckSchedule">
            <div class="mb-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-light-info color-info">
                            <i class="fas fa-info-circle"></i>
                            Please upload schedule file csv here, <a href="#" wire:click.prevent="downloadDemo">
                                click here </a>to download csv file template <i class="fas fa-download"></i>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2" x-data="{ uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true"
                        x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-cancel="uploading = false"
                        x-on:livewire-upload-error="uploading = false">
                        <label>Import Truck Schedule File</label>
                        <input type="file" id="csv-{{ $importIteration }}" class="form-control"
                            wire:model="importForm.csvFile">
                        @error('importForm.csvFile')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div x-show="uploading" class="mt-2">
                            <div class="text-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2 mb-2 csv-table-col">
                        @if (count($importForm->importErrorRows) > 0)
                            <div class="alert alert-light-warning color-warning">
                                <i class="fas fa-info-circle"></i> Found {{ count($importForm->importErrorRows) }}
                                error rows in the uploaded file.
                                Please cross-check the file with the sample template and try again.
                                Alternatively, these rows will be skipped during processing.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <hr>

            <div class="mt-2 float-start">
                <button type="submit" class="btn btn-primary" wire:click="importTruckSchedule">
                    <div wire:loading wire:target="importTruckSchedule">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    Import
                </button>
            </div>
        </form>
    </x-modal>
    <script src="https://code.jquery.com/jquery-3.6.0.js" data-navigate-once></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" data-navigate-once></script>

    @script
        <script>
            (function() {
                let calendarEl = document.getElementById('calendar');
                let calendar = new FullCalendar.Calendar(calendarEl, {
                    themeSystem: 'bootstrap5',
                    initialView: 'grid',
                    views: {
                        grid: {
                        type: 'multiMonth',
                        duration: { months: 2 }
                        }
                    },
                    headerToolbar: {
                        left: 'prev,next',
                        center: 'title',
                        right: 'dayGridMonth,grid'
                    },
                    height: 'auto',
                    datesSet: function(info) {
                        const allDateCells = document.querySelectorAll('.fc-daygrid-day');

                        // Remove spans from each cell
                        allDateCells.forEach(cell => {
                            const existingSpans = cell.querySelectorAll('.zoneinfo-span');
                            existingSpans.forEach(span => span.remove());
                        });
                        $wire.onDateRangeChanges(info.startStr, info.endStr, info.view.title).then(() => {});


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

                        $wire.handleDateClick(info.dateStr).then(() => {});
                    },
                });
                calendar.render();
                Livewire.on('calendar-needs-update', (data) => {
                    if (data['0'].length > 0) {
                        const dateCell = document.querySelector(`[data-date="${data['0'][0].scheduleDate}"]`);
                        if (dateCell) {
                            const existingSpans = dateCell.querySelectorAll('.zoneinfo-span');
                            existingSpans.forEach(span => span.remove());
                        }
                    }
                    data['0'].forEach((schedule, index) => {
                        const bgClass = index % 2 === 0 ? 'bg-light-info' : 'bg-light-success';
                        const scheduleArray = [
                            schedule.scheduleDate,
                            schedule.zone,
                            schedule.start_time + ' - ' + schedule.end_time,
                            'Slots : ' + schedule.slots,
                            bgClass
                            ];
                        setSpanData(scheduleArray);
                    });
                });

                // add all zone data
                Livewire.on('calender-schedules-update', (data) => {

                    data[0].forEach((schedule, index) => {
                        const bgClass = index % 2 === 0 ? 'bg-light-info' : 'bg-light-success';
                        const scheduleArray = [
                            schedule.schedule_date,
                            schedule.zoneName,
                            schedule.timeString,
                            schedule.slotsString,
                            bgClass
                        ];
                        setSpanData(scheduleArray)
                    });

                });

                function setSpanData(data) {
                    const clickedDateCell = document.querySelector(`[data-date="${data[0]}"]`);

                    if (clickedDateCell) {
                        // Create single span with all information
                        const span = document.createElement('span');
                        span.classList.add('badge', data[4], 'zoneinfo-span');

                        // Add additional styling for better fit
                        span.style.cssText = `
                            font-size: 8px;
                            padding: 2px;
                            display: flex;
                            flex-direction: column;
                            gap: 1px;
                            width: 100%;
                            white-space: normal;
                            text-align: left;
                        `;

                        span.innerHTML = `
                            <div><i class="fas fa-globe fa-xs"></i> ${data[1]}</div>
                            <div><i class="fas fa-clock fa-xs"></i> ${data[2]}</div>
                            <div><i class="fas fa-layer-group fa-xs"></i> ${data[3]}</div>
                        `;

                        // Insert span at the top of cell
                        clickedDateCell.insertBefore(span, clickedDateCell.firstChild);
                    }
                }
            })()
        </script>
    @endscript
</div>
