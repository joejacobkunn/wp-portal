@php
    $icon = empty($noIcon) ? ($icon ?? 'fa-save') : '';
    $disabled = !empty($disabled) ? 1 : 0;
@endphp

<div class="d-inline-block {{ $divClass ?? '' }}" x-data="{ activeRequests: 0 }" x-init="() => {
    Livewire.hook('request', ({ uri, options, payload, respond, succeed, fail }) => {
    
        activeRequests++;
    
        respond(({ status, response }) => {
            setTimeout(() => {
                activeRequests--;
            }, 600)
        })
    
        succeed(({ status, json }) => {
        })
    
        fail(({ status, content, preventDefault }) => {
        })
    })
}
">
    <button type="button" class="btn {{ $class ?? '' }}" wire:loading.attr="disabled" wire:click="{{ $method }}" {{ $disabled ? 'disabled' : '' }} :disabled="{{ $disabled }} || activeRequests > 0">
        <div wire:loading wire:target="{{ $method }}">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </div>
        @if($icon) <i class="fas {{ $icon }} me-1"></i> @endif
        {{ $text ?? 'Save' }}
    </button>
</div>