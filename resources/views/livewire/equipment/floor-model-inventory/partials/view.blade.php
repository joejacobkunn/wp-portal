<div class="row">
    <div class="col-8 col-md-8 col-xxl-8">
        <div class="card border-light shadow-sm mb-4">
            <div class="card-header border-gray-300 p-3 mb-4 mb-md-0" :key="'bew' . time()">
                @can('equipment.floor-model-inventory.manage')
                    <livewire:component.action-button :actionButtons="$actionButtons" :key="'comments' . time()">
                @endcan
                <h3 class="h5 mb-0"><i class="fas fa-bars me-1"></i> Overview</h3>
            </div>

            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Product</h3>
                            <p class="small pe-4">{{ $floorModel->product }}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Warehouse</h3>
                            <p class="small pe-4">{{ $floorModel->warehouse->title }}</p>
                        </div>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Quantity</h3>
                            <p class="small mb-0">{{ $floorModel->qty }}</p>
                        </div>
                        @can('equipment.floor-model-inventory.manage')
                            <button class="btn btn-sm btn-outline-primary" wire:click="showModel">Update</button>
                        @endcan
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Operator</h3>
                            <p class="small pe-4">{{ $floorModel->operator?->name ?? '---' }}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                        <div>
                            <h3 class="h6 mb-1">Created At</h3>
                            <p class="small pe-4">{{ $floorModel->created_at?->format(config('app.default_datetime_format')) }}</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-xxl-4">
        <x-tabs
                :tabs="$tabs"
                tabId="floor-model-comment-tabs"
                activeTabIndex="active"
                >
                <x-slot:tab_content_comments
                    component="x-comments"
                    :entity="$floorModel"
                    :key="'comments' . time()">
                </x-slot>

                <x-slot:tab_content_activity
                    component="x-activity-log"
                    :entity="$floorModel"
                    recordType="floor-model"
                    :key="'activity-'. time()">
                </x-slot>
            </x-tabs>
    </div>

    @include('livewire.equipment.floor-model-inventory.partials.product-card',[
        'product' => $floorModel->product ?? '--',
        'brand'=> $floorModel->products->brand->name ?? '--',
        'description' => $floorModel->products->description ?? '--'
        ])
    <div class="qty-update-model">
        <x-modal :toggle="$qtyModal" size="md" :closeEvent="'closeQtyUpdate'">
            <x-slot name="title">Update Quantity</x-slot>
            <form wire:submit.prevent="submit()">
                    <div class="row w-100">
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <x-forms.select label="Quantity"
                                    model="qty"
                                    :options="['0','1','2','3']"
                                    :selected="$qty"
                                    :defaultOption=false
                                    :key="'qty-' . now()" />
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 float-start">
                        <button type="submit" class="btn btn-primary">
                            <div wire:loading wire:target="submit">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </div>
                            Update
                        </button>
                    </div>
            </form>
        </x-modal>
    </div>
</div>
