<div>
    <div class="card border-light shadow-sm warranty-tab">
        @if (!$addRecord)
            <div class="card-header border-gray-300 p-3 mb-4">
                @can('equipment.floor-model-inventory.manage')
                    <button wire:click="create" class="btn btn-primary btn-lg btn-fab"><i class="fas fa-plus"></i></button>
                @endcan
            </div>
        @endif
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    @if ($addRecord)
                        @include('livewire.equipment.floor-model-inventory.inventory.partials.form', [
                            'button_text' => 'Add Inventory',
                        ])
                    @else
                        <livewire:equipment.floor-model-inventory.inventory.table lazy
                            wire:key="{{ 'floor-model-' . $tableKey }}">
                    @endif

                    <div class="badges mt-2">
                        <span class="badge bg-light-success"><i class="fas fa-check-circle"></i> Active Inventory</span>
                        <span class="badge bg-light-secondary"><i class="far fa-eye-slash"></i> Non-Active
                            Inventory</span>
                        <span class="badge bg-light-warning"><i class="far fa-pause-circle"></i> Inventory On
                            Hold</span>
                    </div>
                </div>

            </div>
        </div>
        <div class="update-model">
            <x-modal toggle="ShowUpdateModel" size="lg" :closeEvent="'closeUpdate'">
                <x-slot name="title">Update Quantity</x-slot>
                <form wire:submit.prevent="bulkUpdate()">
                    <div class="row w-100">
                        <div class="col-md-12">
                            <div class="form-group">
                                <x-forms.select label="Quantity" model="bulkqty" :options="['Please Select', '0', '1', '2', '3']" :selected="$bulkqty"
                                    :defaultOption=false :key="'qty-' . now()" />
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <x-forms.textarea label="Comments" model="comments" hint="Optional" />
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 float-start mb-3">
                        <button type="submit" class="btn btn-primary">
                            <div wire:loading wire:target="submit">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </div>
                            Update
                        </button>
                    </div>

                    @if (is_numeric($bulkqty))
                        <div class="row w-100 border border-2">
                            <h5 class="mt-3 mb-2">Update Preview</h5>
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-exclamation-circle"></i> You have selected {{ count($records) }}
                                record(s). Please review and click update.

                            </div>
                            @include('livewire.equipment.floor-model-inventory.inventory.partials.table', [
                                'headers' => $headers,
                                'records' => $records,
                                'updatedqty' => $bulkqty,
                            ])
                        </div>
                    @endif

                </form>
            </x-modal>
        </div>
        <div class="delete-model">
            <x-modal toggle="ShowDeleteModel" size="lg" :closeEvent="'closeDelete'">
                <x-slot name="title">Delete Records</x-slot>
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-circle"></i> You have selected {{ count($records) }} record(s) for
                    deletion ! Do
                    you want to continue?

                </div>
                <form wire:submit.prevent="bulkDelete()">
                    <div class="row w-100">
                        @include('livewire.equipment.floor-model-inventory.inventory.partials.table', [
                            'headers' => $headers,
                            'records' => $records,
                        ])
                    </div>
                    <div class="mt-2 float-start">
                        <button type="submit" class="btn btn-danger">
                            <div wire:loading wire:target="bulkDelete">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </div>
                            <i class="fas fa-trash-alt"></i> Confirm Delete
                        </button>
                    </div>
                </form>
            </x-modal>
        </div>

        <div class="hold-model">
            <x-modal toggle="ShowHoldModel" size="lg" :closeEvent="'closeHold'">
                <x-slot name="title">Hold Records</x-slot>
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-circle"></i> You have selected {{ count($records) }} record(s) to put
                    on Hold ! Do
                    you want to continue?

                </div>
                <form wire:submit.prevent="bulkHold()">
                    <div class="row w-100">
                        @include('livewire.equipment.floor-model-inventory.inventory.partials.table', [
                            'headers' => $headers,
                            'records' => $records,
                        ])
                    </div>
                    <div class="mt-2 float-start">
                        <button type="submit" class="btn btn-warning">
                            <div wire:loading wire:target="bulkHold">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </div>
                            <i class="fas fa-pause-circle"></i> Put on Hold
                        </button>
                    </div>
                </form>
            </x-modal>
        </div>

    </div>
