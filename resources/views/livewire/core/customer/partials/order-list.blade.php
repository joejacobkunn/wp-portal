<div class="card border border-light border-3 shadow-sm mb-4">
    <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
        <h3 class="h5 mb-2">Customer Order History</h3>
    </div>

    <div wire:ignore class="card-body">

        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link @if($open_order_tab) active @endif" id="pills-home-tab" data-bs-toggle="pill"
                    data-bs-target="#pills-open-orders" type="button" role="tab" aria-controls="pills-home"
                    aria-selected="true">Open Orders <span
                        class="badge bg-light-primary">@if(!empty($this->orders->whereIn('stagecd',[1,2])))
                        {{count($this->orders->whereIn('stagecd',[1,2]))}} @endif</span></button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link @if($past_order_tab) active @endif" id="pills-profile-tab" data-bs-toggle="pill"
                    data-bs-target="#pills-other-orders" type="button" role="tab" aria-controls="pills-profile"
                    aria-selected="false">Past Orders <span
                        class="badge bg-light-primary">@if(!empty($this->orders->whereIn('stagecd',[3,4,5])))
                        {{count($this->orders->whereIn('stagecd',[3,4,5]))}} @endif</span></button>
            </li>
        </ul>


        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-open-orders" role="tabpanel"
                aria-labelledby="pills-home-tab">
                <div class="list-group overflow-scroll" style="height:600px">
                    @if(!empty($this->orders) && $open_order_tab)
                    @forelse ($this->orders->whereIn('stagecd',[1,2]) as $order)
                    @php $sro_number = ($order->is_sro == 'SRO') ? $order->refer : ''; @endphp
                    <a wire:click="fetchOrderDetails({{$order->orderno}},{{$order->ordersuf}},'{{$sro_number}}','open-order')"
                        class="list-group-item list-group-item-action" aria-current="true">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">Order# {{$order->orderno}}-{{$order->ordersuf}}
                                @if($order->is_sro == 'SRO') <span class="badge bg-light-danger"><i
                                        class="fas fa-tools"></i>
                                    {{$order->refer}}</span> @endif
                            </h5>
                            <small><span
                                    class="badge bg-light-secondary">{{$order->getStageCode($order->stagecd)}}</span></small>
                        </div>

                        @php

                        $backorder_count = intval($order->totqtyord) - intval($order->totqtyshp);

                        @endphp

                        <p class="mb-1">Order has <strong>{{intval($order->totqtyord)}}</strong> item(s) totalling
                            <strong>${{number_format($order->totordamt,2)}}</strong> on
                            <strong>{{$order->enterdt->toFormattedDateString()}}</strong>.
                            @if($backorder_count > 0)
                            <span class="bg-danger text-white"> <strong>{{$backorder_count}}</strong> item(s)
                                backordered</span>
                            @endif
                        </p>
                        <small>
                            <span class="badge bg-light-info">WHSE : {{strtoupper($order->whse)}}</span>
                            <span class="badge bg-light-warning">TRANS TYPE : {{strtoupper($order->transtype)}}</span>
                            <span class="badge bg-light-secondary">TAKEN BY : {{strtoupper($order->takenby)}}</span>
                            <span
                                class="badge bg-light-success">{{strtoupper($order->getShippingStage($order->stagecd))}}
                                : {{strtoupper(intval($order->totqtyshp))}}</span>
                            <span class="badge bg-light-secondary">PROMISE DT : {{date("M j,
                                Y",strtotime($order->promisedt))}}</span>

                            <div class="float-end" wire:loading
                                wire:target="fetchOrderDetails({{$order->orderno}},{{$order->ordersuf}},'{{$sro_number}}','open-order')">
                                <div class="spinner-border spinner-border-sm float-end" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>

                        </small>
                    </a>
                    @empty
                    <div class="alert alert-light-warning color-warning">
                        No open orders for this customer
                    </div>
                    @endforelse
                    @else
                    <div class="card" aria-hidden="true">
                        <div class="card-body">
                            <h5 class="card-title placeholder-glow">
                                <span class="placeholder col-6"></span>
                            </h5>
                            <p class="card-text placeholder-glow">
                                <span class="placeholder col-7"></span>
                                <span class="placeholder col-4"></span>
                                <span class="placeholder col-4"></span>
                                <span class="placeholder col-6"></span>
                                <span class="placeholder col-8"></span>
                            </p>
                            <a href="#" tabindex="-1" class="btn btn-primary disabled placeholder col-6"></a>
                        </div>
                    </div>
                    @endif
                </div>

            </div>
            <div class="tab-pane fade" id="pills-other-orders" role="tabpanel" aria-labelledby="pills-profile-tab">
                <div class="list-group">
                    <div class="alert alert-light-warning color-warning">
                        Showing last 5 years of past orders
                    </div>
                    @forelse ($this->orders->whereIn('stagecd',[3,4,5]) as $order)
                    @php $sro_number = ($order->is_sro == 'SRO') ? $order->refer : ''; @endphp
                    <a wire:click="fetchOrderDetails({{$order->orderno}},{{$order->ordersuf}},'{{$sro_number}}','past-order')"
                        class="list-group-item list-group-item-action" aria-current="true">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">
                                Order# {{$order->orderno}}-{{$order->ordersuf}}
                                @if($order->is_sro == 'SRO') <span class="badge bg-light-danger"><i
                                        class="fas fa-tools"></i>
                                    {{$order->refer}}</span> @endif

                            </h5>
                            <small><span
                                    class="badge bg-light-secondary">{{$order->getStageCode($order->stagecd)}}</span></small>
                        </div>
                        <p class="mb-1">Order has <strong>{{intval($order->totqtyord)}}</strong> item(s) totalling
                            <strong>${{number_format($order->totordamt,2)}}</strong> on
                            <strong>{{$order->enterdt->toFormattedDateString()}}</strong>
                        </p>
                        <small>
                            <span class="badge bg-light-info">WHSE : {{strtoupper($order->whse)}}</span>
                            <span class="badge bg-light-warning">TRANS TYPE : {{strtoupper($order->transtype)}}</span>
                            <span class="badge bg-light-secondary">TAKEN BY : {{strtoupper($order->takenby)}}</span>
                            <span
                                class="badge bg-light-success">{{strtoupper($order->getShippingStage($order->stagecd))}}
                                : {{strtoupper(intval($order->totqtyshp))}}</span>

                            <span class="badge bg-light-secondary">PROMISE DT : {{date("M j,
                                Y",strtotime($order->promisedt))}}</span>

                            <div class="float-end" wire:loading
                                wire:target="fetchOrderDetails({{$order->orderno}},{{$order->ordersuf}},'{{$sro_number}}','past-order')">
                                <div class="spinner-border spinner-border-sm float-end" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>

                        </small>
                    </a>
                    @empty
                    <div class="alert alert-light-warning color-warning">
                        No open orders for this customer
                    </div>
                    @endforelse

                </div>

            </div>
        </div>

    </div>


</div>