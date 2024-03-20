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





                                </ul>
                            @endunless
                        </div>
                    </div>

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


                                                    @if ($order->status->value != \App\Enums\Order\OrderStatus::Cancelled->value)
                                                        <li><a class="dropdown-item" href="javascript:;"
                                                                wire:click="toggleOrderStatus('{{ \App\Enums\Order\OrderStatus::Cancelled->value }}')">Cancel
                                                                and Notify Customer</a></li>
                                                    @endif

                                                    <li><a class="dropdown-item" href="javascript:;"
                                                            wire:click="toggleOrderStatus('{{ \App\Enums\Order\OrderStatus::ShipmentFollowUp->value }}')">Email
                                                            Shipping Dept</a>
                                                    </li>

                                                    <li><a class="dropdown-item" href="javascript:;"
                                                            wire:click="toggleOrderStatus('{{ \App\Enums\Order\OrderStatus::ReceivingFollowUp->value }}')">Email
                                                            Receiving</a>
                                                    </li>


                                                </ul>
                                            @endcan

                                        </div>
                                    @endif
                                </div>

                                @unless (config('sx.mock'))
                                    <div class="list-group">
                                        @if (!empty($this->sx_order_line_items))
                                            @forelse ($this->sx_order_line_items as $item)
                                                <a href="https://weingartz.com//searchPage.action?keyWord={{ $item->shipprod }}"
                                                    target="_blank"
                                                    class="list-group-item list-group-item-action @if (in_array($item->shipprod, Illuminate\Support\Arr::pluck($this->dnr_line_items, 'shipprod'))) list-group-item-danger @elseif (in_array($item->shipprod, $order->dnr_items ?? [])) border border-warning border-4 @endif"
                                                    aria-current="true">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1">#{{ $item->lineno }} :
                                                            {{ $item->user3 }} {{ $item->shipprod }}
                                                            {{ $item->cleanDescription() }}
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
                        <livewire:x-comments :entity="$order" :key="'comments' . time()" :alert="$this->comment_alert" />
                    </div>

                    <livewire:x-activity-log :entity="$order" :key="'activity-' . time()" />



                </div>
            </div>


            <x-modal :toggle="$cancelOrderModal" size="xl">
                <x-slot name="title">Cancel Order</x-slot>

                @if ($order_is_cancelled_manually_via_sx)
                    <div class="alert alert-light-warning color-warning"><i class="bi bi-exclamation-triangle"></i>
                        This action cannot be reverted. Cancelling will also update the SX order</div>



                    @if (!empty($errorMessage))
                        <div class="alert alert-light-danger color-danger">
                            <button wire:click="moveToErrorsTab('{{ $errorMessage }}')"
                                class="btn btn-sm btn-outline-danger float-end">
                                <div wire:loading wire:target="moveToErrorsTab">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                </div>
                                Move to Errors Tab
                            </button>
                            {!! $errorMessage !!}<br>
                        </div>
                    @endif

                    @if (!empty($currentSession))
                        <div class="alert alert-light-danger color-danger">
                            <div class="spinner-grow spinner-grow-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div> This order has an active session by <strong>{{ $operator->name }}</strong> at
                            {{ $operator->site }}. Try ending the session and reloading the page to retry<br><br>
                            <strong>E-Mail</strong> : {{ $operator->email }}<br><strong>Phone</strong> :
                            {{ $operator->phoneno }}<br><strong>Extension</strong> : {{ $operator->modphoneno }}
                        </div>
                    @endif

                    @if ($this->is_tied_order)
                        <div class="alert alert-light-danger color-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                            This is a <strong>Tied Order</strong>, the system will break the tie if you decide to
                            Cancel. The
                            Purchasing Department will be notified via email and may need to manually intervene after
                            the
                            Cancellation.
                            <div class="row">
                                <div class="col-md-12 mt-3">
                                    <div class="float-end" wire:loading wire:target="tiedOrderAcknowledgement">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>

                                    <x-forms.checkbox label="I understand, proceed to Cancel Order"
                                        model="tiedOrderAcknowledgement" />

                                </div>
                            </div>

                        </div>
                    @endif


                    @if($cancelOrderModal)
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="pre-genkey-div">
                                <x-forms.input label="From" model="emailFrom" disabled />
                                <x-forms.input label="To" model="emailTo" />
                                <x-forms.input label="Email Subject" model="cancelEmailSubject" />
                                <x-forms.html-editor label="Email Content" model="cancelEmailContent" rows="6" />
                            </div>
                        </div>
                    </div>
                    @endif

                    <x-slot name="footer">
                        <button wire:click="cancelOrder" wire:loading.attr="disabled" type="button"
                            class="pre-genkey-div btn btn-danger" @if (!empty($currentSession) || ($this->is_tied_order && !$tiedOrderAcknowledgement)) disabled @endif>
                            <div wire:loading wire:target="cancelOrder">
                                <span class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                            </div>
                            Cancel &
                            Notify
                        </button>
                        <button wire:click="closePopup('cancelOrderModal')" type="button"
                            class="btn btn-outline-secondary">
                            <div wire:loading wire:target="closePopup('cancelOrderModal')">
                                <span class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                            </div>

                            Close
                        </button>
                    </x-slot>
                @else
                    <div class="alert alert-light-warning color-warning"><i class="bi bi-exclamation-triangle"></i>
                        <button wire:click="checkOrderCancelStatus" class="btn btn-sm btn-warning float-end mt-n1">
                            <div wire:loading wire:target="checkOrderCancelStatus">
                                <span class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                            </div>

                            Ok,
                            I have
                            Cancelled
                        </button>
                        You will need to manually <strong>Cancel</strong> this Order <u class="clipboard"
                            data-clipboard-text="{{ $this->sx_order->orderno }}">{{ $this->sx_order->orderno }}</u>
                        in
                        SX before proceeding to the next
                        step.
                    </div>
                @endif



            </x-modal>

            <x-modal :toggle="$followUpModal" size="xl">
                <x-slot name="title">Notify Customer</x-slot>

                <div class="alert alert-light-warning color-warning">
                    Notifying the customer will update this backorder to <strong>Follow Up</strong> status</div>

                @if (empty($this->non_dnr_line_items))
                    <div class="alert alert-light-danger color-danger">
                        All line items in this order are DNR
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <h5>
                            <div class="form-check">
                                <div class="checkbox">
                                    <input type="checkbox" id="checkbox1" class="form-check-input" checked="">
                                    <label for="checkbox1"
                                        style="
                                    margin-top: 4px;
                                ">Email</label>
                                </div>
                            </div>
                        </h5>

                        @if($followUpModal)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="pre-genkey-div">
                                    <x-forms.input label="From" model="emailFrom" disabled />

                                    <x-forms.input label="To" model="emailTo" />

                                    <x-forms.input label="Email Subject" model="followUpSubject" />
                                    <x-forms.html-editor label="Email Content" model="followUpEmailContent" rows="6" />
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5>
                            <div class="form-check">
                                <div class="checkbox">
                                    <input type="checkbox" id="checkbox1" class="form-check-input" checked="">
                                    <label for="checkbox1"
                                        style="
                                    margin-top: 4px;
                                ">SMS</label>
                                </div>
                            </div>
                        </h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="pre-genkey-div">

                                    <x-forms.input label="To" model="smsPhone" />

                                    <x-forms.textarea label="Message" model="smsMessage" rows="6" />
                                </div>
                            </div>
                        </div>

                    </div>
                </div>



                <x-slot name="footer">
                    <button wire:click="sendEmail" type="button" class="pre-genkey-div btn btn-success"
                        wire:loading.attr="disabled">
                        <div wire:loading wire:target="sendEmail">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        <i class="fas fa-paper-plane"></i> Notify Customer
                    </button>
                    <button wire:click="closePopup('followUpModal')" type="button"
                        class="btn btn-outline-secondary">
                        <div wire:loading wire:target="closePopup('followUpModal')">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        Close
                    </button>
                </x-slot>

            </x-modal>

            <x-modal :toggle="$shippingModal" size="xl">
                <x-slot name="title">Email Shipping Department</x-slot>

                <div class="alert alert-light-warning color-warning">
                    Emailing the shipping department will update this order to <strong>Shipment Follow Up</strong>
                    status</div>

                @if($shippingModal)
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="pre-genkey-div">
                            <x-forms.input label="From" model="emailFrom" disabled />

                            <x-forms.input label="To" model="shippingEmail" />

                            <x-forms.input label="Email Subject" model="shippingSubject" />

                            <x-forms.html-editor label="Email Content" model="shippingEmailContent" rows="6" />
                        </div>
                    </div>
                </div>
                @endif

                <x-slot name="footer">
                    <button wire:click="sendShippingEmail" type="button" class="pre-genkey-div btn btn-success"
                        wire:loading.attr="disabled">
                        <div wire:loading wire:target="sendShippingEmail">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        <i class="fas fa-paper-plane"></i> Send Email
                    </button>
                    <button wire:click="closePopup('shippingModal')" type="button"
                        class="btn btn-outline-secondary">
                        <div wire:loading wire:target="closePopup('shippingModal')">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        Close
                    </button>
                </x-slot>

            </x-modal>

            <x-modal :toggle="$receivingModal" size="xl">
                <x-slot name="title">Email Receiving Department</x-slot>

                <div class="alert alert-light-warning color-warning">
                    Emailing receiving will update this order to <strong>Receiving Follow Up</strong>
                    status</div>

                @if($receivingModal)
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="pre-genkey-div">
                            <x-forms.input label="From" model="emailFrom" disabled />

                            <x-forms.input label="To" model="receivingEmail" />

                            <x-forms.input label="Email Subject" model="receivingSubject" />

                            <x-forms.html-editor label="Email Content" model="receivingEmailContent" rows="6" />
                        </div>
                    </div>
                </div>
                @endif

                <x-slot name="footer">
                    <button wire:click="sendReceivingEmail" type="button" class="pre-genkey-div btn btn-success"
                        wire:loading.attr="disabled">
                        <div wire:loading wire:target="sendReceivingEmail">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        <i class="fas fa-paper-plane"></i> Send Email
                    </button>
                    <button wire:click="closePopup('receivingModal')" type="button"
                        class="btn btn-outline-secondary">
                        <div wire:loading wire:target="closePopup('receivingModal')">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </div>
                        Close
                    </button>
                </x-slot>

            </x-modal>




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
