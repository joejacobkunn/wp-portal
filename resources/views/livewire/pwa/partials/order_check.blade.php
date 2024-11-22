<div class="status-div" >
    @if(empty($terminals))
        <div class="alert alert-warning">
            <h5>Error!</h5>
            <p>This location is not currently supported at this time. Contact your admin to configure location in Portal</p>
        </div>
    @else
        <button wire:click="checkPendingPayment" class="btn btn-primary btn-lg px-5 py-3"><i class="fa-solid fa-rotate me-1"></i> Fetch Pending Order Payment</button>
    @endif
</div>