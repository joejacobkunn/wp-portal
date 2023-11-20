<div>

    <x-page
        :breadcrumbs="$breadcrumbs"
    >
        <x-slot:title>Checkout</x-slot>
        <x-slot:content>
            <div class="card border-light shadow-sm mb-4">
                <div class="card-content">
                    <div class="card-body">
                        <div class="checkout-outer-div p-3">
                        <div class="accordion accordion-flush" id="accordionFlushExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="flush-headingOne">
                                        <button class="accordion-button " type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                            1. <span class="title-span"><i class="fas fa-cart-plus"></i> Cart</span>
                                            <p class="mb-0 brief-info">{{ count($cart) ? count($cart) . ' Items selected' : '' }}</p>
                                        </button>
                                    </h2>
                                    <div id="flush-collapseOne" class="accordion-collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample" style="">
                                        <div class="accordion-body">
                                            <div>
                                                <div>
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <x-forms.input
                                                                label="Search Product" 
                                                                model="productQuery" 
                                                                defer />
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <button
                                                                class="btn btn-primary mt-4 search-btn"
                                                                type="button" wire:click="searchProduct">
                                                                <div wire:loading wire:target="searchProduct">
                                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                                </div>

                                                                <div wire:loading.class="d-none" wire:target="searchProduct">
                                                                    <i class="fa fa-search"></i> Search
                                                                </div>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if(count($cart))
                                                <hr/>
                                                <h4 class="mt-4">Total Items selected ({{ count($cart) }})</h4>
                                                <table class="table">
                                                    <thead>
                                                        <th>Product</th>
                                                        <th>Price</th>
                                                        <th class="w-10">Qty.</th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($cart as $item)
                                                        <tr>
                                                            <td>{{ $item['product_name'] }}</td>
                                                            <td>{{ number_format($item['price'], 2) }} USD</td>
                                                            <td>
                                                                <div class="quantity-div">
                                                                    <div class="input-group w-75">
                                                                        <div class="input-group-prepend">
                                                                            <button class="btn btn-secondary" type="button" wire:click="updateQuantity(-1, '{{ $item['product_code'] }}')">-</button>
                                                                        </div>
                                                                        <input type="text" class="form-control text-center" value="{{ $item['quantity'] }}" id="quantityInput" aria-describedby="basic-addon1">
                                                                        <div class="input-group-append">
                                                                            <button class="btn btn-secondary" type="button" wire:click="updateQuantity(1, '{{ $item['product_code'] }}')">+</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="flush-headingTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="true" aria-controls="flush-collapseTwo">
                                            2. <span class="title-span"><i class="fas fa-user-edit"></i> Customer Details</span>
                                            <p class="mb-0 brief-info">Adrian Luna</p>
                                        </button>
                                    </h2>
                                    <div id="flush-collapseTwo" class="accordion-collapse collapse " aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                                        <div class="accordion-body">Customer part</div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="flush-headingThree">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                                            3. <span class="title-span"><i class="far fa-credit-card"></i> Payment</span>
                                        </button>
                                    </h2>
                                    <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
                                        <div class="accordion-body">Payment part</div>
                                    </div>
                                </div>
							</div>
                        </div>
                    </div>
                </div>

                <div>
                    @if($productSearchModal)
                    <x-modal :toggle="$productSearchModal">
                        <x-slot name="title">
                            <div>Search Product: {{ $productResult['product_code'] }}</div>
                        </x-slot>
                        <table class="table">
                            <tr>
                                <td>Product</td>
                                <td class="w-50">{{ $productResult['product_name'] }}</td>
                            </tr>
                            <tr>
                                <td>Category</td>
                                <td>{{ $productResult['category'] }}</td>
                            </tr>
                            <tr>
                                <td>Price</td>
                                <td>{{ number_format($productResult['price'], 2) }} USD</td>
                            </tr>
                            <tr>
                                <td>Qty.</td>
                                <td>
                                    <div class="input-group w-75">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-secondary" {{ $productResult['quantity'] == 1 ? 'disabled' : '' }} type="button" wire:click="updateQuantity(-1)">-</button>
                                        </div>
                                        <input type="text" class="form-control text-center" value="{{ $productResult['quantity'] }}" id="quantityInput" aria-describedby="basic-addon1">
                                        <div class="input-group-append">
                                            <button class="btn btn-secondary" {{ $productResult['quantity'] == $productResult['stock'] ? 'disabled' : '' }}  type="button" wire:click="updateQuantity(1)">+</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <div class="text-center">
                            <button class="btn btn-primary btn-lg my-4 add-cart-btn" wire:click="addToCart">
                                <div wire:loading wire:target="addToCart">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                </div>

                                <div wire:loading.class="d-none" wire:target="addToCart">
                                    <i class="fa fa-plus me-2"></i> Add To Cart
                                </div>
                            </button>
                        </div>
                    </x-modal>
                    @endif

                </div>
            </div>


        </x-slot>

    </x-page>

</div>
