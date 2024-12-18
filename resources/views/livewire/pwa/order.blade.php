
<div>

    <div wire:init="loadTerminals">
        @if($pageLoaded)

            @if(! $orderInProcess)
                @include('livewire.pwa.partials.order_check')
            @else
                @include('livewire.pwa.partials.order_process')
            @endif

        @else
            <div class="status-div">
                <i class="fas fa-spinner me-2 fa-spin"></i> Loading Terminals
            </div>
        @endif
    </div>

</div>