<div>
    @if (!$page)
        @include('livewire.scheduler.schedule.partial.form')
    @else
        @include('livewire.scheduler.schedule.partial.view')
    @endif
</div>
