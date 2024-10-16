<div>
    <div class="card border-light shadow-sm warranty-tab">
        <div class="card-body">
            <div class="alert alert-light-primary color-primary"><i class="fas fa-info-circle"></i> Showing registration
                data for
                the last two years from SX. This list is refreshed hourly<span class="float-end"><strong>Last
                        refreshed</strong>
                    {{ Carbon\Carbon::parse($last_refresh_timestamp)->diffForHumans() }}</span></div>
            @if ($non_registered_count > 0)
                <div class="alert alert-light-warning color-warning"><i class="fas fa-exclamation-triangle"></i>
                    <strong>{{ number_format($non_registered_count) }}</strong> products have missing warranty
                    registration(s)
                </div>
            @endif
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <livewire:equipment.warranty.warranty-import.report-table lazy
                        wire:key="{{ 'warranty-report-model' }}">
                </div>
            </div>
        </div>
    </div>
</div>
