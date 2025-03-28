<div class="col-12 col-md-12 col-xxl-12">
    <div class="card border-light shadow-sm mb-4">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
            @can('scheduler.truck.manage')
                <livewire:component.action-button :actionButtons="$actionButtons" :key="'cargo'">
            @endcan
                <h3 class="h5 mb-0"><i class="fas fa-bars me-1"></i> Overview</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <ul class="list-group list-group-flush">
                        <li
                            class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                            <div>
                                <h3 class="h6 mb-1">Product Category</h3>
                                <p class="small pe-4">{{ $cargoConfigurator->productCategory->name }}</p>
                            </div>
                        </li>
                        <li
                            class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                            <div>
                                <h3 class="h6 mb-1">SRO Equipment Category</h3>
                                <p class="small pe-4">{{ $cargoConfigurator->sroEquipment?->name ?? '----' }}</p>
                            </div>
                        </li>
                        <li
                            class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                            <div>
                                <h3 class="h6 mb-1">Height</h3>
                                <p class="small pe-4">{{ $cargoConfigurator->height }} ft</p>
                            </div>
                        </li>
                        <li
                            class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                            <div>
                                <h3 class="h6 mb-1">Width</h3>
                                <p class="small pe-4">{{ $cargoConfigurator->width }} ft</p>
                            </div>
                        </li>
                        <li
                            class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                            <div>
                                <h3 class="h6 mb-1">Length</h3>
                                <p class="small pe-4">{{ $cargoConfigurator->length }} ft</p>
                            </div>
                        </li>
                        <li
                            class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                            <div>
                                <h3 class="h6 mb-1">Weight</h3>
                                <p class="small pe-4">{{ $cargoConfigurator->weight }} ft</p>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>
