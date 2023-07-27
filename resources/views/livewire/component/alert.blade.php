<div class="alert alert-light-{{ $level }} d-flex justify-content-between w-100 align-items-center">
    <div>
        <i class="far {{ $messageIcon }}" aria-hidden="true"></i>
        {!! $message !!}
    </div>
    @if ($hasAction)
    <div class="button-container">

        <button wire:click="callAction" type="button" class="btn btn-sm btn-outline-{{ $actionButtonClass }} float-end ">
            <div wire:loading="">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </div>
            {{ $actionButtonName }}
        </button>
    </div>
    @endif
</div>
