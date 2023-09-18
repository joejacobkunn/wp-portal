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

                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" data-bs-toggle="pill"
                            data-bs-target="#pills-open-orders-all" type="button" role="tab">All Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-open-orders-taken-by-me"
                            type="button" role="tab">Taken By Me</a>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-open-orders-all" role="tabpanel"
                        aria-labelledby="pills-home-tab">
                        @include('livewire.core.customer.partials.open-orders-all')
                    </div>
                </div>

                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show" id="pills-open-orders-taken-by-me" role="tabpanel"
                        aria-labelledby="pills-home-tab">
                        @include('livewire.core.customer.partials.open-orders-taken-by-me')
                    </div>
                </div>


            </div>
            <div class="tab-pane fade" id="pills-other-orders" role="tabpanel" aria-labelledby="pills-profile-tab">

                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" data-bs-toggle="pill"
                            data-bs-target="#pills-past-orders-all" type="button" role="tab">All Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="pill" data-bs-target="#pills-past-orders-taken-by-me"
                            type="button" role="tab">Taken By Me</a>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-past-orders-all" role="tabpanel"
                        aria-labelledby="pills-home-tab">
                        @include('livewire.core.customer.partials.past-orders-all')
                    </div>
                </div>

                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show" id="pills-past-orders-taken-by-me" role="tabpanel"
                        aria-labelledby="pills-home-tab">
                        @include('livewire.core.customer.partials.past-orders-taken-by-me')
                    </div>
                </div>
            </div>
        </div>

    </div>


</div>