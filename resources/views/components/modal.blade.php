@props([
    'modal_parent_class' => false,
    'modal_class' => false,
    'closeEvent' => '',
    'modal_body_class' => false,
    'modal_close_class' => false,
    'width' => null,
    'toggle' => false,
    'size' => '',
])
<div x-data="{ open: @entangle($toggle) }">
    <div :class="{ 'show': open }" class="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" {{ $attributes }}  aria-modal="true">
        <div class="modal-dialog  modal-{{ $size ?? '' }} {{$modal_class??''}}" style="
         @if(!empty($width))
            width: {{ $width }}px;
            max-width: {{ $width }}px;
         @endif">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">{{ $title ?? '' }}</h5>
                <button type="button" @click="open = false"
                    @if(!empty($closeEvent)) wire:click="$dispatch('{{ $closeEvent }}')" @endif
                    class="btn-close {{$modal_close_class ?? ''}}" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body {{$modal_body_class ?? ''}}">
                {{ $slot }}
            </div>

            @if(isset($footer))
                <div class="modal-footer">
                    {{ $footer ?? '' }}
                </div>
            @endif
            </div>
        </div>
    </div>

    <div class="modal-backdrop fade"  :class="{ 'show': open }"></div>
</div>