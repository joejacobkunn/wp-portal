<div>

    <x-page :breadcrumbs="$breadcrumbs">
        <x-slot:title>Checkout</x-slot>
        <x-slot:content>
            <div class="card border-light shadow-sm mb-4">
                <div class="card-content">
                    <div class="card-body">
                        <div class="checkout-outer-div p-3">
                            <div class="accordion accordion-flush" id="accordionFlushExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="flush-headingOne">
                                        <button
                                            class="accordion-button {{ $activeTab == 1 ? '' : 'collapsed' }}"
                                            {{ $activeTab == 1 ? 'disabled' : '' }}
                                            type="button"
                                            wire:click="selectTab(1)">
                                            1. <span class="title-span"><i class="fas fa-cart-plus"></i> Cart 

                                                <label class=" ms-2" wire:loading wire:target="selectTab(1)">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                </label>
                                            </span>
                                            <p class="mb-0 brief-info">{{ count($cart) ? count($cart) . ' Items selected' : '' }}</p>
                                        </button>
                                    </h2>
                                    <div id="flush-collapseOne" class="accordion-collapse collapse {{ $activeTab == 1 ? 'show' : '' }}"
                                        aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample"
                                        style="">
                                        <div class="accordion-body">
                                            <div>
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <x-forms.input label="Search Product" model="productQuery"
                                                            defer />
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <button class="btn btn-primary mt-4 search-btn"
                                                            type="button" wire:click="searchProduct">
                                                            <div wire:loading wire:target="searchProduct">
                                                                <span class="spinner-border spinner-border-sm"
                                                                    role="status" aria-hidden="true"></span>
                                                            </div>

                                                            <div wire:loading.class="d-none"
                                                                wire:target="searchProduct">
                                                                <i class="fa fa-search"></i> Search
                                                            </div>
                                                        </button>
                                                    </div>
                                                </div>

                                                @if (count($cart))
                                                    <hr />
                                                    <h4 class="mt-4">Total Items selected ({{ count($cart) }})</h4>
                                                    <table class="table">
                                                        <thead>
                                                            <th>Product</th>
                                                            <th>Line Item</th>
                                                            <th>Bin Loc</th>
                                                            <th>Stock</th>
                                                            <th>Price</th>
                                                            <th class="w-10">Qty.</th>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($cart as $item)
                                                                <tr>
                                                                    <td>{{ $item['product_name'] }}</td>
                                                                    <td>{{ $item['prodline'] }}</td>
                                                                    <td>{{ $item['bin_location'] }}</td>
                                                                    <td>{{ $item['stock'] }}</td>
                                                                    <td>${{ number_format($item['price'], 2) }}</td>
                                                                    <td>
                                                                        <div class="quantity-div">
                                                                            <div class="input-group w-75">
                                                                                <div class="input-group-prepend">
                                                                                    <button class="btn btn-secondary"
                                                                                        type="button"
                                                                                        wire:click="updateQuantity(-1, '{{ $item['product_code'] }}')">-</button>
                                                                                </div>
                                                                                <input type="text"
                                                                                    class="form-control text-center"
                                                                                    value="{{ $item['quantity'] }}"
                                                                                    id="quantityInput"
                                                                                    aria-describedby="basic-addon1">
                                                                                <div class="input-group-append">
                                                                                    <button class="btn btn-secondary"
                                                                                        type="button"
                                                                                        wire:click="updateQuantity(1, '{{ $item['product_code'] }}')">+</button>
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
                                        <button
                                            class="accordion-button {{ $activeTab == 2 ? '' : 'collapsed' }}" type="button"
                                            {{ count($cart) ? '' : 'disabled' }}
                                            wire:click="selectTab(2)">
                                            2. <span class="title-span"><i class="fas fa-user-edit"></i> Customer
                                                Details
                                                <label class=" ms-2" wire:loading wire:target="selectTab(2)">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                </label>
                                            </span>
                                            <p class="mb-0 brief-info">{!! $this->customerPanelHint !!}</p>
                                        </button>
                                    </h2>
                                    <div id="flush-collapseTwo" class="accordion-collapse collapse {{ $activeTab == 2 ? 'show' : '' }}"
                                        aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                                        <div class="accordion-body">
                                            <div>
                                                <strong>
                                                <x-forms.checkbox
                                                    label="Waive Customer Info"
                                                    name="waive_customer_info"
                                                    :value="1"
                                                    :model="'waiveCustomerInfo'"
                                                />
                                                </strong>

                                                @if(! $waiveCustomerInfo)
                                                    <hr />

                                                    @if(empty($customerSelected))
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <x-forms.input
                                                                    label="Search Customer"
                                                                    model="customerQuery"
                                                                    placeholder="Search By Name / SX # / Address / Phone #"
                                                                    defer />
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <button class="btn btn-primary mt-4 search-btn"
                                                                    type="button" wire:click="searchCustomer">
                                                                    <div wire:loading wire:target="searchCustomer">
                                                                        <span class="spinner-border spinner-border-sm"
                                                                            role="status" aria-hidden="true"></span>
                                                                    </div>

                                                                    <div wire:loading.class="d-none"
                                                                        wire:target="searchCustomer">
                                                                        <i class="fa fa-search"></i> Search
                                                                    </div>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @else
                                                    <div>
                                                        <label>{{ $customerSelected['name'] }}</label>
                                                        <p class="mb-0">SX #: {{ $customerSelected['sx_customer_number'] }}</p>
                                                        <p class="mb-0">Address: {{ $customerSelected['full_address'] }}</p>
                                                        <p class="mb-0">Email: {{ $customerSelected['email'] }}</p>
                                                        <p class="mb-0">Phone: {{ $customerSelected['phone'] }}</p>
                                                        <button class="btn btn-outline-danger btn-sm mt-2" wire:click="resetCustomerSelection">
                                                            <i class="fa fa-trash"></i> Remove
                                                        </button>
                                                    </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="flush-headingThree">
                                        <button
                                            class="accordion-button {{ $activeTab == 3 ? '' : 'collapsed' }}"
                                            type="button" 
                                            {{ !empty($customerSelected) || !empty($waiveCustomerInfo) ? '' : 'disabled' }}
                                            wire:click="selectTab(3)">
                                            3. <span class="title-span"><i class="far fa-credit-card"></i>
                                                Payment

                                                <label class=" ms-2" wire:loading wire:target="selectTab(3)">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                </label>
                                            </span>
                                            <p class="mb-0 brief-info">{{ !empty($customerSelected) ? $customerSelected['name'] : '' }}</p>
                                        </button>
                                    </h2>
                                    <div id="flush-collapseThree" class="accordion-collapse collapse  {{ $activeTab == 3 ? 'show' : '' }}"
                                        aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
                                        <div class="accordion-body">Payment part</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    @if ($productSearchModal)
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
                                    <td>Prod Line</td>
                                    <td class="w-50">{{ $productResult['prodline'] }}</td>
                                </tr>
                                <tr>
                                    <td>Category</td>
                                    <td>{{ $productResult['category'] }}</td>
                                </tr>
                                <tr>
                                    <td>Availability</td>
                                    <td>{{ $productResult['stock'] }}</td>
                                </tr>
                                <tr>
                                    <td>Bin Location</td>
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
                    @endif

                    @if ($customerSearchModal)
                        <x-modal :toggle="$customerSearchModal" :size="'lg'" :closeEvent="'closeCustomerSearch'">
                            <x-slot name="title">
                                <div>Search Customer: {{ $lastCustomerQuery }}</div>
                            </x-slot>

                            <p>Select Customer for billing</p>

                            <div class="customer-outer-div">
                                @foreach($customerResult as $customer)
                                    <div class="customer-ind-div {{ $customer['id'] == $customerResultSelected['id'] ? 'selected' : '' }}" wire:click="selectCustomer('{{ $customer['id'] }}')">
                                        <div class="selected-label"><i class="fa fa-check-circle"></i></div>
                                        <label>{{ $customer['name'] }}</label>
                                        <p class="mb-0">SX #: {{ $customer['sx_customer_number'] }}</p>
                                        <p class="mb-0">Address: {{ $customer['full_address'] }}</p>
                                        <p class="mb-0">Email: {{ $customer['email'] }}</p>
                                        <p class="mb-0">Phone: {{ $customer['phone'] }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <div class="text-center">
                                <button class="btn btn-primary btn-lg my-4 add-cart-btn" wire:click="proceedToPayment">
                                    <div wire:loading wire:target="proceedToPayment">
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                    </div>

                                    <div wire:loading.class="d-none" wire:target="proceedToPayment">
                                        Proceed <i class="fas fa-arrow-circle-right"></i>
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
