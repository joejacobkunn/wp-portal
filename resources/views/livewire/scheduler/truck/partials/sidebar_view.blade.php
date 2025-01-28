<div class="list-group">
    @forelse ($daySchedules as $schedule)
        <a href="javascript:void(0)" wire:click.prevent="showTruckScheudleForm({{ $schedule['id'] }})" class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1"> <i class="fas fa-globe"></i> {{$schedule['zone']}}</h5>
                <small><span class="badge bg-light-primary badge-pill badge-round ms-1 float-end">
                        {{ $schedule['scheduleCount'].'/'.$schedule['slots'] }}
                    </span>
                </small>
            </div>
            <small><i class="fas fa-clock"></i> {{ $schedule['start_time'] }} - {{ $schedule['end_time'] }}</small>
        </a>
    @empty
    <div class="alert alert-light-warning color-warning"><i class="bi bi-exclamation-triangle"></i>
        No schedules found for this day</div>
    @endforelse

</div>
