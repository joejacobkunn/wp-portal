<div>

    <x-page
        :breadcrumbs="$breadcrumbs"
    >

        <x-slot:title>Users</x-slot>

        <x-slot:description>
            {{ 'Manage users here' }}
        </x-slot>

        <x-slot:content>
            @include('livewire.core.user.partials.listing')
        </x-slot>

    </x-page>

</div>
