<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Template : {{ $template->name }}</x-slot>

        <x-slot:description>View Template</x-slot>

        <x-slot:content>

            @if ($editRecord)
                @include('livewire.scheduler.notification-template.partials.form', [
                    'button_text' => 'Update Template',
                ])
            @else
                @include('livewire.scheduler.notification-template.partials.view')
            @endif

        </x-slot>

    </x-page>
</div>
