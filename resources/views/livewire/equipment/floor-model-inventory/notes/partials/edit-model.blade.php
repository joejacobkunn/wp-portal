<div class="update-model">
    <x-modal toggle="showUpdateModel" size="lg" :closeEvent="'closeUpdate'">
        <x-slot name="title">Update Note</x-slot>
        @include('livewire.equipment.floor-model-inventory.notes.partials.form', [
            'button_text' => 'Update Note',
            'submit_action' => 'update',
            'cancel_action' => 'closeUpdate'
        ])
    </x-modal>
</div>