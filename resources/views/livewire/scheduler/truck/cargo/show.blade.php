
    <x-page
        :breadcrumbs="$breadcrumbs"
    >
        <x-slot:title>Cargo #{{ $cargoConfigurator->id }}</x-slot>

        <x-slot:description>
            View Cargo details
        </x-slot>

        <x-slot:content>
            @if($editRecord)
                @include('livewire.scheduler.truck.cargo.partials.form', ['button_text' => 'Update'])
            @else

                @include('livewire.scheduler.truck.cargo.partials.view')
            @endif

        </x-slot>

    </x-page>

</div>
