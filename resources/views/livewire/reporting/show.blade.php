<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Report #{{ $report->id }}</x-slot>

        <x-slot:description>
            @if ($editRecord)
                Edit Report here
            @else
                View Report here
            @endif
        </x-slot>

        <x-slot:content>

            @if ($editRecord)
                @include('livewire.reporting.partials.form', ['button_text' => 'Update Report'])
            @else
                @include('livewire.reporting.partials.view')
            @endif

        </x-slot>

    </x-page>
</div>
