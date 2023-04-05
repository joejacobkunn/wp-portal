<div>

    <x-page
        :breadcrumbs="$breadcrumbs"
    >

        <x-slot:title>Orders</x-slot>

        <x-slot:description>
            View orders here
        </x-slot>

        <x-slot:content>
            
                <div>
                    <div class="card border-light shadow-sm mb-4" style="min-height: 600px">
                        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">

                            @can('order.manage')
                                <button wire:click="create()" class="btn btn-success btn-lg btn-fab"><i class="fas fa-plus"></i></button>
                            @endcan

                            <h3 class="h5 mb-0">Order List for {{$account->name}}</h3>

                        </div>

                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                    <th scope="col">Order Number</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Warehouse</th>
                                    <th scope="col">Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <th scope="row">{{$order->orderno}} - {{$order->ordersuf}}</th>
                                            <td>{{$order->shiptonm}}</td>
                                            <td>{{$order->whse}}</td>
                                            <td>${{$order->totcost}}</td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

        </x-slot>

    </x-page>

</div>




