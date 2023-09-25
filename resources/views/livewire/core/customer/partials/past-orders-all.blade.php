<div class="alert alert-light-warning color-warning">
    Showing last 5 years of past orders
</div>

<div class="list-group overflow-scroll" style="height:600px">
    @if(!empty($this->orders) && $open_order_tab)
    @forelse ($this->orders->whereIn('stagecd',[3,4,5]) as $order)
    @php $sro_number = ($order->is_sro == 'SRO') ? $order->refer : ''; @endphp
    <a wire:click="fetchOrderDetails({{$order->orderno}},{{$order->ordersuf}},'{{$sro_number}}','past-order')"
        class="list-group-item list-group-item-action" aria-current="true">
        <div class="d-flex w-100 justify-content-between">
            <h5 class="mb-1">Order# {{$order->orderno}}-{{$order->ordersuf}}
                @if($order->is_sro == 'SRO') <span class="badge bg-light-danger"><i class="fas fa-tools"></i>
                    {{$order->refer}}</span> @endif
            </h5>
            <small><span class="badge bg-light-secondary">{{$order->getStageCode($order->stagecd)}}</span></small>
        </div>

        @php

        $backorder_count = intval($order->totqtyord) - intval($order->totqtyshp);
        $item_count = intval($order->totqtyret) + (intval($order->totqtyord) + intval($order->totqtyret));

        @endphp

        <p class="mb-1">Order has <strong>{{$item_count}}</strong> item(s) totalling
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
            <span class="badge bg-light-success">{{strtoupper($order->getShippingStage($order->stagecd))}}
                : {{intval($order->totqtyshp) + intval($order->totqtyret) }}</span>
            @if(!empty($order->totqtyret) && intval($order->totqtyret) > 0)
            <span class="badge bg-light-warning">RETURNED : {{intval($order->totqtyret)}}</span>
            @endif

            <span class="badge bg-light-secondary">PROMISE DT : {{date("M j,
                Y",strtotime($order->promisedt))}}</span>
            @if(str_contains($order->item_type,'A'))
            <span class="badge bg-primary">ACCESSORY</span>
            @endif

            @if(str_contains($order->item_type,'E'))
            <span class="badge bg-success">EQUIPMENT</span>
            @endif

            @if(str_contains($order->item_type,'P'))
            <span class="badge bg-info">PART</span>
            @endif


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
        No past orders for this customer
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