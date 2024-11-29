<div class="row">
    <div class="col-8 col-md-8 col-xxl-8">
        <div class="card border-light shadow-sm mb-4">
            <div class="card-header border-gray-300 p-3 mb-4 mb-md-0" :key="'bew' . time()">
                <livewire:component.action-button :actionButtons="$actionButtons" :key="'salesrep' . time()">
                <h3 class="h5 mb-0"><i class="fas fa-bars me-1"></i> Overview</h3>
            </div>

            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Customer Number</h3>
                            <p class="small pe-4">{{ $customerNumber }}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Ship To</h3>
                            <p class="small pe-4">{{  $shipTo }}</p>
                        </div>


                    </li>


                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Product Line</h3>
                            <p class="small pe-4">{{ $prodLine }}</p>
                        </div>
                    </li>

                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Sales Rep</h3>
                            <p class="small pe-4">{{ $salesRep }}</p>
                        </div>
                    </li>

                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                        <div>
                            <h3 class="h6 mb-1">Created At</h3>
                            <p class="small pe-4">{{ $this->salesRepOverride->created_at?->format(config('app.default_datetime_format')) }}</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
        <div class="col-12 col-md-4 col-xxl-4">
            <x-tabs :tabs="$tabs" tabId="sales-rep-comment-tabs" activeTabIndex="active">
                <x-slot:tab_content_comments component="x-comments" :entity="$salesRepOverride" :key="'comments' . time()">
                </x-slot>

                <x-slot:tab_content_activity component="x-activity-log" :entity="$salesRepOverride" recordType="sales-rep-override" :key="'activity-' . time()">
                </x-slot>
            </x-tabs>
        </div>
</div>
