<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Warranty Registration</x-slot>
        <x-slot:description>Upload Registration Details</x-slot>
        <x-slot:content>
            <div>
                <x-tabs :tabs="$tabs" tabId="warranty-tabs">
                    <x-slot:tab_content_warrantyImport component="equipment.warranty.warranty-import.index" wire:key="brand_import">
                    </x-slot>

                    @can('equipment.warranty.view')
                        <x-slot:tab_content_brand component="equipment.warranty.brand-configurator.index" wire:key="brand_index">
                        </x-slot>
                    @endcan
                </x-tabs>
            </div>
        </x-slot>
    </x-page>
</div>
