<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Order Info</x-slot>

        <x-slot:description>View order details</x-slot>

        <x-slot:content>


            <div class="row px-2">
                <div class="col-sm-4">
                    <div class="card border-light shadow-sm mb-4">
                        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                            <h3 class="h5 mb-0">Order Overview</h3>
                        </div>

                        <div class="card-body">

                            @unless (config('sx.mock'))
                                <ul class="list-group list-group-flush">


                                    <li
                                        class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                        <div>
                                            <h3 class="h6 mb-1">Order Number</h3>
                                            <p class="small pe-4">{{ $order->orderno . '-' . $order->ordersuf ?? '-' }}</p>
                                        </div>
                                        <div>
                                    </li>

                                    <li
                                        class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                        <div>
                                            <h3 class="h6 mb-1">Warehouse</h3>
                                            <p class="small pe-4">{{ strtoupper($order->whse) }}</p>
                                        </div>
                                        <div>
                                    </li>


                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">Order Date</h3>
                                            <p class="small pe-4">{{ date('F j, Y', strtotime($order->enterdt)) }}</p>
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">Order Total</h3>
                                            <p class="small pe-4">${{ number_format($order->totordamt, 2) }}</p>
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">{{ strtoupper($order->getShippingStage($order->stagecd)) }}
                                            </h3>
                                            <p class="small pe-4">
                                                {{ intval($order->totqtyshp) + intval($order->totqtyret) }}</p>
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">TRANS Type</h3>
                                            <p class="small pe-4">{{ strtoupper($order->transtype) }}</p>
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">TAKEN BY</h3>
                                            <p class="small pe-4">{{ strtoupper($order->takenby) }}</p>
                                        </div>
                                    </li>





                                </ul>
                            @endunless
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <h4 class="card-title">Line Items</h4>
                                <div class="alert alert-light-{{ $this->statusAlertClass }} color-primary"><i
                                        class="fas fa-info-circle"></i> {{ $this->statusAlertMessage }}
                                    <div class="btn-group float-end" role="group">
                                        <button id="btnGroupDrop1" type="button"
                                            class="btn btn-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            Review
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                            @if($backorder->status->value != \App\Enums\Order\BackOrderStatus::Ignore->value)
                                            <li><a class="dropdown-item" href="javascript:;" wire:click="toggleOrderStatus('{{ \App\Enums\Order\BackOrderStatus::Ignore->value }}')">Ignore</a></li>
                                            @endif

                                            @if($backorder->status->value != \App\Enums\Order\BackOrderStatus::PendingReview->value)
                                            <li><a class="dropdown-item" href="javascript:;" wire:click="toggleOrderStatus('{{ \App\Enums\Order\BackOrderStatus::PendingReview->value }}')">Pending Review</a></li>
                                            @endif

                                            @if($backorder->status->value != \App\Enums\Order\BackOrderStatus::Cancelled->value)
                                            <li><a class="dropdown-item" href="javascript:;" wire:click="toggleOrderStatus('{{ \App\Enums\Order\BackOrderStatus::Cancelled->value }}')">Cancel and Notify Customer</a></li>
                                            @endif

                                            <li><a class="dropdown-item" href="javascript:;">Add Internal Note</a></li>
                                        </ul>
                                    </div>
                                </div>

                                @unless (config('sx.mock'))
                                    <div class="list-group">
                                        @if (!empty($this->order_line_items))
                                            @forelse ($this->order_line_items as $item)
                                                @php
                                                    $is_dnr = false;

                                                    if (strtoupper($item->statustype) == 'A') {
                                                        $available = $item->qtyship + $item->qtyrel;
                                                        if ($item->qtyord != $available) {
                                                            $dnr_warehouse_product = App\Models\SX\WarehouseProduct::where('cono', 10)
                                                                ->where('whse', $item->whse)
                                                                ->where('prod', $item->shipprod)
                                                                ->where('statustype', 'X')
                                                                ->get();

                                                            if ($dnr_warehouse_product->isNotEmpty()) {
                                                                $is_dnr = true;
                                                            }
                                                        }
                                                    }

                                                @endphp
                                                <a href="https://weingartz.com//searchPage.action?keyWord={{ $item->shipprod }}"
                                                    target="_blank"
                                                    class="list-group-item list-group-item-action @if ($is_dnr) list-group-item-danger @endif"
                                                    aria-current="true">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1">Line #{{ $item->lineno }} :
                                                            {{ $item->user3 }} {{ $item->shipprod }}
                                                            {{ rtrim(
                                                                str_replace(
                                                                    ';',
                                                                    ",
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            ",
                                                                    $item->descrip,
                                                                ),
                                                                ', ',
                                                            ) }}
                                                        </h6>
                                                        <small><span class="badge bg-light-success">Category
                                                                :
                                                                {{ strtoupper($item->prodcat) }} / Prod Line
                                                                :
                                                                {{ strtoupper($item->prodline) }}</span>
                                                        </small>
                                                    </div>
                                                    <small>
                                                        <span class="badge bg-light-secondary">Type :
                                                            {{ $item->getSpecType() }}</span>
                                                    </small>
                                                    <small>
                                                        <span class="badge bg-light-primary">Qty Ordered :
                                                            @if ($item->returnfl == '1')
                                                                -
                                                            @endif
                                                            {{ intval($item->qtyord) }}
                                                        </span>
                                                    </small>
                                                    <small>
                                                        <span class="badge bg-light-info">Qty Shipped :
                                                            @if ($item->returnfl == '1')
                                                                -
                                                            @endif
                                                            {{ intval($item->qtyship) }}
                                                        </span>
                                                    </small>

                                                    <small>
                                                        <span class="badge bg-light-warning">Price :
                                                            ${{ number_format($item->price, 2) }}</span>
                                                    </small>
                                                    <small>
                                                        <span class="badge bg-light-primary">Net Amt :
                                                            ${{ number_format($item->netamt, 2) }}</span>
                                                    </small>
                                                    <small>
                                                        <span class="badge bg-light-secondary">Tied :
                                                            {{ $item->getTied() }}</span>
                                                    </small>
                                                    <small>
                                                        <span class="badge bg-light-warning">Related Order#
                                                            :
                                                            {{ strtoupper($item->orderaltno) ?: 'N/A' }}</span>
                                                    </small>

                                                    @if (!empty($item->user8))
                                                        <small>
                                                            <span class="badge bg-light-info">Exp Date :
                                                                {{ date('M j, Y', strtotime($item->user8)) ?: 'N/A' }}</span>
                                                        </small>
                                                    @endif

                                                </a>
                                            @empty
                                                <div class="alert alert-light-warning color-warning">
                                                    No line items on this order
                                                </div>
                                            @endforelse
                                        @endif
                                    </div>
                                @endunless

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row px-2">
                <div class="col-sm-4">
                    <div class="card border-light shadow-sm mb-4">
                        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                            <h3 class="h5 mb-0">Customer Overview</h3>
                        </div>

                        <div class="card-body">

                            @unless (config('sx.mock'))
                                <ul class="list-group list-group-flush">


                                    <li
                                        class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                        <div>
                                            <h3 class="h6 mb-1">Customer SX Number</h3>
                                            <p class="small pe-4">{{ $customer->custno }}</p>
                                        </div>
                                        <div>
                                    </li>

                                    <li
                                        class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                        <div>
                                            <h3 class="h6 mb-1">Name</h3>
                                            <p class="small pe-4">{{ strtoupper($customer->name) }}</p>
                                        </div>
                                        <div>
                                    </li>


                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">Phone</h3>
                                            <p class="small pe-4">{{ $customer->phoneno }}</p>
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">Address</h3>
                                            <p class="small pe-4">
                                                {{ $customer->addr . ', ' . $customer->city . ', ' . $customer->state . ', ' . $customer->zipcd }}
                                            </p>
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">Type
                                            </h3>
                                            <p class="small pe-4">
                                                {{ $customer->custtype }}</p>
                                        </div>
                                    </li>

                                </ul>
                            @endunless
                        </div>
                    </div>

                </div>
            </div>

            <x-modal :toggle="$cancelOrderModal" size="xl">
                <x-slot name="title">Cancel Order</x-slot>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="pre-genkey-div">
                            <x-forms.textarea
                                label="Email Content"
                                model="cancelEmailContent"
                                rows="6"
                                hint="This is to identify your app, eg Mobile App"
                            />
                        </div>
                    </div>
                </div>

                <x-slot name="footer">
                    <button wire:click="cancelOrder" type="button" class="pre-genkey-div btn btn-success">Cancel & Notify</button>
                    <button wire:click="closePopup('cancelOrderModal')" type="button" class="btn btn-outline-secondary">Close</button>
                </x-slot>

            </x-modal>
        </x-slot>

    </x-page>
</div>
