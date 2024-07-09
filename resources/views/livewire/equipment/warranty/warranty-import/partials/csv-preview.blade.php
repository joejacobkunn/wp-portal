<div>
    <div class="col-md-12 mb-3">
        <h3>Preview</h3>
    </div>
    @if ( count($importErrorRows) > 0 )
        <div class="col-md-12 mb-3 warranty-import">
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ count($importErrorRows) }} Erros found on the uploaded file,
                    <a href="#" wire:click.prevent="downloadInvalidEntries">
                        click here </a>to download invalid entries <i class="fas fa-download"></i>
            </div>
        </div>
    @endif
    @include('livewire.equipment.warranty.warranty-import.partials.table', ['records' => $validatedRows])
</div>

