<div>
    <x-page :breadcrumbs="$breadcrumbs">
        <x-slot:title>Service Area</x-slot>
        <x-slot:description>{{ $editRecord ? ' Edit Zone details' : 'View Zone Details' }}</x-slot>
        <x-slot:content>
                @include('livewire.service-area.zones.partials.view')
        </x-slot>
    </x-page>
</div>
