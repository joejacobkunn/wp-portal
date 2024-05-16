<x-modal :toggle="$location_modal">
    <x-slot name="title">
        <div class="">Update Location</div>
    </x-slot>

    <div class="row">
        <div class="row">
            <div class="col-md-12 mb-3">
                <x-forms.input label="Current Location" model="current_location" lazy />
            </div>
        </div>
    </div>

    <x-slot name="footer">
        <button wire:click="updateLocation()" type="button" class="btn btn-success">Update Location</button>
    </x-slot>

</x-modal>
