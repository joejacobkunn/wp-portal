<div>
    <x-page
        :breadcrumbs="$breadcrumbs"
    >

        <x-slot:title>Email Template #{{ $template->id }}</x-slot>

        <x-slot:description>View Email Template</x-slot>

        <x-slot:content>

            @if($editRecord)
                @include('livewire.scheduler.notification-template.partials.form', ['button_text' => 'Update Template'])
            @else
                @include('livewire.scheduler.notification-template.partials.view')
            @endif

        </x-slot>

    </x-page>
</div>
