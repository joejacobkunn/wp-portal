<div>
    
    <x-page
        :breadcrumbs="$breadcrumbs"
    >
        <x-slot:title>Truck #{{ $truck->id }}</x-slot>

        <x-slot:description>
            View truck details
        </x-slot>
        
        <x-slot:content>
            @include('livewire.scheduler.truck.partials.view')

            <livewire:scheduler.truck.rotation
                :truck="$truck"
            />
        </x-slot>
        
    </x-page>
    
</div>

