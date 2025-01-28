<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title> Sales Rep Override</x-slot>
        <x-slot:description>{{ $editRecord ? 'Edit Sales Rep Override Record' :'View Sales Rep Override Record'}}</x-slot>
        <x-slot:content>
                @include('livewire.sales-rep-override.partials.view')
                @if ($editRecord)
                <div class="update-model">
                    <x-modal toggle="editRecord" size="md" :closeEvent="'closeUpdate'">
                        <x-slot name="title">Update Data</x-slot>
                            @include('livewire.sales-rep-override.partials.form', ['button_text' => 'Update'])
                    </x-modal>
                </div>
                @endif
        </x-slot>
    </x-page>
</div>
