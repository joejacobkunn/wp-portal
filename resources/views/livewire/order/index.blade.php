<div wire:init="getStatusCounts">

    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Orders for {{ $account->name }}</x-slot>

        <x-slot:content>

            @include('livewire.order.partials.metrics')

            <div class="card border-light shadow-sm mb-4" style="min-height: 600px">
                <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                    <button wire:click='import' class="btn btn-sm btn-outline-primary float-end"><i
                            class="fas fa-plus"></i> Import Orders</button>
                </div>

                <div class="card-body">

                    @if ($order_data_sync_timestamp)
                        <div class="alert alert-light-info color-warning">
                            <button type="button" wire:click="updateOpenOrders" wire:loading.attr="disabled"
                                class="btn btn-sm btn-outline-primary float-end">
                                <div wire:loading wire:target="updateOpenOrders">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                </div>
                                Update Open Orders
                            </button>
                            <i class="fas fa-sync"></i>
                            Last refreshed
                            <strong>{{ Carbon\Carbon::parse($order_data_sync_timestamp)->diffForHumans() }}</strong>.
                            <span wire:loading wire:target="updateOpenOrders">Please wait, this might take a
                                minute</span>
                        </div>
                    @endif
                    @if ($showImportNotification)
                        <div class="alert alert-light-info color-info">
                            <i class="bi bi-exclamation-circle"></i> Your import request has been received and is being processed. You will receive an email when it is complete.
                        </div>

                    @endif
                    <livewire:order.table lazy>
                </div>
            </div>
            @include('livewire.order.partials.order-import-modal')
        </x-slot>
    </x-page>

</div>
