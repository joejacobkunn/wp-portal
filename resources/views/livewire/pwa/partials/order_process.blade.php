<div class="pt-3">
    <h4 class="mt-4 my-2">Payment Information</h4>
    <div class="row mt-4 my-2">
        <div class="col-sm-6">
            <label>Order ID</label>
            <div>#{{ $selectedOrder['orderno'] }}</div>
        </div>
        <div class="col-sm-6">
            <label>Customer ID</label>
            <div>#{{ $selectedOrder['custno'] }}</div>
        </div>
    </div>

    <hr />

    <div class="row mb-2">
        <div class="col-sm-12">
            <x-forms.input
                label="Name"
                model="selectedOrder.name"
            />
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-sm-6">
            <x-forms.input
                label="Email"
                model="selectedOrder.email"
            />
        </div>
        <div class="col-sm-6">
            <x-forms.input
                label="Phone"
                model="selectedOrder.phoneno"
            />
        </div>
    </div>

    <hr />
    <h6 class="mb-3 mt-3">Shipping Information</h6>

    <div class="row mb-2">
        <div class="col-sm-12">
            <x-forms.input
                label="Name"
                model="selectedOrder.shiptonm"
            />
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-sm-6">
            <x-forms.input
                label="Address 1"
                model="selectedOrder.address"
            />
        </div>
        <div class="col-sm-6">
            <x-forms.input
                label="Address 2"
                model="selectedOrder.address2"
            />
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-sm-6">
            <x-forms.input
                label="City"
                model="selectedOrder.shiptocity"
            />
        </div>
        <div class="col-sm-3">
            <x-forms.input
                label="State"
                model="selectedOrder.shiptost"
            />
        </div>
        <div class="col-sm-3">
            <x-forms.input
                label="Zipcode"
                model="selectedOrder.shiptozip"
            />
        </div>
    </div>

    <hr />

    <div class="row mb-2">
        <div class="col-sm-12">
            <div class="mt-3 h5 d-inline-block px-3 py-2">
                Total Amount: <span class="d-inline-block ms-2">${{ number_format(!empty($selectedOrder) ? $selectedOrder['totinvamt'] : 0) }}</span>
            </div>
        </div>
        <div class="col-sm-12 text-center">
           <button class="btn btn-success btn-lg" wire:click="processTransaction">Initiate Transacion</button>
        </div>
    </div>
</div>