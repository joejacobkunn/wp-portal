<div class="card border-light shadow-sm mb-4 warranty-tab" >
    <div class="card-body">
            <div class="tab-content mt-4" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel"
                    aria-labelledby="home-tab">
                    <div class="col-md-12 mb-3 text-center" wire:loading wire:target="importData">
                        <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                        <span>Please wait, processing records...</span>
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div wire:loading.remove>
                        @if ( !$this->importAction )
                            @include('livewire.equipment.warranty.warranty-import.partials.form', ['button_text' => 'Import'])
                        @else
                            @include('livewire.equipment.warranty.warranty-import.partials.success', ['records' => $validatedRows])
                        @endif
                    </div>
                </div>
            </div>

    </div>
</div>
