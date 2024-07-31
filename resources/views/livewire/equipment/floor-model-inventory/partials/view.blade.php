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

                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Quantity</h3>
                            <p class="small pe-4">{{ $floorModel->qty }}</p>
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
</div>
