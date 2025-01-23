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
                    <h3 class="h5 mb-0">
                        {{ empty($this->form->truckSchedule) ? ' Create Schedule for ' : 'Update Schedule ' }}
                        {{ Carbon\Carbon::parse($this->form->schedule_date)->toFormattedDayDateString() }}
                    </h3>
                    <hr class="mb-0" />
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ !empty($truckSchedule->id) ? 'save()' : 'submit()' }}">


                        {{-- Zones --}}
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                {{-- <x-forms.select label="Zone" :options="$zones" :model="'form.zoneValue'"
                                    :label-index="'name'" :value-index="'id'"  default-option-label="- Select Zone -" /> --}}
                                <div class="form-group">
                                    <select class="form-select" wire:model="form.zone">
                                        <option value=""> Select Zone</option>
                                        @foreach ($zones as $zone)
                                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.zone')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        {{-- start time --}}
                        <div class="row">
                            <div class="col-md-12 mb-2">

                                <label> Start Time</label>
                                <div class="input-group">
                                    <!-- Time Input -->
                                    <input type="time" wire:model="form.start_time" class="form-control"
                                        placeholder="Select Time" aria-label="Time input">

                                    <!-- AM/PM Select Dropdown -->
                                    <div class="input-group-append">
                                        <select class="form-select" id="timePeriod" wire:model="form.timePeriod"
                                            name="timePeriod">
                                            <option value="AM">AM</option>
                                            <option value="PM">PM</option>
                                        </select>
                                    </div>

                                </div>
                                @error('form.start_time')
                                    <span class="text-danger">{{ $message }}</span>
                                    <br>
                                @enderror
                                @error('form.timePeriod')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- end time --}}
                            <div class="col-md-12 mb-2">
                                <label> End Time</label>
                                <div class="input-group">
                                    <!-- Time Input -->
                                    <input type="time" wire:model="form.end_time" class="form-control">
                                    <!-- AM/PM Select Dropdown -->
                                    <div class="input-group-append">
                                        <select class="form-select" wire:model="form.timePeriodEnd">
                                            <option value="AM">AM</option>
                                            <option value="PM">PM</option>
                                        </select>
                                    </div>
                                </div>
                                @error('form.end_time')
                                    <span class="text-danger">{{ $message }}</span>
                                    <br>
                                @enderror
                                @error('form.timePeriodEnd')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <div class="form-group">
                                    <x-forms.input type="number" label="Slots" model="form.slots" lazy />
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button class="btn btn-primary" type="submit">
                                <div wire:loading wire:target="submit">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                </div>
                                {{ !empty($this->form->truckSchedule) ? 'Update' : 'Schedule' }}
                            </button>
                        </div>
                    </form>
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
                    initialView: 'dayGridMonth',
                    height: 'auto',
                    datesSet: function(info) {
                        $wire.onDateRangeChanges(info.startStr, info.endStr).then(() => {});

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

                    setSpanData(data);
                });

                // add all zone data
                Livewire.on('calender-schedules-update', (data) => {
                    data[0].forEach((schedule, index) => {
                        const scheduleArray = [
                            schedule.schedule_date,
                            schedule.zoneName,
                            schedule.timeString,
                            schedule.slotsString
                        ];
                        setSpanData(scheduleArray)
                    });

                });

                function setSpanData(data) {
                    const clickedDateCell = document.querySelector(`[data-date="${data[0]}"]`);

                    if (clickedDateCell) {
                        // Remove all existing zoneinfo spans
                        const existingSpans = clickedDateCell.querySelectorAll('.zoneinfo-span');
                        existingSpans.forEach(span => span.remove());

                        // Create first span for top of cell
                        const span1 = document.createElement('span');
                        span1.classList.add('badge', 'bg-light-info', 'zoneinfo-span');
                        span1.style.fontSize = 'x-small';
                        span1.innerHTML = `
                            <i class="fas fa-globe"></i> ${data[1]}
                        `;

                        // Insert first span at the top of cell
                        clickedDateCell.insertBefore(span1, clickedDateCell.firstChild);

                        // Create other spans for bottom of cell
                        const span2 = document.createElement('span');
                        span2.classList.add('badge', 'bg-light-warning', 'zoneinfo-span');
                        span2.style.fontSize = 'x-small';
                        span2.innerHTML = `
                            <i class="fas fa-clock"></i> ${data[2]}
                        `;

                        const span3 = document.createElement('span');
                        span3.classList.add('badge', 'bg-light-success', 'zoneinfo-span');
                        span3.style.fontSize = 'x-small';
                        span3.innerHTML = `
                            <i class="fas fa-layer-group"></i> ${data[3]}
                        `;

                        // append other spans on botton div
                        const bottomDiv = clickedDateCell.querySelector('.fc-daygrid-day-bottom');
                        if (bottomDiv) {
                            bottomDiv.appendChild(span2);
                            bottomDiv.appendChild(span3);
                        }
                    }
                }
            })()
        </script>
    @endscript
</div>
