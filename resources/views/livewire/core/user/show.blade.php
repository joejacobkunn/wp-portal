<div>
    <x-page
        :breadcrumbs="$breadcrumbs"
    >

        <x-slot:title>Users #{{ $user->id }}</x-slot>

        <x-slot:description>
            @if($editRecord)
                Edit User here
            @else
                View User here
            @endif
        </x-slot>

        <x-slot:content>

            @if($editRecord)
                @include('livewire.core.user.partials.form', ['button_text' => 'Update User'])
            @else
                @include('livewire.core.user.partials.view')
            @endif

        </x-slot>

    </x-page>
</div>
