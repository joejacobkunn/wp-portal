<div class="order-response-div succes-div">
    <div class="title-div">
        <h2 class="mt-4"><i class="bi bi-patch-check-fill"></i> Order placed successfully!</h2>
    </div>
    <div class="row">
        <div class="col-sm-6 offset-md-3 mt-4">
            <table class="table">
                <tr>
                    <td>Order ID</td>
                    <td>{{ $orderData['id'] }}</td>
                </tr>
                <tr>
                    <td>Amount</td>
                    <td>{{ $netPrice }}</td>
                </tr>
            </table>
        </div>
    </div>

    <p class="mt-4">Click here to add new order</p>
    <button type="button" wire:click="refreshOrder" class="btn btn-outline-primary btn-lg mb-5"><i class="fa fa-plus"></i> Add New Order</button>
</div>