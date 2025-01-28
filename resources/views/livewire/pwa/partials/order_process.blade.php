<div class="pt-3 mt-4">
    <h4 class="mt-4 my-2">Order #{{ preg_replace('/(\d+)(\d{2})$/', '$1-$2', $selectedOrder['orderno']) }}</h4>

    <hr />

    <div class="row mb-2">
        <div class="col-sm-12">
            <x-forms.input label="Name" model="selectedOrder.name" :hint="'SX CustNo #' . $selectedOrder['custno']" />
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-sm-6">
            <x-forms.input prependIcon="fa-solid fa-at" label="Email" model="selectedOrder.email" />
        </div>
        <div class="col-sm-6">
            <x-forms.input prependIcon="fa-solid fa-phone" label="Phone" model="selectedOrder.phoneno" />
        </div>
    </div>

    <hr />
    <h6 class="mb-3 mt-3">Shipping Information</h6>

    <div class="row mb-3">
        <div class="col-sm-12">
            <x-forms.input label="Name" model="selectedOrder.shiptonm" />
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-6">
            <x-forms.input label="Address 1" model="selectedOrder.address" />
        </div>
        <div class="col-sm-6">
            <x-forms.input label="Address 2" model="selectedOrder.address2" />
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-6">
            <x-forms.input label="City" model="selectedOrder.shiptocity" />
        </div>
        <div class="col-sm-3">
            <x-forms.input label="State" model="selectedOrder.shiptost" />
        </div>
        <div class="col-sm-3">
            <x-forms.input label="ZIP Code" model="selectedOrder.shiptozip" />
        </div>
    </div>

    <hr />

    <div class="row mb-4 mt-3">
        <div class="col-sm-12">
            <div class="card payment-outer-div">
                <div class="card-body mb-4">
                    <div class="row mb-4">
                        <div class="col-sm-3">
                            <label>Total Amount</label>
                        </div>
                        <div class="col-sm-9">
                            <div class="price-value-div">
                                ${{ number_format(!empty($selectedOrder) ? $selectedOrder['totinvamt'] : 0, 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-3">
                            <label>Terminal</label>
                        </div>
                        <div class="col-sm-9">
                            <div class="payment-process-div">
                                <div class="item-selection-div">
                                    @forelse($terminals as $terminal)
                                        <div class="{{ $selectedTerminal == $terminal['id'] ? 'active' : '' }} {{ !$terminal['available'] ? 'disabled' : '' }}"
                                            {!! $terminal['available'] ? 'wire:click="setTerminal(\'' . $terminal['id'] . '\') ' : '' !!}">
                                            <i class="fas fa-cash-register"></i> {{ $terminal['title'] }}
                                            <small wire:loading wire:target="setTerminal('{{ $terminal['id'] }}')">
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
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-sm-3">

                        </div>
                        <div class="col-sm-9 mt-3">
                            <button type="button" class="btn btn-primary btn-lg mb-4 payment-btn"><i
                                    class="fa-solid fa-cash-register me-1"></i> Initiate Transaction</button>
                            <button type="button" class="btn btn-outline-danger btn-lg mb-4 ms-2" wire:confirm-action
                                data-confirm-type="danger" data-confirm-title="Confirm"
                                data-confirm-content="Are you sure to cancel the transaction?"
                                data-confirm-button="Cancel Transaction" data-cancel-button="Close"
                                wire:click="cancelTransaction()"><i class="fa-solid fa-times me-1"></i> Cancel
                                Transaction</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @script
        <script>
            (function() {
                setTimeout(() => {
                    document.querySelector('.container').addEventListener('click', function(event) {
                        if (event.target.matches('.payment-btn') || event.target.matches(
                                '.payment-btn i')) {
                            event.preventDefault();
                            let target = event.target;
                            if (event.target.matches('.payment-btn i')) {
                                target = event.target.closest('.payment-btn');
                            }

                            target.setAttribute('disabled', true);
                            target.innerHTML =
                                '<i class="fa-solid fa-spinner fa-spin me-1"></i> Initiating Sale';

                            $wire.processTransaction().then((data) => {
                                if (data.status == 'success') {
                                    target.setAttribute('disabled', true);
                                    target.innerHTML =
                                        '<i class="fa-solid fa-spinner fa-spin me-1"></i> Processing Sale';
                                    checkStatus(data.checkSum, data.orderNo, data.transactionCode);
                                } else {
                                    target.removeAttribute('disabled');
                                    target.innerHTML =
                                        '<i class="fa-solid fa-cash-register me-1"></i> Initiate Transaction';
                                }
                            });
                        }
                    })

                    let statusCheckIntr;

                    function checkStatus(checkSum, orderNo, transactionCode) {
                        let target = document.querySelector('.container .payment-btn');
                        let statusCounter = 0;
                        let statusCheckRunning;
                        let statusData;

                        let statusCheckIntr = setInterval(() => {
                            if (!statusCheckRunning) {
                                statusCheckRunning = true;
                                $wire.getTransactionStatus(checkSum, orderNo, transactionCode).then((
                                    statusData) => {
                                    statusCheckRunning = false
                                    target.setAttribute('disabled', true);
                                    target.innerHTML =
                                        '<i class="fa-solid fa-spinner fa-spin me-1"></i> Waiting for Terminal';

                                    statusCounter++;

                                    if (statusCounter == 20 || (statusData && statusData.status !=
                                            'in_process')) {
                                        clearInterval(statusCheckIntr);

                                        target.removeAttribute('disabled');
                                        target.innerHTML =
                                            '<i class="fa-solid fa-cash-register me-1"></i> Initiate Transaction';
                                    }
                                });
                            }

                        }, 3000);
                    }

                    document.addEventListener('browser:transaction-cancelled', function() {
                        clearInterval(statusCheckIntr);
                    });

                }, 150)
            });
        </script>
    @endscript
</div>
