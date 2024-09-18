<div>
    <div class="card border-light shadow-sm warranty-tab">
        <div class="card-body">
            <div class="alert alert-light-primary color-primary"><i class="fas fa-info-circle"></i> Showing registration
                date for
                the last six months in SX</div>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <livewire:equipment.warranty.warranty-import.report-table lazy
                        wire:key="{{ 'warranty-report-model' }}">
                </div>
            </div>
        </div>
    </div>
</div>
