<div>
    <div class="card border-light shadow-sm warranty-tab">
        <div class="card-body">
            <div class="alert alert-light-primary color-primary"><i class="fas fa-info-circle"></i> Showing registration
                data for
                the last one year from SX. This list is refreshed hourly<span class="float-end"><strong>Last
                        refreshed</strong>
                    {{ Carbon\Carbon::parse($last_refresh_timestamp)->diffForHumans() }}</span></div>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <livewire:equipment.warranty.warranty-import.report-table lazy
                        wire:key="{{ 'warranty-report-model' }}">
                </div>
            </div>
        </div>
    </div>
</div>
