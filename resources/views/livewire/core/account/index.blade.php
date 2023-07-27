<div>

    <x-page
        :breadcrumbs="$breadcrumbs"
    >

        <x-slot:title>Accounts</x-slot>

        <x-slot:description>
            {{ !$addRecord ? 'Manage accounts here' : 'Create a new account here' }}
        </x-slot>

        <x-slot:content>
            @if($addRecord)
                @include('livewire.core.account.partials.form', ['button_text' => 'Add Account'])
            @else
                @include('livewire.core.account.partials.listing')
            @endif

        </x-slot>

    </x-page>

</div>




