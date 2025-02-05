    <ul class="list-group">
        @foreach ($filteredSchedules as $key => $schedule)
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                    <x-forms.select :label="'Driver for ' . $schedule['truck_name']" :model="'filteredSchedules.' . $key . '.driver_id'" :options="$drivers" :label-index="'name_title'"
                        :value-index="'id'" default-option-label="- None -" :selected="$filteredSchedules[$key]['driver_id']" :key="'schedule-' . now()"
                        :hint="'Zone ' .
                            $schedule['zone'] .
                            ' during ' .
                            $schedule['start_time'] .
                            ' to ' .
                            $schedule['end_time']" />
                </div>
            </li>
        @endforeach
    </ul>
    @error('asignDrivers')
        <span class="text-danger">{{ $message }}</span>
    @enderror
    <x-slot name="footer">
        <button type="button" wire:click="asignDrivers" class="btn btn-primary">
            <div wire:loading wire:target="asignDrivers">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </div>
            Assign Drivers
        </button>
    </x-slot>
