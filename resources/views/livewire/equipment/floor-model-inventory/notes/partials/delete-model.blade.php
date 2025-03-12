<div class="delete-model">
    <x-modal toggle="showDeleteModel" size="lg" :closeEvent="'cancelDelete'">
        <x-slot name="title">Delete Note</x-slot>
        <div class="p-2 mb-2">
            <i class="fas fa-exclamation-circle"></i> Are you sure you want to delete ?
            <div class="pt-2">
                <q>{{ $editInventoryNote?->note }}</q>
            </div>
        </div>
        <form wire:submit.prevent="destroy">
            <div class="mt-2 float-start">
                <button type="submit" class="btn btn-danger">
                    <div wire:loading wire:target="submit">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    <i class="fas fa-trash-alt"></i> Confirm Delete
                </button>
            </div>
        </form>
    </x-modal>
</div>