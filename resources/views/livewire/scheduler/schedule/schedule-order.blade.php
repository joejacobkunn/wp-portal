<div>
    @if (!$page)
        @include('livewire.scheduler.schedule.partial.form')
    @else
        @if ($form->type == \App\Enums\Scheduler\ScheduleEnum::at_home_maintenance->value)
            @include('livewire.scheduler.schedule.partial.ahm_view')
        @else
            @include('livewire.scheduler.schedule.partial.pd_view')
        @endif
    @endif
</div>
