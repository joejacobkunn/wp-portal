<div class="row">
    <div class="col-12 col-md-12">
        <form wire:submit.prevent="submit()">
            @if ($addRecord ?? false)
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <x-forms.select label="Warehouse" model="warehouseId" :options="$warehouses" :selected="$warehouseId"
                                hasAssociativeIndex default-option-label="- None -" label-index="title" value-index="id"
                                :key="'warehouse-' . now()" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group x-input">
                            <label>Product</label>
                            <div class="input-group">
                                <input id="product_input-field" type="text" class="form-control"
                                    placeholder="Enter Prod Code" wire:model.blur="product">
                            </div>
                        </div>
                        @error('product')
                            <p><span class="text-danger">{{ $message }}</span></p>
                        @enderror
                    </div>
                </div>
            @endif
            @if ($showBox && !is_null($matchedProduct))
                <div class="row">
                    @include('livewire.equipment.floor-model-inventory.inventory.partials.product-card', [
                        'product' => config('sx.mock') ? $matchedProduct->prod : $matchedProduct->Prod,
                        'brand' => config('sx.mock') ? $matchedProduct->brand?->name : $matchedProduct->Brand,
                        'description' => config('sx.mock')
                            ? $matchedProduct->description
                            : $matchedProduct->Description,
                    ])
                </div>
            @endif
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <x-forms.select label="Quantity" model="qty" :options="['Please Select', '0', '1', '2', '3']" :selected="$qty"
                            :defaultOption=false />
                    </div>
                </div>
            </div>
            <hr>
            <div class="mt-2 float-start">
                <x-button-submit
                    class="btn-primary"
                    method="submit"
                    no-icon
                    :text="$button_text" />

                <button type="button" wire:click="cancel" class="btn btn-light-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>
