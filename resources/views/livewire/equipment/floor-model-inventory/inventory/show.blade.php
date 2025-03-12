<div>
    <x-page :breadcrumbs="$breadcrumbs">
        <x-slot:title>Floor Model Inventory</x-slot>
        <x-slot:description>{{ $page }}</x-slot>
        <x-slot:content>
                @include('livewire.equipment.floor-model-inventory.inventory.partials.view')
        </x-slot>
    </x-page>
</div>
