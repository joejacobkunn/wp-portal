<div class="row">
    <div class="col-12 col-md-12">
        <form wire:submit.prevent="submit">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <x-forms.input label="Customer Number" model="customerNumber" disabled="{{isset($editRecord) ? true: false}}" live />
                    @if ($customerName)
                        <p class="text-success"><i class="far fa-check-circle"></i> {{ $customerName }}</p>
                    @endif
                </div>
                <div class="col-md-12 mb-3">
                    <x-forms.input label="Ship To" model="shipTo" disabled="{{isset($editRecord) ? true: false}}" live />
                    @if ($address)
                        <p class="text-success"><i class="far fa-check-circle"></i> {{ $address }}</p>
                    @endif
                </div>
                <div class="col-md-12 mb-3">
                    <x-forms.input label="Product Line" model="prodLine"  live />
                    @if ($line)
                        <p class="text-success"><i class="far fa-check-circle"></i> {{ strtoupper($line) }}</p>
                    @endif
                </div>
                <div class="col-md-12 mb-3">
                    <x-forms.input label="Sales Rep" model="salesRep" live />
                    @if ($operator)
                        <p class="text-success"><i class="far fa-check-circle"></i> {{ strtoupper($operator) }}</p>
                    @endif

                </div>

                <div class="mt-2 float-start">

                    <button type="submit" class="btn btn-primary" @if ($this->notEligibleForSubmit()) disabled @endif>
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
