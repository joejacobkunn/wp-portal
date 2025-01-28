<x-modal toggle="retire_modal">
        <x-slot name="title">
            <div class="">Retire Vehicle</div>
        </x-slot>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-light-warning color-warning">
                      You are retiring {{$vehicle->name}}. You can revert back later if you choose to.
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <button wire:click="retire()" type="button" class="btn btn-danger">Retire</button>
        </x-slot>

    </x-modal>
