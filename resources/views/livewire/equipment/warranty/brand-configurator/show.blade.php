<div>
    <x-page
        :breadcrumbs="$breadcrumbs"
    >

        <x-slot:title>Warranty Management</x-slot>

        <x-slot:content>
            @include('livewire.equipment.warranty.brand-configurator.partials.tabs')
            @if($editRecord)
                @include('livewire.equipment.warranty.brand-configurator.partials.form', ['button_text' => 'Update'])
            @else
                @include('livewire.equipment.warranty.brand-configurator.partials.view')
            @endif

        </x-slot>

    </x-page>
</div>
