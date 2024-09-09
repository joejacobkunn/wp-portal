
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
                                    <livewire:equipment.floor-model-inventory.table lazy wire:key="{{'floor-model-'.$tableKey}}">
                                @endif

                            </div>
                        </div>

                    </div>
                </div>
                <div class="update-model">
                    <x-modal :toggle="$ShowUpdateModel" size="md" :closeEvent="'closeUpdate'">
                        <x-slot name="title">Update Quantity</x-slot>
                        <form wire:submit.prevent="bulkUpdate()">
                            <div class="row w-100">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <x-forms.select label="Quantity" model="bulkqty" :options="['0', '1', '2', '3']" :selected="$bulkqty"
                                            :defaultOption=false :key="'qty-' . now()" />
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <x-forms.textarea label="Comments" model="comments"
                                         />
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
                <div class="delete-model">
                    <x-modal :toggle="$ShowDeleteModel" size="lg" :closeEvent="'closeDelete'">
                        <x-slot name="title">Delete Records</x-slot>
                        <div class="alert alert-warning" role="alert">
                            <i class="fas fa-exclamation-circle"></i>You are selected {{ count($records) }} for delete ! do you want to continue?

                        </div>
                        <form wire:submit.prevent="bulkDelete()">
                            <div class="row w-100">
                                    @include('livewire.equipment.floor-model-inventory.partials.table',['headers' => $headers, 'records' => $records])
                            </div>
                            <div class="mt-2 float-start">
                                <button type="submit" class="btn btn-primary" >
                                    <div wire:loading wire:target="submit">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    </div>
                                    Confirm Delete
                                </button>
                            </div>
                        </form>
                    </x-modal>
                </div>
        </x-slot>
    </x-page>
</div>
