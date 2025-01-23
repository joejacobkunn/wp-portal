<div wire:init="setWarehouse">

    <x-page :breadcrumbs="$breadcrumbs">
        <x-slot:title>Checkout</x-slot>
        <x-slot:content>

            <div class="card border-light shadow-sm mb-4">
                <div class="card-content">
                    <div class="card-body">

                        <div class="checkout-outer-div p-3">
                            @if ($orderStatus == 'completed')
                                @include('livewire.pos.partials.order_success')
                            @else
                                @include('livewire.pos.partials.checkout')
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    @if ($productSearchModal)
                        @include('livewire.pos.partials.product_modal')
                    @endif

                    @if ($customerSearchModal)
                        @include('livewire.pos.partials.customer_modal')
                    @endif

                    @if ($couponSearchModal)
                        @include('livewire.pos.partials.coupon_modal')
                    @endif

                    @if ($newCustomerModal)
                        <x-modal toggle="newCustomerModal" :size="'xl'" :closeEvent="'closeNewCustomer'">
                            <x-slot name="title">
                                <div>Add New Customer</div>
                            </x-slot>

                            <livewire:core.customer.create source-popup />
                        </x-modal>
                    @endif

                    @if ($priceBreakdownModal)
                        @include('livewire.pos.partials.price_breakup_modal')
                    @endif


                </div>
            </div>

        </x-slot>

    </x-page>

    @script
        <script type="text/javascript" id="ariPartStream"
            src="https://services.arinet.com/PartStream/?appKey={{ config('partstream.key') }}"></script>
    @endscript
</div>
