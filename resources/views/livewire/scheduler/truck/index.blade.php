<div>

    <x-page
        :breadcrumbs="$breadcrumbs"
    >

        <x-slot:title>Trucks</x-slot>

        <x-slot:description>
            {{ !$addRecord ? 'Manage trucks here' : 'Create a new truck here' }}
        </x-slot>

        <x-slot:content>
            @if($addRecord)
                @include('livewire.scheduler.truck.partials.form', ['button_text' => 'Add Truck'])
            @else
                @include('livewire.scheduler.truck.partials.listing')
            @endif
        </x-slot>

    </x-page>

</div>
