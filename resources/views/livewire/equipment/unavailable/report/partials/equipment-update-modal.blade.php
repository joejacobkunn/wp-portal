<x-modal :toggle="$equipment_modal">
    <x-slot name="title">
        <div class="">Update Location</div>
    </x-slot>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-light-primary color-primary">
                Update current location of {{ $editEquipmentName }}
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <x-forms.input label="Current Location" model="location" />
                </div>
            </div>


        </div>
    </div>
    <x-slot name="footer">
        <button wire:click="updateLocation" type="button" class="pre-genkey-div btn btn-success">Update</button>
    </x-slot>
</x-modal>
