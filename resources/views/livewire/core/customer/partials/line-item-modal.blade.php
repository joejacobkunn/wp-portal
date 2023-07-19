<x-modal :toggle="$open_line_item_modal" size="xl">
    <x-slot name="title">
        <div class="">Line Items for Order# @if(!empty($this->order_line_items) &&
            !is_null($this->order_line_items->first())) {{
            $this->order_line_items->first()->orderno
            }}
            @endif</div>
    </x-slot>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-light-primary color-primary">
                <i class="fas fa-info-circle"></i> See basic order details of this open order here
            </div>

            <div class="card border-secondary collapse-icon accordion-icon-rotate">
                <div class="card-body">
                    <div class="list-group">
                        @if(!empty($this->order_line_items))
                        @forelse ($this->order_line_items as $item)
                        <a href="https://weingartz.com//searchPage.action?keyWord={{$item->shipprod}}" target="_blank"
                            class="list-group-item list-group-item-action" aria-current="true">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">Line #{{$item->lineno}} :
                                    {{$item->user3}} {{$item->shipprod}} {{ rtrim(str_replace(";",",
                                    ",$item->descrip),", ") }}
                                </h5>
                                <small><span class="badge bg-light-success">Category :
                                        {{strtoupper($item->prodcat)}} / Prod Line :
                                        {{strtoupper($item->prodline)}}</span>
                                </small>
                            </div>
                            <small>
                                <span class="badge bg-light-secondary">Type :
                                    {{$item->getSpecType()}}</span>
                            </small>
                            <small>
                                <span class="badge bg-light-primary">Qty Ordered :
                                    {{intval($item->qtyord)}}</span>
                            </small>
                            <small>
                                <span class="badge bg-light-info">Qty Shipped :
                                    {{intval($item->qtyship)}}</span>
                            </small>

                            <small>
                                <span class="badge bg-light-warning">Price : ${{number_format($item->price,2)}}</span>
                            </small>
                            <small>
                                <span class="badge bg-light-primary">Net Amt :
                                    ${{number_format($item->netamt,2)}}</span>
                            </small>
                            <small>
                                <span class="badge bg-light-secondary">Tied :
                                    {{$item->getTied()}}</span>
                            </small>
                            <small>
                                <span class="badge bg-light-warning">Related Order# :
                                    {{strtoupper($item->orderaltno) ?: 'N/A'}}</span>
                            </small>

                            @if(!empty($item->user8))
                            <small>
                                <span class="badge bg-light-info">Exp Date :
                                    {{date('M j, Y',strtotime($item->user8)) ?: 'N/A'}}</span>
                            </small>
                            @endif

                        </a>
                        @empty
                        <div class="alert alert-light-warning color-warning">
                            No open orders for this customer
                        </div>
                        @endforelse
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-modal>