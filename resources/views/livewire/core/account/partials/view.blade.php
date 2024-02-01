<div wire:init="init">
    <x-tabs
        :tabs="$tabs"
        tabId="account-view-tabs"
    >
        <x-slot:tab_content_general>
            @if($tabs['account-view-tabs']['active'] == 'general')
                @include('livewire.core.account.partials.general')
            @endif
        </x-slot>

        <x-slot:tab_content_credentials 
            component="core.account.credentials"
            :account="$account"
            wire:key="account_cred">
        </x-slot>

        @can('locations.view')
            <x-slot:tab_content_locations 
                component="core.location.index"
                :account="$account"
                wire:key="location_index">
            </x-slot>
        @endcan

        @can('modules.view')
            <x-slot:tab_content_modules 
                component="core.module.index"
                :account="$account"
                wire:key="module_index">
            </x-slot>
        @endcan

    </x-tabs>
</div>
