<x-modal :toggle="$orderStatusModal" :size="'lg'" closeEvent="closeOrderStatusModal">
    <x-slot name="title">
        <div>Order Status</div>
    </x-slot>

    <div>
        @if(!empty($lastTransactionDetails))
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td>#ID</td>
                    <td>{{ $lastTransactionDetails['id'] }}</td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td><span class="badge bg-{{ $lastTransactionDetails['status_class'] }}"><i class="fas {{ $lastTransactionDetails['status_icon'] }} me-1"></i> {{ $lastTransactionDetails['status'] }}</span></td>
                </tr>
                <tr>
                    <td>Transaction Amount</td>
                    <td>${{ number_format(($lastTransactionDetails['transaction_amount'] / 100), 2) }}</td>
                </tr>
                <tr>
                    <td>Description</td>
                    <td>{{ $lastTransactionDetails['description'] }}</td>
                </tr>
            </tbody>
        </table>
        @endif
    </div>
</x-modal>
