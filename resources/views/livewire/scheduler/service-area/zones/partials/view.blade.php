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
                    wire:key="{{ 'status_alert_' . $zone->id . '_' . $alertConfig['level'] }}" />
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Zone Name</h3>
                            <p class="small pe-4">{{ $zone->name }}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                        <div>
                            <h3 class="h6 mb-1">Description</h3>
                            <p class="small pe-4">{{ $zone->description }}</p>
                        </div>
                    </li>

                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                        <div>
                            <h3 class="h6 mb-1">Last Updated At</h3>
                            <p class="small pe-4">
                                {{ $zone->updated_at?->format(config('app.default_datetime_format')) }}</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>


    </div>
    <div class="col-12 col-md-4 col-xxl-4">
        <x-tabs :tabs="$tabs" tabId="zones-comment-tabs" activeTabIndex="active">
            <x-slot:tab_content_comments component="x-comments" :entity="$zone" :key="'comments' . time()">
            </x-slot>

            <x-slot:tab_content_activity component="x-activity-log" :entity="$zone" recordType="zones" :key="'activity-' . time()">
            </x-slot>
        </x-tabs>
    </div>
</div>
