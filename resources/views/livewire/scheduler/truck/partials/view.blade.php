<div class="row">
    <div class="col-8 col-md-12 col-xxl-12">
        <div class="card border-light shadow-sm mb-4">
            <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                <h3 class="h5 mb-0"><i class="fas fa-bars me-1"></i> Overview</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                <div>
                                    <h3 class="h6 mb-1">Name</h3>
                                    <p class="small pe-4">{{ $truck->truck_name }}</p>
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                <div>
                                    <h3 class="h6 mb-1">Driver</h3>
                                    <p class="small pe-4">{{ $truck->cubic_storage_space }}</p>
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                <div>
                                    <h3 class="h6 mb-1">Year</h3>
                                    <p class="small pe-4">{{ $truck->year }}</p>
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                <div>
                                    <h3 class="h6 mb-1">Color</h3>
                                    <p class="small pe-4">{{ $truck->color }}</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                <div>
                                    <h3 class="h6 mb-1">VIN#</h3>
                                    <p class="small pe-4">{{ $truck->vin_number }}</p>
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                <div>
                                    <h3 class="h6 mb-1">Model & Make</h3>
                                    <p class="small pe-4">{{ $truck->model_and_make }}</p>
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                                <div>
                                    <h3 class="h6 mb-1">Cubic Storage Space</h3>
                                    <p class="small pe-4">{{ $truck->cubic_storage_space }}</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
