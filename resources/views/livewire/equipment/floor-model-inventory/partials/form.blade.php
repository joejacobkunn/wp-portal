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
                            <div class="col-md-12 mb-3">
                                <div class="card border shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Product Details</h5>
                                        <small class="badge bg-light-warning">product/brand/description</small>
                                        <div wire:loading.class="opacity-50">
                                            <ul class="list-group mb-2">
                                                    <li class="list-group-item">{{ $matchedProduct->prod.'/ '.$matchedProduct->brand->name.'/ '.$matchedProduct->description }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                @endif
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <x-forms.select label="Quantity"
                                model="qty"
                                :options="['0','1','2','3']"
                                :selected="$qty"
                                default-option-label="- None -"
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
                    @if($addRecord ?? false)
                        <button type="button" wire:click="cancel" class="btn btn-light-secondary">Cancel</button>
                    @endif
                </div>
            </form>
    </div>
</div>
