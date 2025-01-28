<div>

    <x-modal toggle="showRotationFormModal">
        <x-slot name="title">
            <div class="">Create New Rotation</div>
        </x-slot>

        <div class="row">
            <div class="col-md-12 mb-3">
                <x-forms.select
                    label="Zone"
                    model="selectedZone"
                    :options="$zones"
                />
            </div>
        </div>
    
        <x-slot name="footer">
            <button wire:click="addRotation()" type="button" class="btn btn-success">
                <div wire:loading wire:target="addRotation">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </div>
                Add New
            </button>
        </x-slot>
    
    </x-modal>
</div>
