<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Order Info</x-slot>

        <x-slot:description>View order details</x-slot>

        <x-slot:content>


            <div class="row px-2">
                <div class="col-sm-4">
                    <div class="card border-light shadow-sm mb-4">
                        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                            @if ($order->qty_ord > $order->qty_ship)
                                <span class="badge bg-light-warning float-end">BACKORDER</span>
                            @endif
                            @if ($order->is_sro)
                                <span class="badge bg-light-info float-end">SRO</span>
                            @endif
                            <h3 class="h5 mb-0">Order Overview</h3>
                        </div>

                        <div class="card-body">

                            @unless (config('sx.mock'))
                                <div class="alert alert-light-secondary color-secondary">
                                    Order is in stage code : <strong> {{ $this->sx_order->getStageCode() }} </strong>
                                </div>

                                <ul class="list-group list-group-flush">
                                    <li
                                        class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                        <div>
                                            <h3 class="h6 mb-1">Order Number</h3>
                                            <p class="small pe-4">
                                                {{ $this->sx_order->orderno . '-' . $this->sx_order->ordersuf ?? '-' }}</p>
                                        </div>
                                        <div>
                                    </li>

                                    <li
                                        class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                        <div>
                                            <h3 class="h6 mb-1">Warehouse</h3>
                                            <p class="small pe-4">{{ strtoupper($this->sx_order->whse) }}</p>
                                        </div>
                                        <div>
                                    </li>


                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">Order Date</h3>
                                            <p class="small pe-4">{{ date('F j, Y', strtotime($this->sx_order->enterdt)) }}
                                            </p>
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">Order Total</h3>
                                            <p class="small pe-4">${{ number_format($this->sx_order->totordamt, 2) }}</p>
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">
                                                {{ strtoupper($this->sx_order->getShippingStage($this->sx_order->stagecd)) }}
                                            </h3>
                                            <p class="small pe-4">
                                                {{ intval($this->sx_order->totqtyshp) + intval($this->sx_order->totqtyret) }}
                                            </p>
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">TRANS Type</h3>
                                            <p class="small pe-4">{{ strtoupper($this->sx_order->transtype) }}</p>
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">TAKEN BY</h3>
                                            <p class="small pe-4">{{ strtoupper($this->sx_order->takenby) }}</p>
                                        </div>
                                    </li>

                                    @if (!empty($this->sx_order->shipinstr))
                                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                            <div>
                                                <h3 class="h6 mb-1">Ship Instruction</h3>
                                                <p class="small pe-4">{{ strtoupper($this->sx_order->shipinstr) }}</p>
                                            </div>
                                        </li>
                                    @endif

                                    @if (!empty($this->sx_order->refer))
                                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                            <div>
                                                <h3 class="h6 mb-1">Reference</h3>
                                                <p class="small pe-4">{{ strtoupper($this->sx_order->refer) }}</p>
                                            </div>
                                        </li>
                                    @endif

                                    @if (!empty($this->sx_order->user8))
                                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                            <div>
                                                <h3 class="h6 mb-1">Delivery Date</h3>
                                                <p class="small pe-4">{{ strtoupper($this->sx_order->user8) }}</p>
                                            </div>
                                        </li>
                                    @endif

                                    @if (!empty($this->sx_order->user4))
                                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                            <div>
                                                <h3 class="h6 mb-1">Status Comm</h3>
                                                <p class="small pe-4">{{ strtoupper($this->sx_order->user4) }}</p>
                                            </div>
                                        </li>
                                    @endif

                                </ul>
                            @endunless
                        </div>
                    </div>


                    @if (!empty($this->shipping))
                        <div class="card border-light shadow-sm mb-4">
                            <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                                <h3 class="h5 mb-0">Shipping Information</h3>
                            </div>

                            <div class="card-body">

                                @unless (config('sx.mock'))
                                    <ul class="list-group list-group-flush">


                                        <li
                                            class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                            <div>
                                                <h3 class="h6 mb-1">Tracking Number</h3>
                                                <p class="small pe-4">{{ $this->shipping->trackerno }}</p>
                                            </div>
                                            <div>
                                        </li>

                                        <li
                                            class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                            <div>
                                                <h3 class="h6 mb-1">Carrier</h3>
                                                <p class="small pe-4">{{ $this->shipping?->getCarrier() }}</p>
                                            </div>
                                            <div>
                                        </li>


                                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                            <div>
                                                <h3 class="h6 mb-1">Freight Amount</h3>
                                                <p class="small pe-4">${{ $this->shipping->freightamt }}</p>
                                            </div>
                                        </li>

                                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                            <div>
                                                <h3 class="h6 mb-1">Freight Weight</h3>
                                                <p class="small pe-4">
                                                    {{ $this->shipping->actweight }} lbs
                                                </p>
                                            </div>
                                        </li>
                                    </ul>
                                @endunless
                            </div>
                        </div>
                    @endif

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
                                            <p class="small pe-4">{{ $this->customer->custno }}</p>
                                        </div>
                                        <div>
                                    </li>

                                    <li
                                        class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                        <div>
                                            <h3 class="h6 mb-1">Name</h3>
                                            <p class="small pe-4">{{ strtoupper($this->customer->name) }}</p>
                                        </div>
                                        <div>
                                    </li>


                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">Phone</h3>
                                            <p class="small pe-4">{{ $this->customer->phoneno }}</p>
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">Email</h3>
                                            <p class="small pe-4">{{ $this->customer->email ?? $this->sx_order->user13 }}
                                            </p>
                                        </div>
                                    </li>


                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">Address</h3>
                                            <p class="small pe-4">
                                                {{ $this->customer->addr . ', ' . $this->customer->city . ', ' . $this->customer->state . ', ' . $this->customer->zipcd }}
                                            </p>
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                        <div>
                                            <h3 class="h6 mb-1">Type
                                            </h3>
                                            <p class="small pe-4">
                                                {{ $this->customer->custtype }}</p>
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
                                        class="fas fa-info-circle"></i> {!! $this->statusAlertMessage !!}

                                    @if ($order->status->value != \App\Enums\Order\OrderStatus::Cancelled->value)
                                        <div class="btn-group float-end" role="group">
                                            @can('order.manage')
                                                <button id="btnGroupDrop1" type="button"
                                                    class="btn btn-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <div wire:loading wire:target="toggleOrderStatus">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                    </div>

                                                    Review
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                    @if ($order->status->value != \App\Enums\Order\OrderStatus::Ignore->value)
                                                        <li><a class="dropdown-item" href="javascript:;"
                                                                wire:click="toggleOrderStatus('{{ \App\Enums\Order\OrderStatus::Ignore->value }}')">Ignore</a>
                                                        </li>
                                                    @endif

                                                    @if ($order->status->value != \App\Enums\Order\OrderStatus::PendingReview->value)
                                                        <li><a class="dropdown-item" href="javascript:;"
                                                                wire:click="toggleOrderStatus('{{ \App\Enums\Order\OrderStatus::PendingReview->value }}')">Pending
                                                                Review</a></li>
                                                    @endif

                                                    <li><a class="dropdown-item" href="javascript:;"
                                                            wire:click="toggleOrderStatus('{{ \App\Enums\Order\OrderStatus::FollowUp->value }}')">Notify
                                                            Customer</a>
                                                    </li>



                                                    <li><a class="dropdown-item" href="javascript:;"
                                                            wire:click="toggleOrderStatus('{{ \App\Enums\Order\OrderStatus::ShipmentFollowUp->value }}')">Email
                                                            Shipping Dept</a>
                                                    </li>

                                                    <li><a class="dropdown-item" href="javascript:;"
                                                            wire:click="toggleOrderStatus('{{ \App\Enums\Order\OrderStatus::ReceivingFollowUp->value }}')">Email
                                                            Receiving</a>
                                                    </li>

                                                    @if ($order->status->value != \App\Enums\Order\OrderStatus::Cancelled->value)
                                                        <li><a class="dropdown-item" href="javascript:;"
                                                                wire:click="toggleOrderStatus('{{ \App\Enums\Order\OrderStatus::Cancelled->value }}')"><span
                                                                    class="text-danger">Cancel
                                                                    and Notify Customer</span></a></li>
                                                    @endif



                                                </ul>
                                            @endcan

                                        </div>
                                    @endif
                                </div>

                                @unless (config('sx.mock'))
                                    <div class="list-group">
                                        @if (!empty($this->sx_order_line_items))
                                            @forelse ($this->sx_order_line_items as $item)
                                                @php
                                                    $backorder_count =
                                                        intval($item->stkqtyord) - intval($item->stkqtyship);
                                                @endphp

                                                @if (strtolower($item->specnstype) != 'l')
                                                    <a class="list-group-item list-group-item-action @if (in_array($item->shipprod, Illuminate\Support\Arr::pluck($this->dnr_line_items, 'shipprod'))) list-group-item-danger @elseif (in_array($item->shipprod, $order->dnr_items ?? [])) border border-warning border-4 @endif"
                                                        aria-current="true">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <h6 class="mb-1">#{{ $item->lineno }} :
                                                                {{ $item->user3 }} {{ $item->shipprod }}
                                                                {{ $item->cleanDescription() }}
                                                                @if ($backorder_count > 0)
                                                                    <span class="px-1 bg-danger text-white">
                                                                        <strong>{{ $backorder_count }}</strong> item(s)
                                                                        backordered</span>

                                                                    @if (str_contains(json_encode($order->wt_transfers), $item->shipprod))
                                                                        <span class="px-1 bg-primary text-white">
                                                                            WT In-Process {{ $item->ordertype }}
                                                                        </span>
                                                                    @else
                                                                        @if (!in_array($item->ordertype, ['p', 't']))
                                                                            <span
                                                                                wire:click='showWTModal({{ $item }})'
                                                                                class="px-1 bg-secondary text-white">
                                                                                Click for
                                                                                WT
                                                                                <div wire:loading
                                                                                    wire:target='showWTModal({{ $item }})'>
                                                                                    <span
                                                                                        class="spinner-border spinner-border-sm"
                                                                                        role="status"
                                                                                        aria-hidden="true"></span>
                                                                                </div>
                                                                            </span>
                                                                        @endif
                                                                    @endif

                                                                    @if (in_array($item->shipprod, $order->non_stock_line_items ?? []))
                                                                        <span class="px-1 ms-1 bg-warning text-white">
                                                                            Non Stock Item
                                                                        </span>
                                                                    @endif

                                                                    @if (in_array($item->shipprod, $order->golf_parts ?? []))
                                                                        <span class="px-1 ms-1 bg-info text-white">
                                                                            Golf
                                                                        </span>
                                                                    @endif
                                                                @endif
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
                                                        @if ($item->getTied() != 'N/A')
                                                            <small>
                                                                <span class="badge bg-light-warning">Related Order#
                                                                    :
                                                                    {{ strtoupper($item->orderaltno) ?: 'N/A' }}</span>
                                                            </small>
                                                        @endif

                                                        @if (!empty($item->user8))
                                                            <small>
                                                                <span class="badge bg-light-info">Exp Date :
                                                                    {{ date('M j, Y', strtotime($item->user8)) ?: 'N/A' }}</span>
                                                            </small>
                                                        @endif

                                                    </a>
                                                @endif

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

                    <div class="px-2">
                        <livewire:x-comments :entity="$order" :key="'comments' . time()" :alert="$this->comment_alert" lazy />
                    </div>

                    <livewire:x-activity-log show-only-user-activity :entity="$order" :key="'activity-' . time()" lazy />

                    @include('livewire.order.partials.sx-notes')



                </div>
            </div>

            @include('livewire.order.partials.notification-modal')
            @include('livewire.order.partials.wt-modal')

        </x-slot>

    </x-page>
    @script
        <script>
            clipboard = new ClipboardJS('.clipboard');

            clipboard.on('success', function(e) {
                console.info('Action:', e.action);
                console.info('Text:', e.text);
                console.info('Trigger:', e.trigger);
                Livewire.dispatch('clipboardCopied');

                e.clearSelection();
            });
        </script>
    @endscript

</div>
