<div class="row">
    <div class="col-12 col-md-12">
            <form wire:submit.prevent="submit()">
                @if($addRecord ?? false)
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <x-forms.select label="Warehouse"
                                    model="warehouseId"
                                    :options="$warehouses"
                                    :selected="$warehouseId"
                                    hasAssociativeIndex
                                    default-option-label="- None -"
                                    label-index="title"
                                    value-index="id"
                                    :key="'warehouse-' . now()" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-group x-input">
                                <label>Product</label>
                                <div class="input-group">
                                    <input id="product_input-field" type="text" class="form-control" placeholder="Search Product"  wire:model.blur="product">
                                </div>
                            </div>
                            @error('product')
                            <p><span class="text-danger">{{ $message }}</span></p>
                            @enderror
                        </div>
                    </div>
                    @endif
                    @if ($showBox)
                        <div class="row">
                            @include('livewire.equipment.floor-model-inventory.partials.product-card',[
                                'product' => $matchedProduct->prod ?? '--',
                                'brand'=> $matchedProduct->brand?->name ?? '--',
                                'description' => $matchedProduct->description ?? '--'
                                ])
                        </div>
                    @endif
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <x-forms.select label="Quantity"
                                model="qty"
                                :options="['0','1','2','3']"
                                :selected="$qty"
                                :defaultOption=false
                                :key="'qty-' . now()" />
                        </div>
                    </div>
                </div>
                <hr>
                <div class="mt-2 float-start">
                    <button type="submit" class="btn btn-primary">
                        <div wire:loading wire:target="submit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        {{ $button_text }}
                    </button>
                        <button type="button" wire:click="cancel" class="btn btn-light-secondary">Cancel</button>
                </div>
            </form>
    </div>
</div>
