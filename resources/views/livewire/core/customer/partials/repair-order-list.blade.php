<div class="card border border-light border-3 shadow-sm mb-4">
    <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
        <a href="{{config('sro.url')}}/dashboard/service-check-ins/create" target="_blank"
            class="btn btn-sm btn-outline-success float-end"><i class="fas fa-plus"></i> New SRO</a>
        <h3 class="h5 mb-2">Customer Repair Orders</h3>
    </div>

    <div class="card-body">
        <livewire:core.customer.repair-orders-table :customer="$sro_customer">
    </div>


</div>