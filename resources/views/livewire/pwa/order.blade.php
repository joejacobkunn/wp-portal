
<div>

    @section('page_title', 'Order Payment')

    <div class="status-div" wire:init="loadTerminals">
        @if($pageLoaded)

            @if(0 && empty($terminals))
                <div class="alert alert-warning">
                    <h5>Error!</h5>
                    <p>This location is not currently supported at this time. Contact your admin to configure location in Portal</p>
                </div>
            @else
                <button class="btn btn-primary btn-lg px-5 py-3"><i class="fa-solid fa-rotate me-1"></i> Fetch Pending Order Payment</button>
            @endif
        @else
            <i class="fas fa-spinner me-2 fa-spin"></i> Loading Terminals
        @endif
    </div>

</div>