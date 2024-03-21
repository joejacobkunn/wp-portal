@props([
    'modal_class' => false,
    'closeEvent' => 'closeModal',
    'modal_body_class' => false,
    'modal_close_class' => false,
    'width' => null,
    'listener' => 'closeModal',
    'toggle' => false,
    'size' => '',
])

<div>
    <div {{ $attributes }} class="modal fade @if($toggle === true) show @endif" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="display: @if($toggle === true)
                 block
         @else
                 none
         @endif;
         ">
        <div class="modal-dialog  modal-{{ $size ?? '' }} {{$modal_class??''}}" style="
         @if(!empty($width))
            width: {{ $width }}px;
            max-width: {{ $width }}px;
         @endif">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">{{ $title ?? '' }}</h5>
                <button type="button" wire:click="$dispatch('{{ $listener }}')" class="btn-close {{$modal_close_class ?? ''}}" data-bs-dismiss="modal" aria-label="Close"></button>
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

    <div class="modal-backdrop fade show"
         id="backdrop"
         style="display: @if($toggle === true)
                 block
         @else
                 none
         @endif;"></div>
</div>