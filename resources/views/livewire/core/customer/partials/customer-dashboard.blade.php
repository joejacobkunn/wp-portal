<div class="row">
    <div class="col-6 col-lg-4 col-md-6">
        <div class="card shadow-md border border-3">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon purple mb-2">
                            <i class="fas fa-user-clock"></i>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">
                            Customer Since
                        </h6>
                        <h6 class="font-extrabold mb-0">{{$customer->customer_since->toFormattedDateString()}}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4 col-md-6">
        <div class="card shadow-md border border-3">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon blue mb-2">
                            <i class="fas fa-history"></i>
                        </div>
                    </div>
                    @if(!empty($customer->last_sale_date))
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Last Sale Date</h6>
                        <h6 class="font-extrabold mb-0">{{$customer->last_sale_date->toFormattedDateString()}}</h6>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4 col-md-6">
        <div class="card shadow-md border border-3">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon green mb-2">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Sales Territory</h6>
                        <h6 class="font-extrabold mb-0">{{$customer->sales_territory}}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>