<div>
    <x-page :breadcrumbs="$breadcrumbs">
        <x-slot:title>Floor Model Inventory</x-slot>
        <x-slot:description>Manage Floor Model Inventory</x-slot>
        <x-slot:content>
            @if($editRecord)
                <div class="card card-body shadow-sm mb-4">
                    @include('livewire.equipment.floor-model-inventory.partials.form', ['button_text' => 'Update'])
                </div>
            @else
                @include('livewire.equipment.floor-model-inventory.partials.view')
            @endif
        </x-slot>
    </x-page>
</div>
