<div class="order-export-modal">
    <x-modal toggle="exportModal" size="lg" :closeEvent="'closeModel'">
        <x-slot name="title">Reports</x-slot>
        @if (empty($orderType))
            <div class="alert alert-secondary">
                Select a report from the dropdown to export to csv. Reports will be sent to
                <strong>{{ auth()->user()->email }}</strong> when complete
            </div>
        @endif

        @if ($orderType == 'open')
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Report to export all open orders as of
                {{ Carbon\Carbon::parse($order_data_sync_timestamp)->diffForHumans() }}
            </div>
        @endif

        @if ($orderType == 'closed')
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Report to export all closed orders as of
                {{ Carbon\Carbon::parse($order_data_sync_timestamp)->diffForHumans() }}
            </div>
        @endif

        @if ($orderType == 'ready_for_shipment_web')
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Report to export all open orders for all web orders that show quantity
                ordered equals quantity
                shipped but have not been shipped out yet as of
                {{ Carbon\Carbon::parse($order_data_sync_timestamp)->diffForHumans() }}
            </div>
        @endif

        <form wire:submit.prevent="submit()">
            <div class="row w-100">
                <div class="col-md-12">
                    <div class="form-group">
                        <x-forms.select label="Order Type" model="orderType" :options="[
                            'open' => 'Open Orders',
                            'closed' => 'Closed Orders',
                            'ready_for_shipment_web' => 'Ready for Shipment (WEB)',
                        ]" :selected="$orderType"
                            hasAssociativeIndex default-option-label="- None -" :key="'export-' . now()" />
                    </div>
                </div>
            </div>
            <div class="float-start mt-2">
                <button type="submit" class="btn btn-primary">
                    <div wire:loading wire:target="submit">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    <i class="fas fa-file-download"></i> Email Report
                </button>
            </div>
        </form>
    </x-modal>
</div>
