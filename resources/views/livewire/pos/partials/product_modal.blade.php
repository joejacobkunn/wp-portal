<div class="product-search-modal">
    <x-modal :toggle="$productSearchModal" size="xl" :closeEvent="'closeProductSearch'">
        <x-slot name="title">
            <div>Select Products</div>
        </x-slot>

        <livewire:product.table :account="$account" from-checkout />

    </x-modal>
</div>
