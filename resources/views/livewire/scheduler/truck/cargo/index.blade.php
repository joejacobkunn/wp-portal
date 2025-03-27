<div>

    <x-page
        :breadcrumbs="$breadcrumbs"
    >
        <x-slot:title>Cargo Configurator</x-slot>

        <x-slot:description>
            {{$addRecord ? 'Create New Cargo' : 'View Cargo List' }}
        </x-slot>

        <x-slot:content>
            <ul class="nav nav-pills mb-2">
                <li class="nav-item">
                    <a class="nav-link " aria-current="page" href="{{ route('scheduler.truck.index') }}">
                        Truck List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="javascript:;">
                        Cargo Configurator</a>
                </li>
            </ul>
            @if($addRecord)
                @include('livewire.scheduler.truck.cargo.partials.form', ['button_text' => 'Add new'])
            @else

                @include('livewire.scheduler.truck.cargo.partials.listing')
            @endif
        </x-slot>

    </x-page>

</div>

