<div wire:init="init">
    <x-tabs
        :tabs="$tabs"
        tabId="account-view-tabs"
        activeTabIndex="tab"
    >
        <x-slot:tab_content_general>
            @include('livewire.core.account.partials.general')
        </x-slot>

        <x-slot:tab_content_credentials>
            @if($tabLoaded)
            <livewire:core.account.credentials
                :account="$account"
                wire:key="account_cred"
                        />
            @endif
        </x-slot>

        @can('locations.view')
            <x-slot:tab_content_locations>
                @if($tabLoaded)
                    <livewire:core.location.index
                        :account="$account"
                        wire:key="location_index"
                    />
                @endif
            </x-slot>
        @endcan

        @can('modules.view')
            <x-slot:tab_content_modules>
                @if($tabLoaded)
                    <livewire:core.module.index
                        :account="$account"
                        wire:key="module_index"
                    />
                @endif
            </x-slot>
        @endcan


    </x-tabs>
</div>
