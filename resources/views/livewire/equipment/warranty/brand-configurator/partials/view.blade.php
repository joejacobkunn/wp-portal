<div>
    <div class="card border-light shadow-sm mb-4">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0" :key="'bew' . time()">
            <livewire:component.action-button :actionButtons="$actionButtons" :key="'comments' . time()">
            <h3 class="h5 mb-0"><i class="fas fa-bars me-1"></i> Overview</h3>
        </div>

        <div class="card-body">


            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Brand</h3>
                        <p class="small pe-4">{{ $warranty->brand->name }}</p>
                    </div>
                </li>

                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Registraion URL</h3>
                        <p class="small pe-4">{{  $warranty->registration_url }}</p>
                    </div>


                </li>

                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Product Lines</h3>
                        @foreach ($this->productLines as $key => $item )
                        <p class="small pe-4">{{  $item}}</p>
                        @endforeach

                    </div>
                </li>
                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Require a proof of registration attachment</h3>
                        <p class="small pe-4">{{ $this->warranty->require_proof_of_reg ? 'Yes' : 'No'}}</p>
                    </div>
                </li>

                <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                    <div>
                        <h3 class="h6 mb-1">Created At</h3>
                        <p class="small pe-4">{{ $this->warranty->created_at?->format(config('app.default_datetime_format')) ;
                            }}</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
