<x-modal :toggle="$open_order_modal">
    <x-slot name="title">
        <div class="">Open Order Info</div>
    </x-slot>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-light-primary color-primary">
                <i class="fas fa-info-circle"></i> See basic order details of this open order here
            </div>

            @if(!empty($open_order_details))
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Order Number</strong> <span
                        class="float-end">{{$open_order_details['order_number']}}</span></li>
                <li class="list-group-item"><strong>Order Date</strong> <span class="float-end">{{date('F j,
                        Y',strtotime($open_order_details['order_date']))}}</span></li>
                <li class="list-group-item"><strong>Order Amount</strong> <span
                        class="float-end">${{$open_order_details['order_amount']}}</span></li>
                <li class="list-group-item"><strong>Warehouse</strong> <span
                        class="float-end">{{$open_order_details['warehouse']}}</span></li>
            </ul>

            <br><br>
            <div class="divider">
                <div class="divider-text"><strong>Line Items</strong></div>
            </div>

            <div class="card border-secondary collapse-icon accordion-icon-rotate">
                <div class="card-body">
                    <div class="accordion" id="cardAccordion">
                        <div class="accordion" id="accordionExample">
                            @forelse ($open_order_details['items'] as $key => $item)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{$key}}" aria-expanded="false"
                                        aria-controls="collapseOne">
                                        {{$item['name']}}
                                    </button>
                                </h2>
                                <div id="collapse{{$key}}" class="accordion-collapse collapse"
                                    aria-labelledby="headingOne" data-bs-parent="#accordionExample" style="">
                                    <div class="accordion-body">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item"><strong>Price</strong> <span
                                                    class="float-end">${{$item['price']}}</span>
                                            </li>
                                            <li class="list-group-item"><strong>Category</strong> <span
                                                    class="float-end">{{$item['category']}}</span></li>
                                            <li class="list-group-item"><strong>Quantity</strong> <span
                                                    class="float-end">{{$item['quantity']}}</span>
                                            </li>
                                            <li class="list-group-item"><strong>Description</strong> <span
                                                    class="float-end">{{$item['description']}}</span></li>
                                        </ul>

                                    </div>
                                </div>
                            </div>
                            @empty
                            <center>No line items for this order</center>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>



</x-modal>