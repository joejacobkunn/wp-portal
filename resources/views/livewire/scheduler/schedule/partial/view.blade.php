<div class="row">
    <div class="col-8 col-md-8 col-xxl-8">
        <div class="card border rounded shadow-sm mb-4">
            <div class="card-header border-gray-300 p-3 mb-4 mb-md-0" :key="'bew'.time()">
                @can('equipment.floor-model-inventory.manage')
                    <livewire:component.action-button :actionButtons="$actionButtons" :key="'comments' . time()">
                    @endcan
                    <h3 class="h5 mb-0"><i class="fas fa-bars me-1"></i> Overview</h3>
            </div>

            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Type</h3>
                            <p class="small pe-4">{{ $scheduleOptions[$form->schedule->type] }}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Scheduled Date</h3>
                            <p class="small pe-4">{{ $form->schedule->schedule_date }}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Scheduled Time</h3>
                            <p class="small pe-4">{{ $form->schedule->schedule_time }}</p>
                        </div>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Status</h3>
                            <p class="small mb-0">{{ $form->schedule->status }}</p>
                        </div>
                        {{-- @can('equipment.floor-model-inventory.manage')
                            <button class="btn btn-sm btn-outline-primary" wire:click="showModel">Update</button>
                        @endcan --}}
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Created By</h3>
                            <p class="small pe-4">{{ $form->schedule->user->name ?? '---' }}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                        <div>
                            <h3 class="h6 mb-1">Last Updated At</h3>
                            <p class="small pe-4">
                                {{ $form->schedule->updated_at?->format(config('app.default_datetime_format')) }}</p>
                        </div>
                    </li>

                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                        <div>
                            <h3 class="h6 mb-1">Created At</h3>
                            <p class="small pe-4">
                                {{ $form->schedule->created_at?->format(config('app.default_datetime_format')) }}</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 col-xxl-4">
        <div class="card border rounded shadow-sm mb-4">
            <div class="card-header border-gray-300 p-3 mb-4 mb-md-0" :key="'bew'.time()">

                    <h3 class="h5 mb-0"><i class="fas fa-bars me-1"></i> Order Overview</h3>
            </div>

            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Order Number</h3>
                            <p class="small pe-4">{{ $form->schedule->sx_ordernumber.'-'. $form->schedule->order_number_suffix}}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Warehouse</h3>
                            <p class="small pe-4">{{ $form->orderInfo->whse }}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Order Date</h3>
                            <p class="small pe-4">{{ $form->orderInfo->order_date?->format(config('app.default_datetime_format')) }}</p>
                        </div>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Order Status</h3>
                            <p class="small mb-0">{{ $form->orderInfo->status }}</p>
                        </div>
                        {{-- @can('equipment.floor-model-inventory.manage')
                            <button class="btn btn-sm btn-outline-primary" wire:click="showModel">Update</button>
                        @endcan --}}
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">CUstomer Name</h3>
                            <p class="small pe-4">{{ $form->orderInfo->customer->name ?? '---' }}</p>
                        </div>
                    </li>

                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                        <div>
                            <h3 class="h6 mb-1">Customer SX number</h3>
                            <p class="small pe-4">
                                {{ $form->orderInfo->sx_customer_number }}</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
