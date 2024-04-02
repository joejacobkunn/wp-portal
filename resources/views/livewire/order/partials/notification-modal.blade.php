<x-modal :toggle="$notificationModal" size="xl">
    <x-slot name="title">
        @if ($cancelOrderModal)
            Cancel Order
        @endif
        @if ($followUpModal)
            Follow Up on Order
        @endif
        @if ($shippingModal)
            Shipping Follow Up on Order
        @endif
        @if ($receivingModal)
            Receiving Follow Up on Order
        @endif
    </x-slot>

    @if ($cancelOrderModal)
        @if ($order_is_cancelled_manually_via_sx)
            <div class="alert alert-light-warning color-warning"><i class="bi bi-exclamation-triangle"></i>
                This action cannot be reverted. Cancelling will also update the SX order</div>
        @else
            <div class="alert alert-light-warning color-warning"><i class="bi bi-exclamation-triangle"></i>
                <button wire:click="checkOrderCancelStatus" class="btn btn-sm btn-warning float-end mt-n1">
                    <div wire:loading wire:target="checkOrderCancelStatus">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
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
    @endif

    @if ($followUpModal)
        <div class="alert alert-light-warning color-warning">
            Notifying the customer will update this backorder to <strong>Follow Up</strong> status</div>
    @endif

    @if ($shippingModal)
        <div class="alert alert-light-warning color-warning">
            Emailing the shipping department will update this order to <strong>Shipment Follow Up</strong>
            status</div>
    @endif

    @if (($cancelOrderModal && $order_is_cancelled_manually_via_sx) || $followUpModal || $shippingModal || $receivingModal)
        <div class="row">
            <div class="col-md-12 mb-2 mt-2">
                <div class="form-group">
                    <x-forms.select wire:key="'template-dropdown-'.now()" label="Template" model="templateId"
                        :options="$templates" :selected="$templateId ?? null" default-selectable default-option-label="- None -"
                        label-index="name" value-index="id" :listener="'templateChanged'" />
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="pre-genkey-div">
                    <x-forms.input label="From" model="emailFrom" disabled />
                    <x-forms.input label="To" model="emailTo" />
                    <x-forms.input label="Email Subject" model="emailSubject" />
                    <x-forms.html-editor :key="$templateId" :value="$emailContent" label="Email Content" model="emailContent"
                        rows="6" />
                    @if (!empty($this->getManualPlaceholders()))
                        <div class="p-2 mb-2 bg-warning text-dark">Please populate
                            {{ implode(',', $this->getManualPlaceholders()) }} above</div>
                    @endif

                </div>
            </div>
        </div>

        @if ($cancelOrderModal)
            <x-slot name="footer">
                <button wire:click="cancelOrder" wire:loading.attr="disabled" type="button"
                    class="pre-genkey-div btn btn-danger">
                    <div wire:loading wire:target="cancelOrder">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    Cancel &
                    Notify
                </button>
                <button wire:click="closePopup('cancelOrderModal')" type="button" class="btn btn-outline-secondary">
                    <div wire:loading wire:target="closePopup('cancelOrderModal')">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>

                    Close
                </button>
            </x-slot>
        @endif

        @if ($followUpModal)
            <x-slot name="footer">
                <button wire:click="sendEmail" type="button" class="pre-genkey-div btn btn-success"
                    wire:loading.attr="disabled">
                    <div wire:loading wire:target="sendEmail">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    <i class="fas fa-paper-plane"></i> Notify Customer
                </button>
                <button wire:click="closePopup('followUpModal')" type="button" class="btn btn-outline-secondary">
                    <div wire:loading wire:target="closePopup('followUpModal')">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    Close
                </button>
            </x-slot>
        @endif

        @if ($shippingModal)
            <x-slot name="footer">
                <button wire:click="sendShippingEmail" type="button" class="pre-genkey-div btn btn-success"
                    wire:loading.attr="disabled">
                    <div wire:loading wire:target="sendShippingEmail">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    <i class="fas fa-paper-plane"></i> Send Email
                </button>
                <button wire:click="closePopup('shippingModal')" type="button" class="btn btn-outline-secondary">
                    <div wire:loading wire:target="closePopup('shippingModal')">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    Close
                </button>
            </x-slot>
        @endif

        @if ($receivingModal)
            <x-slot name="footer">
                <button wire:click="sendReceivingEmail" type="button" class="pre-genkey-div btn btn-success"
                    wire:loading.attr="disabled">
                    <div wire:loading wire:target="sendReceivingEmail">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    <i class="fas fa-paper-plane"></i> Send Email
                </button>
                <button wire:click="closePopup('receivingModal')" type="button" class="btn btn-outline-secondary">
                    <div wire:loading wire:target="closePopup('receivingModal')">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    Close
                </button>
            </x-slot>
        @endif

    @endif

</x-modal>
