 <div class="col-md-12 mb-3">
    <div class="card border shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Product Details</h5>
            <small class="badge bg-light-warning">product/brand/description</small>
            <div wire:loading.class="opacity-50">
                <ul class="list-group mb-2">
                        <li class="list-group-item">{{ $product.'/ '.$brand.'/ '.$description }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
