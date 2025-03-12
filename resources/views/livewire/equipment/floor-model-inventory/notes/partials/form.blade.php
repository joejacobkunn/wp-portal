<div class="row">
    <div class="col-12 col-md-12">
        <form wire:submit.prevent="{{ $submit_action }}">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <textarea class="form-control border-2 mb-4" wire:model.defer="note" placeholder="Enter your note" rows="3"
                            maxlength="1000" required=""></textarea>
                        @error('note')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            <div class="mt-2 float-start">
                <button type="submit" class="btn btn-primary">
                    <div wire:loading wire:target="submit">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    {{ $button_text }}
                </button>

                <button type="button" wire:click="{{ $cancel_action }}" class="btn btn-light-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>
