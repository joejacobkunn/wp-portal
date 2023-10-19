<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Dashboard #{{ $dashboard->id }}</x-slot>

        <x-slot:description>
            @if ($editRecord)
                Edit Dashboard here
            @else
                View Dashboard here
            @endif
        </x-slot>

        <x-slot:content>

            @if ($editRecord)
                @include('livewire.reporting-dashboard.partials.form', ['button_text' => 'Update Report'])
            @else
                @include('livewire.reporting-dashboard.partials.view')
            @endif

        </x-slot>

    </x-page>
</div>
