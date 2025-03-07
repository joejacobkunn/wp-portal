<div class="row">
    <form wire:submit.prevent="submit">
        <div class="col-12 col-md-12">
            <div class="card card-body shadow-sm mb-2">
                <div class="row">
                    <!-- Truck Name -->
                    <div class="col-md-6 mb-3">
                        <x-forms.select label="Product Category" model="cargoForm.product_category_id" :options="$productCategories" :selected="$cargoForm->product_category_id"
                            :hasAssociativeIndex="true" default-selectable default-option-label="- Select Category -" />
                    </div>

                    <!-- VIN Number -->
                    <div class="col-md-6 mb-3">
                        <x-forms.input label="Weight" model="cargoForm.weight" lazy appendText="lb" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <x-forms.input label="Height" model="cargoForm.height" appendText="ft" lazy />
                    </div>
                    <div class="col-md-4 mb-2">
                        <x-forms.input label="Width" model="cargoForm.width" appendText="ft" lazy />
                    </div>
                    <div class="col-md-4 mb-2">
                        <x-forms.input label="Length" model="cargoForm.length" appendText="ft" lazy />
                    </div>
                </div>
                <hr>

                <!-- Submit and Cancel Buttons -->
                <div class="mt-2 float-start">
                    <button type="submit" class="btn btn-success">
                        <div wire:loading wire:target="submit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        {{ $button_text }}
                    </button>
                    <button type="button" wire:click="cancel" class="btn btn-light-secondary">Cancel</button>
                </div>
            </div>

        </div>
    </form>
</div>
