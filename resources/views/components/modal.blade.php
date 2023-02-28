@if($toggle)
<div>
    <div {{ $attributes }} class="modal fade show" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="display: block">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">{{ $title ?? '' }}</h5>
                <button type="button" wire:click="$emit('closeModal')" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            <div class="modal-footer">
                {{ $footer ?? '' }}
            </div>
            </div>
        </div>
    </div>

    <div class="modal-backdrop fade show"
         id="backdrop"
         style="display: block;"></div>
</div>
@endif