<div>

    <x-page
        :breadcrumbs="$breadcrumbs"
    >

        <x-slot:title>Users</x-slot>

        <x-slot:description>
            {{ !$addRecord ? 'Manage users here' : 'Create a new user here' }}
        </x-slot>

        <x-slot:content>
            @if($addRecord)
                @include('livewire.core.user.partials.form', ['button_text' => 'Add User'])
            @else
                @include('livewire.core.user.partials.listing')
            @endif
        </x-slot>

    </x-page>

</div>
