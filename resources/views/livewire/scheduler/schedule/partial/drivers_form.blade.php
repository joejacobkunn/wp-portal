    <ul class="list-group">
        @foreach ($filteredSchedules as $key=>$schedule)
            <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                    <div class="fw-bold mb-2"><a
                            href="{{ route('service-area.zones.show', ['zone' => $schedule['zone_id']]) }}">
                            <span class="badge bg-light-primary"><i
                                    class="fas fa-globe"></i>
                                {{ $schedule['zone'] }}</span></a>
                        => <a
                            href="{{ route('scheduler.truck.show', ['truck' => $schedule['truck_id']]) }}">
                            <span class="badge bg-light-secondary"><i
                                    class="fas fa-truck"></i>{{ $schedule['truck_name'] }}</span>
                        </a></div>

                    <x-forms.select label="Driver" :model="'filteredSchedules.'.$key.'.driver_id'" :options="$drivers"
                     :label-index="'name'" :value-index="'id'" default-option-label="- None -" :selected="$filteredSchedules[$key]['driver_id']"
                    :key="'schedule-' . now()" />
                </div>
                <span class="me-2 fst-italic text-muted" style="font-size: smaller;"><i
                        class="far fa-clock"></i>
                    {{ $schedule['start_time'] }}
                    -
                    {{ $schedule['end_time'] }}
                </span>
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

