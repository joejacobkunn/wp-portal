
<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Floor Model Inventory</x-slot>
        <x-slot:description>{{ $page }}</x-slot>
        <x-slot:content>
                <div class="card border-light shadow-sm warranty-tab">
                    @if(!$addRecord)
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
                                    @include('livewire.equipment.floor-model-inventory.partials.form', [
                                        'button_text' => 'Add Inventory',
                                    ])
                                @else
                                    <livewire:equipment.floor-model-inventory.table lazy>
                                @endif

                            </div>
                        </div>

                    </div>
                </div>
                <div class="update-model">
                    <x-modal :toggle="$ShowUpdateModel" size="md" :closeEvent="'closeUpdate'">
                        <x-slot name="title">Update Quantity</x-slot>
                        <form wire:submit.prevent="submit()">
                            <div class="row w-100">
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <x-forms.select label="Quantity" model="qty" :options="['0', '1', '2', '3']" :selected="$qty"
                                            :defaultOption=false :key="'qty-' . now()" listener="qty:changed" />
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 float-start">
                                <button type="submit" class="btn btn-primary" >
                                    <div wire:loading wire:target="submit">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    </div>
                                    Update
                                </button>
                            </div>
                        </form>
                    </x-modal>
                </div>
        </x-slot>
    </x-page>
</div>
