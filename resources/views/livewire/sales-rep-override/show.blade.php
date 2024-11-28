<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title> Sales Rep Override</x-slot>
        <x-slot:description>{{ $editRecord ? 'Edit Sales Rep Override Record' :'View Sales Rep Override Record'}}</x-slot>
        <x-slot:content>
            @if($editRecord)
            <div>
                <div class="card border-light shadow-sm mb-4">
                    <div class="card-body">
                         @include('livewire.sales-rep-override.partials.form', ['button_text' => 'Update'])
                    </div>
                </div>
            </div>
            @else
                @include('livewire.sales-rep-override.partials.view')
            @endif
        </x-slot>
    </x-page>
</div>
