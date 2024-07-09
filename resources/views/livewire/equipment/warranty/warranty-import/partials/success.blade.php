<div class="row">
    @if(!empty($importErrorRows))
        <div class="col-md-12 mb-3 warranty-import">
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ count($importErrorRows) }} Erros found on the uploaded file,
                    <a href="#" wire:click.prevent="downloadInvalidEntries">
                        click here </a>to download invalid entries <i class="fas fa-download"></i>
            </div>
        </div>
    @endif
    <div class="col-md-12 mb-3">
        <h2>Imported Records</h2>
    </div>
    @include('livewire.equipment.warranty.warranty-import.partials.table', ['records' => $validatedRows])
</div>

