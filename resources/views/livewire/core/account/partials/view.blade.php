<div wire:init="init">
    <x-tabs
        :tabs="$tabs"
        tabId="account-view-tabs"
        activeTabIndex="activeTab"
    >
        <x-slot:tab_content_general>
            @include('livewire.core.account.partials.general')
        </x-slot>

        <x-slot:tab_content_credentials>
            <livewire:core.account.credentials
                :account="$account"
                wire:key="account_cred"
            />
        </x-slot>

        @can('locations.view')
            <x-slot:tab_content_locations>
                <livewire:core.location.index
                    :account="$account"
                    wire:key="location_index"
                />
            </x-slot>
        @endcan

        @can('modules.view')
            <x-slot:tab_content_modules>
                <livewire:core.module.index
                    :account="$account"
                    wire:key="module_index"
                />
            </x-slot>
        @endcan

    </x-tabs>
</div>
