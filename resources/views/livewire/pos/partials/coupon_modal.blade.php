<div class="product-search-modal">
    <x-modal :toggle="$couponSearchModal" size="xl" :closeEvent="'closeCouponSearch'">
        <x-slot name="title">
            <div>Search Coupons</div>
        </x-slot>

        <div wire:ignore>
            <livewire:product.table
                :account="$account"
                from-checkout
                :only-lines="$excemptedProductLines"
                table-type="coupon"
                :key="'product-coupons'"
            />
        </div>

    </x-modal>
</div>
