<div>

    <x-page
        :breadcrumbs="$breadcrumbs"
    >
        <x-slot:title>Truck #{{ $truck->id }}</x-slot>

        <x-slot:description>
            View truck details
        </x-slot>

        <x-slot:content>
            @if ($editRecord)
                @include('livewire.scheduler.truck.partials.form', ['button_text' => 'Update Truck'])
            @else
                @include('livewire.scheduler.truck.partials.view')

                <livewire:scheduler.truck.truck.schedule
                    :truck="$truck"
                />
            @endif

        </x-slot>

    </x-page>

</div>

