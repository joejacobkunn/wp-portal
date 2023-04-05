<div>
    <x-page
        :breadcrumbs="$breadcrumbs"
    >

        <x-slot:title>Vehicle Info</x-slot>

        <x-slot:description>View vehicle details</x-slot>

        <x-slot:content>

            @if($editRecord)
                @include('livewire.vehicle.vehicle.partials.form', ['button_text' => 'Update Vehicle'])
            @else
                @include('livewire.vehicle.vehicle.partials.view')
            @endif

            @include('livewire.vehicle.vehicle.partials.retire-modal')

        </x-slot>

    </x-page>
</div>
