<div>
    <div class="col-md-12 mb-3">
        <h3>Preview</h3>
    </div>
    @if (count($importErrorRows) > 0)
        <div class="col-md-12 mb-3 warranty-import">
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ count($importErrorRows) }} rows will be skipped for processing
                due to improper formation, either correct the phone number or adjust the message length or change the location and try again
                or proceed with import
                <a href="#" wire:click.prevent="downloadInvalidEntries">
                    click here </a>to download invalid entries <i class="fas fa-download"></i>
            </div>
        </div>
    @endif
    @include('livewire.marketing.sms-marketing.partials.table', ['records' => $validatedRows])
</div>
