<div class="row">
    <div class="col-8 col-md-8 col-xxl-8">
        <div class="card border-light shadow-sm mb-4">
            <div class="card-header border-gray-300 p-3 mb-4 mb-md-0" :key="'bew'.time()">
                <livewire:component.action-button :actionButtons="$actionButtons" :key="'comments' . time()">
                    <h3 class="h5 mb-0"><i class="fas fa-bars me-1"></i> Overview</h3>
            </div>
            <div class="card-body">
                <livewire:component.alert :level="$alertConfig['level']" :message="$alertConfig['message']" :messageIcon="$alertConfig['icon']" :hasAction="'true'"
                    :actionButtonClass="$alertConfig['btnClass']" :actionButtonName="$alertConfig['btnText']" :actionButtonAction="'updateStatus'"
                    wire:key="{{ 'status_alert_' . $zipcode->id . '_' . $alertConfig['level'] }}" />

                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">ZIP Code</h3>
                            <p class="small pe-4">{{ $zipcode->zip_code }}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Zone</h3>
                            <p class="small pe-4">{{ $zipcode->getZone->name }}</p>
                        </div>
                    </li>

                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Delivery Rate</h3>
                            <p class="small pe-4">${{ number_format($zipcode->delivery_rate, 2) }}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Pickup Rate</h3>
                            <p class="small pe-4">${{ number_format($zipcode->pickup_rate, 2) }}</p>
                        </div>
                    </li>

                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div class="w-100">
                            <h3 class="text-sm font-semibold mb-3">Services</h3>
                            <ul>
                                @foreach ($zipcode->service as $item)
                                    <li>{{ $form->serviceArray[$item] }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                        <div>
                            <h3 class="h6 mb-1">Last Updated At</h3>
                            <p class="small pe-4">
                                {{ $zipcode->updated_at?->format(config('app.default_datetime_format')) }}</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>


    </div>
    <div class="col-12 col-md-4 col-xxl-4">
        <x-tabs :tabs="$tabs" tabId="zipcode-comment-tabs" activeTabIndex="active">
            <x-slot:tab_content_comments component="x-comments" :entity="$zipcode" :key="'comments' . time()">
            </x-slot>

            <x-slot:tab_content_activity component="x-activity-log" :entity="$zipcode" recordType="zipcode" :key="'activity-' . time()">
            </x-slot>
        </x-tabs>
    </div>
</div>
