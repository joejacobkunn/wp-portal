<div>
    @if($addRecord)
        @include('livewire.scheduler.truck.partials.form', ['button_text' => 'Add Truck'])
    @else

        @include('livewire.scheduler.truck.partials.listing')
    @endif
</div>
