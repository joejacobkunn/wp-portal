<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title> Manage Brand Configuration</x-slot>
        <x-slot:description>Warranty Registration Details</x-slot>
        <x-slot:content>
            @if($editRecord)
                @include('livewire.equipment.warranty.brand-configurator.partials.form', ['button_text' => 'Update'])
            @else
                @include('livewire.equipment.warranty.brand-configurator.partials.view')
            @endif
        </x-slot>
    </x-page>
</div>
