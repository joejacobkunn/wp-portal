<div class="product-search-modal">
    <x-modal toggle="productSearchModal" size="xl" :closeEvent="'closeProductSearch'">
        <x-slot name="title">
            <div>Select Products</div>
        </x-slot>

        @if($loadingCart)
            <div class="prod-loading-cart">
                Updating Cart <i class="fa fa-spin fa-spinner ms-1"></i>
            </div>
        @endif
        <div wire:ignore>
            <livewire:product.table
                :account="$account"
                from-checkout
                :except-lines="$excemptedProductLines"
                :key="'product'"
            />
        </div>

    </x-modal>
</div>
