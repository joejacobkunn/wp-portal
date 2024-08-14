<x-modal :toggle="$hours_modal" :closeEvent="'closeHoursModel'">
    <x-slot name="title">
        <div class="">Update Hours</div>
    </x-slot>

    <div class="row">
        <div class="row">
            <div class="col-md-12 mb-3">
                <x-forms.input label="Hours" model="hours" lazy />
            </div>
        </div>
    </div>

    <x-slot name="footer">
        <button wire:click="updateHours()" type="button" class="btn btn-success">Update Hours</button>
    </x-slot>

</x-modal>
