<div class="row">
    <div class="col-12 col-md-12">
        <div class="card card-body shadow-sm mb-4">
            <form wire:submit.prevent="submit()">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <x-forms.select label="Warehouse"
                                model="warehouseId"
                                :options="$warehouses"
                                :selected="$warehouseId ?? $defalutLocation"
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
                        @if($product)
                        <div class="form-group x-input">
                            <label>Product</label>
                            <div class="input-group">
                                <input id="product_input-field" type="text" class="form-control" placeholder="Search Product"  wire:model.live.debounce.300ms="product">
                            </div>
                        </div>
                        @else
                            <button type="button" class="btn btn-primary" wire:click="SelectProduct">
                                Add Product
                            </button>
                        @endif
                        @error('product')

                        <p><span class="text-danger">{{ $message }}</span></p>
                        @enderror
                    </div>
                </div>
                <div class="row" style="position: relative; z-index: 0; margin-top: -15px;">
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
                    <button type="button" wire:click="{{ $editRecord ?? false ? 'resetForm' : 'cancel' }}" class="btn btn-light-secondary">
                        {{ $editRecord ?? false ? 'Reset' : 'Cancel' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="product-search-modal">
    <x-modal :toggle="$productSearchModal" size="xl" :closeEvent="'closeProductSearch'">
        <x-slot name="title">
            <div>Select Products</div>
        </x-slot>
        @if($ShowLoader)
            <div class="prod-loading-cart">
                Adding product to form <i class="fa fa-spin fa-spinner ms-1"></i>
            </div>
        @endif
        <div wire:ignore>
            <livewire:equipment.floor-model-inventory.product-table
                :key="'product'"
            />
        </div>

    </x-modal>
</div>
