
<div class="accordion accordion-flush {{ $orderStatus == 'completed' ? 'd-none' : '' }}" id="accordionFlushExample">
    <div class="accordion-item">
        <h2 class="accordion-header" id="flush-headingOne">
            <button class="accordion-button {{ $activeTab == 1 ? '' : 'collapsed' }}"
                {{ $activeTab == 1 ? 'disabled' : '' }} type="button"
                wire:click="selectTab(1)">
                1. <span class="title-span"><i class="fas fa-cart-plus"></i> Cart

                    <label class=" ms-2" wire:loading wire:target="selectTab(1)">
                        <span class="spinner-border spinner-border-sm" role="status"
                            aria-hidden="true"></span>
                    </label>
                </span>
                <span class="badge bg-light-primary">
                    <strong>{{ count($cart) ? count($cart) . ' Item(s) selected' : '' }}</strong>
                </span>
            </button>
        </h2>
        <div id="flush-collapseOne"
            class="accordion-collapse collapse {{ $activeTab == 1 ? 'show' : '' }}"
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
                            <button class="btn btn-primary mt-4 search-btn" type="button"
                                wire:click="searchProduct">
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
                        <table class="table table-hover">
                            <thead>
                                <th>Product</th>
                                <th>Product Line</th>
                                <th>Bin Loc 1</th>
                                <th>Net Avail</th>
                                <th>Price</th>
                                <th class="w-10">Qty.</th>
                            </thead>
                            <tbody>
                                @foreach ($cart as $item)
                                    <tr>
                                        <td><a href="https://weingartz.com//searchPage.action?keyWord={{ $item['product_code'] }}"
                                                target="_blank">{{ $item['product_name'] }}</a>
                                        </td>
                                        <td>{{ $item['prodline'] }}</td>
                                        <td>{{ $item['bin_location'] }}</td>
                                        <td>{{ $item['stock'] }}</td>
                                        <td>${{ number_format($item['price'], 2) }}</td>
                                        <td>
                                            <div class="quantity-div">
                                                <div class="input-group w-75">
                                                    <div class="input-group-prepend">
                                                        <button
                                                            class="btn btn-{{ $item['quantity'] == 1 ? 'danger' : 'secondary' }}"
                                                            type="button"
                                                            wire:click="updateQuantity(-1, '{{ $item['product_code'] }}')">{!! $item['quantity'] == 1 ? '<i class="fa fa-trash"></i>' : '<i class="fa fa-minus"></i>' !!}</button>
                                                    </div>
                                                    <input type="text"
                                                        class="form-control text-center"
                                                        value="{{ $item['quantity'] }}"
                                                        id="quantityInput"
                                                        aria-describedby="basic-addon1">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-secondary"
                                                            type="button"
                                                            wire:click="updateQuantity(1, '{{ $item['product_code'] }}')"><i
                                                                class="fa fa-plus"></i></button>
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
            <button class="accordion-button {{ $activeTab == 2 ? '' : 'collapsed' }}"
                type="button" {{ count($cart) ? '' : 'disabled' }}
                wire:click="selectTab(2)">
                2. <span class="title-span"><i class="fas fa-user-edit"></i> Customer
                    Details
                    <label class=" ms-2" wire:loading wire:target="selectTab(2)">
                        <span class="spinner-border spinner-border-sm" role="status"
                            aria-hidden="true"></span>
                    </label>
                </span>
                <p class="mb-0 brief-info">{!! $this->customerPanelHint !!}</p>
            </button>
        </h2>
        <div id="flush-collapseTwo"
            class="accordion-collapse collapse {{ $activeTab == 2 ? 'show' : '' }}"
            aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">
                <div>
                    <strong>
                        <x-forms.checkbox label="Waive Customer Info"
                            name="waive_customer_info" :value="1"
                            :model="'waiveCustomerInfo'" />
                    </strong>

                    @if (!$waiveCustomerInfo)
                        <hr />

                        @if (empty($customerSelected))
                            <div class="row">
                                <div class="col-sm-4">
                                    <x-forms.input label="Search Customer"
                                        model="customerQuery"
                                        placeholder="Search By Name / SX # / Address / Phone # / Email"
                                        defer />
                                </div>
                                <div class="col-sm-8">
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

                                    <button class="btn btn-outline-success ms-2 mt-4"
                                        type="button" wire:click="newCustomer">
                                        <div wire:loading wire:target="newCustomer">
                                            <span class="spinner-border spinner-border-sm"
                                                role="status" aria-hidden="true"></span>
                                        </div>

                                        <div wire:loading.class="d-none"
                                            wire:target="newCustomer">
                                            <i class="fa fa-plus"></i> New Customer
                                        </div>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div>
                                <label>{{ $customerSelected['name'] }}</label>
                                <p class="mb-0"><strong>SX #</strong>
                                    {{ $customerSelected['sx_customer_number'] }}</p>
                                <p class="mb-0"><strong>Address</strong>:
                                    {{ $customerSelected['full_address'] }}</p>
                                <p class="mb-0"><strong>Email</strong>:
                                    {{ $customerSelected['email'] }}
                                </p>
                                <p class="mb-0"><strong>Phone</strong>:
                                    {{ format_phone($customerSelected['phone']) }}
                                </p>
                                <button class="btn btn-outline-danger btn-sm mt-2"
                                    wire:click="resetCustomerSelection">
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
            <button class="accordion-button {{ $activeTab == 3 ? '' : 'collapsed' }}"
                type="button"
                {{ !empty($customerSelected) || !empty($waiveCustomerInfo) ? '' : 'disabled' }}
                wire:click="selectTab(3)">
                3. <span class="title-span"><i class="far fa-credit-card"></i>
                    Payment

                    <label class=" ms-2" wire:loading wire:target="selectTab(3)">
                        <span class="spinner-border spinner-border-sm" role="status"
                            aria-hidden="true"></span>
                    </label>
                </span>
                <span class="badge bg-light-secondary">Total:
                    {{ format_money($netPrice) }}
                </span>
            </button>
        </h2>
        <div id="flush-collapseThree"
            class="accordion-collapse collapse  {{ $activeTab == 3 ? 'show' : '' }}"
            aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">
                <div class="payment-outer-div mt-3 mb-4 d-none1">
                    <div class="row mb-4">
                        <div class="col-sm-3">
                            <label>Total Amount</label>
                        </div>
                        <div class="col-sm-9">
                            <div class="price-value-div">{{ format_money($netPrice) }}
                            </div>
                            <a href="javascript:;" wire:click="showPriceBreakdown"
                                class="view-breakup"><small>View Breakdown</small></a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Payment Method</label>
                        </div>
                        <div class="col-sm-8">
                            <div class="method-selection item-selection-div">
                                <div wire:click="setPaymentMethod('cash')" class="{{ $paymentMethod == 'cash' ? 'active' : '' }}"><i class="far fa-money-bill-alt me-1"></i> By Cash {!! $paymentMethod == 'cash' ? '<i class="fas fa-check-circle text-success ms-2"></i>' : '' !!}</div>
                                <div wire:click="setPaymentMethod('card')" class="{{ $paymentMethod == 'card' ? 'active' : '' }}"><i class="far fa-credit-card me-1"></i> By Card {!! $paymentMethod == 'card' ? '<i class="fas fa-check-circle text-success ms-2"></i>' : '' !!}</div>
                            </div>

                            <small class="mt-4" wire:loading wire:target="setPaymentMethod('card')">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Fetching Terminals
                            </small>

                            @if($paymentMethod == 'card')
                            <div class="payment-process-div mt-5">
                                <label class="mb-2">Select Terminal</label>
                                <div class="item-selection-div">
                                    @foreach($terminals as $terminal)
                                        <div class="{{ $selectedTerminal == $terminal['id'] ? 'active' : '' }} {{ !$terminal['available'] ? 'disabled' : '' }}" {!! $terminal['available'] ? 'wire:click="setTerminal(\'' . $terminal['id'] .'\') ' : '' !!}">
                                            <i class="fas fa-cash-register"></i> {{ $terminal['title'] }}
                                            <small wire:loading wire:target="setTerminal('{{ $terminal['id'] }}')">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            </small>

                                            @if(!$terminal['available'])
                                            <small class="error-alert ">(Not Available)</small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 offset-md-3">
                            <button wire:click="proceedToOrder" class="btn btn-primary mt-5 w-100 btn-lg {{ !$this->isOrderReady ? 'disabled' : '' }}">
                                <label wire:loading wire:target="proceedToOrder">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span> Processing
                                </label>
                                <label wire:loading.remove wire:target="proceedToOrder"><i class="fas fa-shopping-basket me-2"></i> Place Order</label>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>