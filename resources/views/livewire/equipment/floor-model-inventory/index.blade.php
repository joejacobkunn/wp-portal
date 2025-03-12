<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Floor Model Inventory</x-slot>
        <x-slot:description>{{ $page }}</x-slot>
        <x-slot:content>
            <x-tabs :tabs="$tabs" tabId="inventory-tabs">
                <x-slot:tab_content_inventory component="equipment.floor-model-inventory.inventory.index" wire:key="inventory">
                </x-slot>

                <x-slot:tab_content_notes component="equipment.floor-model-inventory.notes.index" wire:key="notes">
                </x-slot>
            </x-tabs>
        </x-slot>
    </x-page>
</div>
