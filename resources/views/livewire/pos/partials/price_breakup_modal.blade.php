<x-modal :toggle="$priceBreakdownModal" :size="'lg'" :closeEvent="'closeBreakdownModal'">
    <x-slot name="title">
        <div>Price Breakdown</div>
    </x-slot>

    <div>
        <table class="table table-striped">
            <thead>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total Price</th>
            </thead>
            <tbody>
                @foreach ($cart as $item)
                    <tr>
                        <td>{{ $item['product_name'] }} ({{ $item['product_code'] }})</td>
                        <td>{{ format_money($item['price']) }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>{{ format_money($item['total_price']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">
            <div class="text-end me-5">
                <span class="me-3">Tax:</span> {{ format_money($netTax) }}
            </div>
            
            @if(!empty($couponDiscount))
            <div class="text-end me-5">
                <span class="me-3">Coupon Discount:</span> {{ format_money($couponDiscount) }}
            </div>
            @endif

            <div class="text-end me-5 font-bold">
                <span class="me-3">Total:</span> {{ format_money($netPrice) }}
            </div>
        </div>
    </div>
</x-modal>
