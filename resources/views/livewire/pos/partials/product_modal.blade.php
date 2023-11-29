
<x-modal :toggle="$productSearchModal" :closeEvent="'closeProductSearch'">
    <x-slot name="title">
        <div>Search Product: {{ $productResult['product_code'] }}</div>
    </x-slot>
    <table class="table">
        <tr>
            <td>Product</td>
            <td class="w-50">{{ $productResult['product_name'] }}</td>
        </tr>
        <tr>
            <td>Product Line</td>
            <td class="w-50">{{ $productResult['prodline'] }}</td>
        </tr>
        <tr>
            <td>Category</td>
            <td>{{ $productResult['category'] }}</td>
        </tr>
        <tr>
            <td>Net Avail</td>
            <td>{{ $productResult['stock'] }}</td>
        </tr>
        <tr>
            <td>Bin Location 1</td>
            <td>{{ $productResult['bin_location'] }}</td>
        </tr>

        <tr>
            <td>Price</td>
            <td>${{ number_format($productResult['price'], 2) }}</td>
        </tr>
        <tr>
            <td>Qty.</td>
            <td>
                <div class="input-group w-75">
                    <div class="input-group-prepend">
                        <button class="btn btn-secondary"
                            {{ $productResult['quantity'] == 1 ? 'disabled' : '' }}
                            type="button" wire:click="updateQuantity(-1)">-</button>
                    </div>
                    <input type="text" class="form-control text-center"
                        value="{{ $productResult['quantity'] }}" id="quantityInput"
                        aria-describedby="basic-addon1">
                    <div class="input-group-append">
                        <button class="btn btn-secondary"
                            {{ $productResult['quantity'] == $productResult['stock'] ? 'disabled' : '' }}
                            type="button" wire:click="updateQuantity(1)">+</button>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="text-center">
        <button class="btn btn-primary btn-lg my-4 add-cart-btn" wire:click="addToCart">
            <div wire:loading wire:target="addToCart">
                <span class="spinner-border spinner-border-sm" role="status"
                    aria-hidden="true"></span>
            </div>

            <div wire:loading.class="d-none" wire:target="addToCart">
                <i class="fa fa-plus me-2"></i> Add To Cart
            </div>
        </button>
    </div>
</x-modal>