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
                            <h3 class="h6 mb-1">Service</h3>
                            <p class="small pe-4">{{ $zone->service->label() }}</p>
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

        <div class="card border-light shadow-sm mb-4" wire:key="zone-zipcodes-{{ $zipodesKey }}">
            @php
                $zoneZipcodes = $this->zone->zipcodes->sortBy('zip_code')->unique('zip_code');
            @endphp
            <div class="card-header border-gray-300 p-3 mb-2 mb-md-0">
                <p class="h5 mb-0"><i class="fas fa-map-marker-alt me-1"></i> ZIP Codes
                    <button type="button" wire:click="showZipcodeZoneForm"
                        class="btn btn-outline-success mb-2 float-end">
                        <span wire:loading wire:target="showZipcodeZoneForm" class="spinner-border spinner-border-sm"
                            role="status" aria-hidden="true"></span>
                        <i class="fa fa-plus"></i> Add New ZIP Code
                    </button>
                </p>

            </div>

            <div class="card-body">
                @if ($zoneZipcodes->count())
                    <ul class="list-group list-group-flush">
                        @foreach ($zoneZipcodes as $zipcode)
                            <li
                                class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                <strong>{{ $zipcode->zip_code }} - {{ $zipcode->generalZipcode->city }},
                                    {{ $zipcode->generalZipcode->state }}</strong>
                                <button class="btn btn-outline-danger mb-2 float-end"
                                    wire:click="removeZipcode({{ $zipcode->id }})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>No zip codes configured in this zone</p>
                @endif
            </div>
        </div>

    </div>
    <div class="col-12 col-md-4 col-xxl-4">
        {{-- <div class="card border-light shadow-sm mb-4">
            <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                <button wire:click="createZipcode" class="btn btn-primary btn-lg btn-fab"><i class="fas fa-plus"></i></button>
                <h3 class="h5 mb-0"><i class="fas fa-bars me-1"></i>Zipcodes in {{ $this->zone->warehouse->title }}</h3>
            </div>

            <div class="card-body">
                @if ($addZipcodeRecord)
                    @include('livewire.scheduler.service-area.zip-code.partials.form', [
                        'button_text' => 'Add ZIP Code',
                    ])
                @else
                    <livewire:scheduler.service-area.zip-code.table wire:key="'zipcode'" whseId="{{ $zone->whse_id }}" lazy>
                @endif
            </div>
        </div> --}}
        <x-tabs :tabs="$tabs" tabId="zones-comment-tabs" activeTabIndex="active">
            <x-slot:tab_content_comments component="x-comments" :entity="$zone" :key="'comments' . time()">
            </x-slot>

            <x-slot:tab_content_activity component="x-activity-log" :entity="$zone" recordType="zones" :key="'activity-' . time()">
            </x-slot>
        </x-tabs>
    </div>
    @if ($assignZipcde)
        <x-modal toggle="assignZipcde" size="md" :closeEvent="'closeZipcodeAssign'">
            <x-slot name="title">Assign ZIP Code </x-slot>
            <div class="col-md-12 mb-2">
                <div class="form-group">
                    <x-forms.select label="Zipcode" model="assignZipcodes" :options="$this->zipcodes" :selected="$assignZipcodes"
                        :multiple="true" hasAssociativeIndex default-option-label="- None -" :key="'zipcode' . now()" />

                </div>
            </div>
            <x-slot name="footer">
                <button type="submit" class="btn btn-secondary" wire:click="saveZipcode">
                    <div wire:loading wire:target="saveZipcode">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    Assign
                </button>
            </x-slot>
        </x-modal>
    @endif
</div>
