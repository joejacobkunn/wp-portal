<div class="row">
    <div class="col-12 col-md-12">
        <form wire:submit.prevent="submit">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <x-forms.input label="Customer Number" model="customerNumber" lazy />
                </div>
                <div class="col-md-6 mb-3">
                    <x-forms.input label="Ship To" model="shipTo" lazy />
                </div>
                <div class="col-md-12 mb-3">
                    <x-forms.input label="Product Line" model="prodLine" lazy />
                </div>
                <div class="col-md-12 mb-3">
                    <x-forms.input label="Sales Rep" model="salesRep" lazy />
                </div>

                <div class="mt-2 float-start">

                    <button type="submit" class="btn btn-primary">
                        <div wire:loading wire:target="submit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        {{ $button_text }}
                    </button>

                    <button type="button" wire:click="cancel" class="btn btn-light-secondary">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
