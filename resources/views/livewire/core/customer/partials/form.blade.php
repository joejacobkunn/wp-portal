<div class="row">
    <div class="col-12 col-md-12">
        <div class="card card-body shadow-sm mb-4">
            <form wire:submit.prevent="submit">

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Customer Email" model="customer.email" prepend-icon="fas fa-at" lazy />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Customer Phone" model="customer.phone"
                            prepend-icon="fas fa-phone-square-alt" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <x-forms.select label="Type" model="customer.customer_type" :options="$customer_types"
                                :selected="$customer->customer_type ?? null" default-selectable default-option-label="- None -" />
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Customer Name" model="customer.name" lazy />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Customer Address Line 1" model="customer.address" lazy />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <x-forms.input label="Customer Address Line 2" model="customer.address2" lazy />
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <x-forms.input label="ZIP Code" model="customer.zip" lazy />
                    </div>

                    <div class="col-md-4 mb-3">
                        <x-forms.input label="City" model="customer.city" lazy />
                    </div>

                    <div class="col-md-4 mb-3">
                        <x-forms.input label="State" model="customer.state" />
                    </div>

                </div>





                <div class="mt-2">
                    <button type="submit" class="btn btn-success">
                        <div wire:loading wire:target="submit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>

                        {{ $button_text }}

                    </button>
                    <button type="button" wire:click="cancel" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
