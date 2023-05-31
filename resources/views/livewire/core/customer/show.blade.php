<div wire:init="loadData">
    <div>
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
            <div class="d-block mb-4 mb-md-0 mt-2">
                <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
            </div>
        </div>
    </div>

    <div class="row px-2">
        <div class="card border-light shadow-sm border border-3 mb-4">
            <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                <h3 class="h5 mb-0">
                    <span class="badge bg-primary float-end clipboard"
                        data-clipboard-text="{{$customer->sx_customer_number}}">SX#
                        {{$customer->sx_customer_number}}</span>
                    Customer Overview for {{ucwords(strtolower($customer->name))}}
                </h3>
            </div>

            <div class="card-body">


                <div class="row">
                    <div class="col-sm-6">
                        @if($customer->is_active)
                        <div class="alert alert-light-success color-success" role="alert">
                            <i class="fa fa-check" aria-hidden="true"></i> This Customer is <strong>Active</strong>
                        </div>
                        @else
                        <div class="alert alert-light-danger color-danger" role="alert">
                            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> This Customer is Deactivated
                        </div>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <div class="alert @if($customer_has_good_credit_status) alert-light-primary color-primary @else alert-light-danger color-danger @endif"
                            role="alert">
                            <i class="fas fa-handshake"></i> Credit Status Check :
                            <strong>{!! $credit_status ? $credit_status['message'] ?: 'OK' : '<div
                                    class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div> Verifying...' !!}</strong>
                        </div>
                    </div>
                </div>

                @include('livewire.core.customer.partials.customer-dashboard')

                <div class="row">
                    <div class="col-sm-12 mb-4">
                        @include('livewire.core.customer.partials.customer-details')
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-7">
                        @include('livewire.core.customer.partials.order-list')
                    </div>
                    <div class="col-sm-5">
                        @include('livewire.core.customer.partials.note-list')
                    </div>

                </div>

                <div class="row">
                    <div class="col-sm-12">
                        @include('livewire.core.customer.partials.equipment-list')
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        @include('livewire.core.customer.partials.repair-order-list')
                    </div>
                </div>

            </div>
        </div>
    </div>
    @include('livewire.core.customer.partials.line-item-modal')
</div>

<script>
    document.addEventListener('livewire:load', function () {
        var clipboard = new ClipboardJS('.clipboard');

        clipboard.on('success', function(e) {
            console.info('Action:', e.action);
            console.info('Text:', e.text);
            console.info('Trigger:', e.trigger);
            @this.emit('clipboardCopied')

            e.clearSelection();
        });
    })

</script>