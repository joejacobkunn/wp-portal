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
    </x-tabs>
</div>