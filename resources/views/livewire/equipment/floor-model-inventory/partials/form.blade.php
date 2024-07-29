<div class="row">
    <div class="col-12 col-md-12">
            <form wire:submit.prevent="submit()">
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
                                <input id="product_input-field" type="text" class="form-control" placeholder="Search Product"  wire:model.live.debounce.300ms="product">
                            </div>
                        </div>
                        @error('product')
                        <p><span class="text-danger">{{ $message }}</span></p>
                        @enderror
                    </div>
                </div>
                @if ($showBox)
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="card border shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">Matching Products</h5>
                                    <small class="badge bg-light-warning">product/brand/description</small>
                                    <div wire:loading.class="opacity-50" wire:target="nextPage,previousPage">
                                        <ul class="list-group mb-2">
                                            @foreach ($products as $item)
                                                <li class="list-group-item">{{ $item->prod.'/ '.$item->brand->name.'/ '.$item->description }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @if ($totalCount > $limit)
                                        <nav aria-label="Page navigation">
                                            <ul class="pagination">
                                                <li class="page-item {{ $offset == 0 ? 'disabled' : '' }}">
                                                    <a class="page-link" href="#" wire:click.prevent="previousPage" aria-label="Previous">
                                                        <span aria-hidden="true">&laquo;</span>
                                                        <span class="sr-only">Previous</span>
                                                    </a>
                                                </li>
                                                <li class="page-item {{ ($offset + $limit) >= $totalCount ? 'disabled' : '' }}">
                                                    <a class="page-link" href="#" wire:click.prevent="nextPage" aria-label="Next">
                                                        <span aria-hidden="true">&raquo;</span>
                                                        <span class="sr-only">Next</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </nav>
                                    @endif
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
                    <button type="button" wire:click="{{ $editRecord ?? false ? 'resetForm' : 'cancel' }}" class="btn btn-light-secondary">
                        {{ $editRecord ?? false ? 'Reset' : 'Cancel' }}
                    </button>
                </div>
            </form>
    </div>
</div>
