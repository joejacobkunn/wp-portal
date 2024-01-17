<div>

    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Orders</x-slot>

        <x-slot:description>
            View orders here
        </x-slot>

        <x-slot:content>

            <div class="row">
                <div class="col-2">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link {{ $orderTab == 'orders' ? 'active' : '' }}" id="v-pills-home-tab" data-bs-toggle="pill" href="#v-pills-home"
                            role="tab" aria-controls="v-pills-home" aria-selected="true" wire:click="$set('orderTab', 'orders')">Orders</a>
                        <a class="nav-link {{ $orderTab == 'back_orders' ? 'active' : '' }}" id="v-pills-profile-tab" data-bs-toggle="pill" href="#v-pills-profile"
                            role="tab" aria-controls="v-pills-profile" aria-selected="false" tabindex="-1" wire:click="$set('orderTab', 'back_orders')">Back
                            Orders</a>
                    </div>
                </div>
                <div class="col-10">
                    <div class="tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade {{ $orderTab == 'orders' ? 'active show' : '' }}" id="v-pills-home" role="tabpanel"
                            aria-labelledby="v-pills-home-tab">
                            <div>
                                <div class="card border-light shadow-sm mb-4" style="min-height: 600px">
                                    <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">

                                        @can('order.manage')
                                            <button wire:click="create()" class="btn btn-success btn-lg btn-fab"><i
                                                    class="fas fa-plus"></i></button>
                                        @endcan

                                        <h3 class="h5 mb-0">Order List for {{ $account->name }}</h3>

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
                                                @foreach ($orders as $order)
                                                    <tr>
                                                        <th scope="row">{{ $order->orderno }} -
                                                            {{ $order->ordersuf }}</th>
                                                        <td>{{ $order->shiptonm }}</td>
                                                        <td>{{ $order->whse }}</td>
                                                        <td>${{ $order->totcost }}</td>

                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="tab-pane fade {{ $orderTab == 'back_orders' ? 'active show' : '' }}" id="v-pills-profile" role="tabpanel"
                            aria-labelledby="v-pills-profile-tab">
                            <div class="card border-light shadow-sm mb-4" style="min-height: 600px">
                                <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                                    <h3 class="h5 mb-0">Backorder List for {{ $account->name }}</h3>
                                </div>

                                <div class="card-body">

                                    <x-tabs
                                        :tabs="$tabs"
                                        tabId="back-order-tabs"
                                        class="mb-5">
                                        <x-slot:content>
                                            <livewire:order.backorder-table
                                                :activeTab="$tabs['back-order-tabs']['active']"
                                                key="{{ $tabs['back-order-tabs']['active'] }}"
                                                lazy />
                                        </x-slot>

                                    </x-tabs>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


        </x-slot>

    </x-page>

</div>
