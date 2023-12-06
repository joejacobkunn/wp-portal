<div class="product-search-modal">
    <x-modal :toggle="$productSearchModal" size="xl" :closeEvent="'closeProductSearch'">
        <x-slot name="title">
            <div>Select Products</div>
        </x-slot>

        <div class="text-center">
            <button class="btn btn-primary btn-sm add-cart-btn" {{ $loadingCart ? 'disabled' : '' }}
                wire:click="invokeAddToCart">
                @if ($loadingCart)
                    <div>
                        <i class="fa fa-spin fa-spinner me-2"></i> Processing
                    </div>
                @else
                    <div>
                        <i class="fa fa-plus me-2"></i> Add To Cart
                    </div>
                @endif

            </button>
        </div>


        <livewire:product.table :account="$account" from-checkout />

        <div class="text-center">
            <button class="btn btn-primary btn-sm add-cart-btn" {{ $loadingCart ? 'disabled' : '' }}
                wire:click="invokeAddToCart">
                @if ($loadingCart)
                    <div>
                        <i class="fa fa-spin fa-spinner me-2"></i> Processing
                    </div>
                @else
                    <div>
                        <i class="fa fa-plus me-2"></i> Add To Cart
                    </div>
                @endif

            </button>
        </div>


    </x-modal>
</div>
