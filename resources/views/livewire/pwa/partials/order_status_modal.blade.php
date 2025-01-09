<x-modal :toggle="$orderStatusModal" :size="'lg'" closeEvent="closeOrderStatusModal">
    <x-slot name="title">
        <div>Order Status</div>
    </x-slot>

    <div class="order-status-div">
        @if(!empty($lastTransactionDetails))
        <table class="table table-table-table-sm table-striped-columns">
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
        
        @if(!empty($lastTransactionDetails['emv_receipt_data']))
            <strong class="ms-2 mb-2 d-block">Emv Receipt</strong>
            <table class="table emv-table table-table-table-sm table-striped-columns">
                <tbody>
                    <tr>
                        <td>TVR</td>
                        <td>{{ $lastTransactionDetails['emv_receipt_data']['TVR'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>AID</td>
                        <td>{{ $lastTransactionDetails['emv_receipt_data']['AID'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>TSI</td>
                        <td>{{ $lastTransactionDetails['emv_receipt_data']['TSI'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>APPLAB</td>
                        <td>{{ $lastTransactionDetails['emv_receipt_data']['APPLAB'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>CVM</td>
                        <td>{{ $lastTransactionDetails['emv_receipt_data']['CVM'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>PAN</td>
                        <td>{{ $lastTransactionDetails['emv_receipt_data']['PAN'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>IAD</td>
                        <td>{{ $lastTransactionDetails['emv_receipt_data']['IAD'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>ARC</td>
                        <td>{{ $lastTransactionDetails['emv_receipt_data']['ARC'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>CURR</td>
                        <td>{{ $lastTransactionDetails['emv_receipt_data']['CURR'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>MODE</td>
                        <td>{{ $lastTransactionDetails['emv_receipt_data']['MODE'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>AC</td>
                        <td>{{ $lastTransactionDetails['emv_receipt_data']['AC'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>NETWORK</td>
                        <td>{{ $lastTransactionDetails['emv_receipt_data']['NETWORK'] ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        @endif

        @endif
    </div>
</x-modal>
