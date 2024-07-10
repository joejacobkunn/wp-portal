<div class="card border-light shadow-sm mb-4 warranty-tab">
    <div class="card-body">
        <div class="tab-content mt-4" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="col-md-12 mb-3 text-center" wire:loading>
                    <span><strong>Please wait, processing records...</strong></span>
                    <span class="sr-only">Loading...</span>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                    </div>
                </div>
                <div wire:loading.remove>
                    @if (!$this->importAction)
                        @include('livewire.equipment.warranty.warranty-import.partials.form', [
                            'button_text' => 'Import',
                        ])
                    @else
                        @include('livewire.equipment.warranty.warranty-import.partials.success', [
                            'records' => $validatedRows,
                        ])
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
