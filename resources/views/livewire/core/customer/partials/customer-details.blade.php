<div class="card border border-light border-3 shadow-sm mb-4">
    <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
        <h4 class="mb-2">Customer Details</h4>
    </div>

    <div class="card-body">
        <div class="list-group list-group-horizontal-sm mb-1 text-center" role="tablist">
            <a class="list-group-item" id="list-sunday-list" data-bs-toggle="list" href="#list-sunday" role="tab"
                aria-selected="true"><strong>Name</strong> : {{
                $customer->name ?? '-' }}</a>
            <a class="list-group-item clipboard" data-clipboard-text="{{$customer->getFullAddress()}}"
                id="list-sunday-list" data-bs-toggle="list" href="#list-sunday" role="tab"
                aria-selected="true"><strong>Type</strong> : {{
                $customer->customer_type ?? '-' }}</a>
            <a class="list-group-item clipboard" data-clipboard-text="{{$customer->getFullAddress()}}"
                id="list-monday-list" data-bs-toggle="list" href="#list-monday" role="tab" aria-selected="false"
                tabindex="-1"><strong>Address</strong> : {{ $customer->getFullAddress() ?:
                'N/A' }}</a>
            @if($customer->phone)
            <a class="list-group-item clipboard" data-clipboard-text="{{$customer->phone}}" id="list-tuesday-list"
                data-bs-toggle="list" href="#list-tuesday" role="tab" aria-selected="false"
                tabindex="-1"><strong>Phone</strong> : {{ $customer->phone }}
            </a>
            @endif

            @if($customer->email)
            <a class="list-group-item" id="list-tuesday-list" data-bs-toggle="list" href="#list-tuesday" role="tab"
                aria-selected="false" tabindex="-1"><strong>E-Mail</strong> : {{ $customer->email }}
            </a>
            @endif

        </div>

    </div>
</div>