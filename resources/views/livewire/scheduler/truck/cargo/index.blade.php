<div>
    @if($addRecord)
        @include('livewire.scheduler.truck.cargo.partials.form', ['button_text' => 'Add new'])
    @else

        @include('livewire.scheduler.truck.cargo.partials.listing')
    @endif
</div>
