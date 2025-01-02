<div class="accordion accordion-flush {{ $orderStatus == 'completed' ? 'd-none' : '' }}" id="accordionFlushExample">
    <div class="accordion-item">
        <h2 class="accordion-header" id="flush-headingOne">
            <button class="accordion-button {{ $activeTab == 1 ? '' : 'collapsed' }}"
                {{ $activeTab == 1 ? 'disabled' : '' }} type="button" wire:click="selectTab(1)">
                1. <span class="title-span"><i class="fas fa-cart-plus"></i> Cart

                    <label class=" ms-2" wire:loading wire:target="selectTab(1)">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </label>
                </span>
                <span class="badge bg-light-primary">
                    <strong>{{ count($cart) ? count($cart) . ' Item(s) selected' : '' }}</strong>
                </span>
            </button>
        </h2>
        <div id="flush-collapseOne" class="accordion-collapse collapse {{ $activeTab == 1 ? 'show' : '' }}"
            aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">
                <div>
                    <div class="row">
                        <div class="col-sm-12">
                            @if ($loadingCart)
                                <h5 class="my-3"><small><i class="fa fa-spinner fa-spin"></i> Loading selected items
                                        to cart</small></h5>
                            @else
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-7">
                                                <label>Add Product</label>
                                                <div class="input-group mb-3">
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        wire:model="productQuery"
                                                        placeholder="Enter Product Code / Alias"
                                                        wire:keydown.enter="searchProduct"
                                                    >
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary px-4" wire:click="searchProduct"
                                                            type="button">
                                                            <div wire:loading wire:target="searchProduct">
                                                                <span class="spinner-border spinner-border-sm mx-4"
                                                                    role="status" aria-hidden="true"></span>
                                                            </div>
                                                            <div wire:loading.class="d-none"
                                                                wire:target="searchProduct">
                                                                <i class="fas fa-cart-plus"></i> Add
                                                            </div>
                                                        </button>
                                                    </div>
                                                </div>
                                                @if (isset($productQuery))
                                                    @error('productQuery')
                                                        <span class="text-danger d-block mb-3">{{ $message }}</span>
                                                    @enderror
                                                @endif
                                            </div>
                                            <div class="col-sm-5">
                                                <label class="mt-4 float-start pt-2 me-3">- OR -</label>
                                                <button class="btn btn-outline-primary mt-4 search-btn" type="button"
                                                    wire:click="showProductSearchModal">
                                                    <div wire:loading wire:target="showProductSearchModal">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                    </div>

                                                    <div wire:loading.class="d-none"
                                                        wire:target="showProductSearchModal">
                                                        <i class="fas fa-search"></i> Search for Products
                                                    </div>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="btn-group mb-1 mt-3 float-end">
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary dropdown-toggle ms-2"
                                                    type="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="true">
                                                    <i class="fas fa-warehouse me-2"></i> Warehouse:
                                                    <strong>{{ $selectedWareHouse ? $warehouses[$selectedWareHouse] : '- Not Selected -' }}</strong>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                                    data-popper-placement="bottom-start">
                                                    <h6 class="dropdown-header">Select Warehouse</h6>
                                                    @foreach ($warehouses as $warehouseShort => $warehouseName)
                                                        <a class="dropdown-item" href="javascript:;"
                                                                wire:click="selectWareHouse('{{ $warehouseShort }}')">{{ $warehouseName }}</a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @endif
                        </div>
                    </div>

                    @if (count($cart) && !$loadingCart)
                        <hr />
                        <h4 class="mt-4">Total Items selected ({{ count($cart) }})</h4>
                        <table class="table table-hover">
                            <thead>
                                <th>Product</th>
                                <th>Product Line</th>
                                <th>Bin Loc 1</th>
                                <th>Net Avail</th>
                                <th width="10%">Unit Of Measure</th>
                                <th>Price</th>
                                <th class="w-10">Qty.</th>
                            </thead>
                            <tbody>
                                @foreach ($cart as $cartIndex => $item)
                                    <tr>
                                        <td><a href="https://weingartz.com//searchPage.action?keyWord={{ $item['product_code'] }}"
                                                target="_blank">{{ $item['product_name'] }}</a>

                                            @if(!empty($item['supersedes']))
                                            <div class="d-flex">
                                                @foreach($item['supersedes'] as $supersede)
                                                    <span class="badge text-primary media-library-text-link me-1" wire:click="viewSupersede('{{ $supersede }}', '{{ $item['product_code'] }}')">
                                                        <i class="far fa-clone me-1"></i> {{ $supersede }}
                                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" wire:loading wire:target="viewSupersede('{{ $supersede }}', '{{ $item['product_code'] }}')"></span>
                                                    </span>
                                                @endforeach
                                            </div>
                                            @endif
                                        </td>
                                        <td>{{ $item['prodline'] }}</td>
                                        <td>{{ $item['bin_location'] }}</td>
                                        <td>
                                            @if(!in_array($item['brand_name'], $nonQtyProductBrands))
                                                @if ($item['stock'] > 0)
                                                    {{ $item['stock'] }}
                                                @else
                                                    <span class="alert-danger px-2">Backorder</span>
                                                @endif
                                            @else
                                                <span>-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="{{ !empty($item['unit_sell']) && is_array($item['unit_sell']) && count($item['unit_sell']) > 1 ? 'multi-units ' : '' }}" wire:click="showChangeUnitOfMeasure('{{ $cartIndex }}')">{{ $item['unit_of_measure'] }}</span>
                                        </td>
                                        <td><span class="price-update-span {{ !empty($item['price_overridden']) ? 'alert-warning px-2' : '' }}" wire:click="showOverridePriceModal('{{ $cartIndex }}')">${{ number_format($item['price'], 2) }}</span></td>
                                        <td>
                                            @if(!in_array($item['brand_name'], $nonQtyProductBrands))
                                            <div class="quantity-div">
                                                <div class="input-group w-75">
                                                    <div class="input-group-prepend">
                                                        <button
                                                            class="btn btn-{{ $item['quantity'] < 2 ? 'danger' : 'secondary' }}"
                                                            type="button"
                                                            wire:click="updateQuantity(-1, '{{ $item['product_code'] }}')">{!! $item['quantity'] == 1 ? '<i class="fa fa-trash"></i>' : '<i class="fa fa-minus"></i>' !!}</button>
                                                    </div>
                                                    <input type="text" class="form-control text-center"
                                                        value="{{ $item['quantity'] }}" id="quantityInput"
                                                        aria-describedby="basic-addon1">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-secondary" type="button"
                                                            wire:click="updateQuantity(1, '{{ $item['product_code'] }}')"><i
                                                                class="fa fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                                <div class="quantity-div">
                                                    <button
                                                        class="btn btn-{{ $item['quantity'] < 2 ? 'danger' : 'secondary' }}"
                                                        type="button"
                                                        wire:click="updateQuantity(-1, '{{ $item['product_code'] }}')">{!! $item['quantity'] == 1 ? '<i class="fa fa-trash"></i>' : '<i class="fa fa-minus"></i>' !!}</button>
                                                </div>
                                            @endif
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
            <button class="accordion-button {{ $activeTab == 2 ? '' : 'collapsed' }}" type="button"
                {{ count($cart) || $loadingCart ? '' : 'disabled' }} wire:click="selectTab(2)">
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
                        <x-forms.checkbox label="Waive Customer Info" name="waive_customer_info" :value="1"
                            :model="'waiveCustomerInfo'" />
                    </strong>

                    @if (!$waiveCustomerInfo)
                        <hr />

                        @if (empty($customerSelected))
                            <div class="row">
                                <div class="col-sm-4">
                                    <x-forms.input
                                        label="Search Customer"
                                        model="customerQuery"
                                        placeholder="Search By Name / SX # / Address / Phone # / Email"
                                        defer 
                                        enterAction="searchCustomer"
                                        />
                                </div>
                                <div class="col-sm-8">
                                    <button class="btn btn-primary mt-4 search-btn" type="button"
                                        wire:click="searchCustomer">
                                        <div wire:loading wire:target="searchCustomer">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                        </div>

                                        <div wire:loading.class="d-none" wire:target="searchCustomer">
                                            <i class="fa fa-search"></i> Search
                                        </div>
                                    </button>

                                    <button class="btn btn-outline-success ms-2 mt-4" type="button"
                                        wire:click="newCustomer">
                                        <div wire:loading wire:target="newCustomer">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                        </div>

                                        <div wire:loading.class="d-none" wire:target="newCustomer">
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

                    <hr/>

                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <h6>Preferred Contact Method</h6>
                                <div class="input-group mb-3">
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">{{ $selectedContactMethod }}</button>
                                    <ul class="dropdown-menu" style="">
                                        <li><a class="dropdown-item" href="#" wire:click="updateContactMethod('SMS')">SMS</a></li>
                                        <li><a class="dropdown-item" href="#" wire:click="updateContactMethod('Call')">Call</a></li>
                                        <li><a class="dropdown-item" href="#" wire:click="updateContactMethod('Email')">Email</a></li>
                                    </ul>
                                    <input type="text" class="form-control" wire:model="contactMethodValue">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <x-forms.radio-group
                            label="Delivery Method"
                            name="lender_required"
                            :items="[ 'Customer Taking With', 'Customer Pick Up Later', 'Shipping']"
                            model="deliveryMethod"
                        />
                    </div>

                    @if($deliveryMethod == 'Shipping')
                    <div class="row">
                        <div class="col-sm-3 offset-sm-6">
                            <x-forms.select
                                model="shippingOptionSelected"
                                :options="$shippingOptions"
                                :selected="$shippingOptionSelected ?? null"
                            />    
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header" id="flush-headingThree">
            <button class="accordion-button {{ $activeTab == 3 ? '' : 'collapsed' }}" type="button"
                {{ ! $this->paymentTabActivated ? 'disabled' : '' }}
                wire:click="selectTab(3)">
                3. <span class="title-span"><i class="far fa-credit-card"></i>
                    Payment

                    <label class=" ms-2" wire:loading wire:target="selectTab(3)">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </label>
                </span>
                <span class="badge bg-light-secondary">Total:
                    {{ format_money($netPrice) }}
                </span>
            </button>
        </h2>
        <div id="flush-collapseThree" class="accordion-collapse collapse  {{ $activeTab == 3 ? 'show' : '' }}"
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
                            <a href="javascript:;" wire:click="showPriceBreakdown" class="view-breakup"><small>View
                                    Breakdown</small></a>

                            <div class="row mt-3">
                                <div class="col-sm-6">
                                    <div class="form-group mt-2">
                                        <label><i class="fas fa-ticket"></i> Have Coupon Code?</label>
                                        <div class="mt-2">
                                            @if(!empty($couponProduct))
                                                <div class="row mt-4">
                                                    <div class="col-sm-3 pe-0">
                                                        <p>Applied Coupon</p>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <span class="bg-primary text-white px-3 ms-2"><i class="fas fa-tag me-1"></i> {{ $couponProduct->prod }}</span> <i class="fa fa-times text-danger ms-2 text-link" title="Remove Coupon" wire:click="clearCoupon"></i>
                                                        <div><small class="ms-2">{{ $couponProduct->description }}</small></div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <x-forms.input
                                                            no-label
                                                            model="couponCode"
                                                            autocomplete-off
                                                        />
                                                    </div>
                                                    <div class="col-sm-8 ps-0">
                                                        <button class="btn btn-secondary" wire:click="applyCoupon">
                                                            <span wire:loading wire:target="applyCoupon"
                                                                class="spinner-border spinner-border-sm"
                                                                role="status"
                                                                aria-hidden="true"></span> Apply
                                                        </button>

                                                        <button class="btn btn-outline-primary search-btn ms-2" type="button"
                                                            wire:click="showCouponSearchModal">
                                                            <div wire:loading wire:target="showCouponSearchModal">
                                                                <span class="spinner-border spinner-border-sm" role="status"
                                                                    aria-hidden="true"></span>
                                                            </div>

                                                            <div wire:loading.class="d-none"
                                                                wire:target="showCouponSearchModal">
                                                                <i class="fas fa-search"></i> Search for Coupons
                                                            </div>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr/>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Payment Method</label>
                        </div>
                        <div class="col-sm-8">
                            <div class="method-selection item-selection-div">
                                <div wire:click="setPaymentMethod('cash')"
                                    class="{{ $paymentMethod == 'cash' ? 'active' : '' }}">
                                    <i class="far fa-money-bill-alt me-1"></i> Cash {!! $paymentMethod == 'cash' ? '<i class="fas fa-check-circle text-success ms-2"></i>' : '' !!}
                                </div>
                                <div 
                                    wire:click="setPaymentMethod('card')"
                                    class="{{ $paymentMethod == 'card' ? 'active' : '' }}">
                                    <i class="far fa-credit-card me-1"></i> Card {!! $paymentMethod == 'card' ? '<i class="fas fa-check-circle text-success ms-2"></i>' : '' !!}
                                </div>
                                <div 
                                    wire:click="setPaymentMethod('house_account')"
                                    class="{{ $paymentMethod == 'house_account' ? 'active' : '' }}">
                                    <i class="fas fa-house-user me-1"></i> House Account {!! $paymentMethod == 'house_account' ? '<i class="fas fa-check-circle text-success ms-2"></i>' : '' !!}
                                </div>
                                <div 
                                    wire:click="setPaymentMethod('check')"
                                    class="{{ $paymentMethod == 'check' ? 'active' : '' }}">
                                    <i class="fas fa-money-check-alt me-1"></i> Check {!! $paymentMethod == 'check' ? '<i class="fas fa-check-circle text-success ms-2"></i>' : '' !!}
                                </div>
                            </div>

                            <small class="mt-4" wire:loading wire:target="setPaymentMethod('card')">
                                <span class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span> Fetching Terminals
                            </small>

                            @if ($paymentMethod == 'card')
                                <div class="payment-process-div mt-5">
                                    <label class="mb-2">Select Terminal</label>
                                    <div class="item-selection-div">
                                        @forelse($terminals as $terminal)
                                            <div class="{{ $selectedTerminal == $terminal['id'] ? 'active' : '' }} {{ !$terminal['available'] ? 'disabled' : '' }}"
                                                {!! $terminal['available'] ? 'wire:click="setTerminal(\'' . $terminal['id'] . '\') ' : '' !!}">
                                                <i class="fas fa-cash-register"></i> {{ $terminal['title'] }}
                                                <small wire:loading
                                                    wire:target="setTerminal('{{ $terminal['id'] }}')">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                </small>

                                                @if (!$terminal['available'])
                                                    <small class="error-alert ">(Not Available)</small>
                                                @endif
                                            </div>
                                        @empty
                                            <div class="alert alert-warning" role="alert">
                                                No terminals found. Make sure its active or check if locations are
                                                configured by your administrator
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @elseif ($paymentMethod == 'cash')
                            <hr/>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group mt-2">
                                        <label><i class="fas fa-calculator"></i> Change Helper</label>
                                        <div class="mt-2">
                                            <x-forms.input
                                                no-label
                                                type="number"
                                                model="collectedAmount"
                                                prependText="$"
                                                live
                                                autocomplete-off
                                            />

                                            @if($collectedAmount)
                                            <label>{!! ($returnAmount > 0 ? 'Return Amount <span class="alert-success px-2">' . format_money(abs($returnAmount)) . '</span>'  : 'Additional Amount Required <span class="alert-danger px-2">'. format_money(abs($returnAmount)) . '</span>')  !!}</label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @elseif ($paymentMethod == 'check')
                            <div class="row mt-3">
                                <div class="col-sm-4">
                                    <x-forms.input
                                        label="Check Number"
                                        model="checkNumber"
                                    />
                                </div>
                            </div>
                            @endif

                            <hr/>

                            <div class="mt-3">
                                <x-forms.textarea label="Notes"
                                    model=""
                                />
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 offset-md-3">
                            <button wire:click="proceedToOrder"
                                class="btn btn-primary mt-5 w-100 btn-lg {{ !$this->isOrderReady ? 'disabled' : '' }}">
                                <label wire:loading wire:target="proceedToOrder">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span> Processing
                                </label>
                                <label wire:loading.remove wire:target="proceedToOrder"><i
                                        class="fas fa-shopping-basket me-2"></i> Place Order</label>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-modal :toggle="$priceUpdateModal" closeEvent="closePriceUpdateModal">
        <x-slot name="title">
            <div class="">Update Product Price</div>
        </x-slot>
        
        <div>
            <div>
                <h3 class="h6 mb-1">Product Code</h3>
                <p class="small pe-4">{{ $priceUpdateData['product_code'] }}</p>

                <h3 class="h6 mb-1">Current Price</h3>
                <p class="small pe-4">${{ number_format($priceUpdateData['current_price'], 2) }}</p>
            </div>

            <hr/>

            <x-forms.input
                label="New Price"
                model="priceUpdateData.value"
                prependText="$"
            />

            <x-forms.textarea
                label="Reason for price update"
                model="priceUpdateData.reason"
                prependText="$"
            />
        </div>

        <x-slot name="footer">
            <button wire:click="confirmOverridePrice()" type="button" class="btn btn-success">
                <span wire:loading wire:target="confirmOverridePrice"
                    class="spinner-border spinner-border-sm"
                    role="status"
                    aria-hidden="true"></span> Update
            </button>
        </x-slot>

    </x-modal>

    <x-modal :toggle="$measureUpdateModal" closeEvent="closeMeasureUpdateModal">
        <x-slot name="title">
            <div class="">Update Unit Of Measure</div>
        </x-slot>
        
        <div>
            <div>
                <h3 class="h6 mb-1">Product Code</h3>
                <p class="small pe-4">{{ $measureUpdateData['product_code'] }}</p>

            </div>

            <x-forms.select
                label="Select Measure"
                :model="$measureUpdateData['index']"
                :options="$measureUpdateData['options']"
                :selected="$measureUpdateData['value'] ?? null"
                default-option="false"
                listener="unit_of_measure:updated"
                key="select-{{ $measureUpdateData['index'] . $measureUpdateData['value'] }}"
            />
        </div>

        <x-slot name="footer">
            <button wire:click="confirmMeasureUpdate()" type="button" class="btn btn-success">
                <span wire:loading wire:target="confirmMeasureUpdate"
                    class="spinner-border spinner-border-sm"
                    role="status"
                    aria-hidden="true"></span> Update
            </button>
        </x-slot>

    </x-modal>

    <x-modal :toggle="$supersedeModal" closeEvent="closeSupersedeModal">
        <x-slot name="title">
            <div class="">View Supersede</div>
        </x-slot>
        
        @if(!empty($supersedeData))
        <div>
            <table class="table">
                <tr>
                    <td>Product Code</td>
                    <td>{{ $supersedeData['product_code'] }}</td>
                </tr>
                <tr>
                    <td>Product Line</td>
                    <td>{{ $supersedeData['prodline'] }}</td>
                </tr>
                <tr>
                    <td>Bin Location</td>
                    <td>{{ $supersedeData['bin_location'] }}</td>
                </tr>
                <tr>
                    <td>Net Availability</td>
                    <td>{{ $supersedeData['stock'] }}</td>
                </tr>
                <tr>
                    <td>Price</td>
                    <td>${{ number_format($supersedeData['price'], 2) }}</td>
                </tr>
            </table>

            @if(!empty($supersedeData['stock_error']))
            <div class="alert alert-warning">
                <p class="mb-0">Not enough stock for the selected quantity!</p>
            </div>
            @endif
        </div>
        @endif

        <x-slot name="footer">
            <div class="m-auto">
                @if(!empty($supersedeData))
                <button type="button"
                    class="btn btn-primary mb-3 px-4 py-2 mt-1"
                    wire:click="substituteSupersede('{{ $supersedeData['product_code'] }}')"
                    {{ !empty($supersedeData['stock_error']) ? 'disabled' : '' }}>
                    <i class="fas fa-sync-alt me-2" wire:loading.class="fa-spin"></i> Substitute supersede
                </button>
                @endif
            </div>
        </x-slot>

    </x-modal>

</div>
