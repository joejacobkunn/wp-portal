<div class="form-group x-input">

    @php
        $id = ($id ?? "") . $model . "_input-field";
        $isDisabled = !empty($disabled) && $disabled;
    @endphp

    @if(!isset($noLabel))
        <label>{{ $label ?? '' }}</label>
        @if(isset($labelInfoText))
            <a tabindex="0" role="button" data-bs-toggle="popover"
                data-bs-placement="top"
                data-bs-trigger="focus"
                data-bs-content="{{ $labelInfoText }}">
                <i class="fa fa-info-circle"></i>
            </a>
        @endif
    @endif

    <div class="input-group">
        @if(!empty($prependIcon) || !empty($prependText))
        <span class="input-group-text">
            @if(!empty($prependIcon))
                <i class="{{ $prependIcon }}"></i>
            @elseif(!empty($prependText))
                <span>{{ $prependText }}</span>
            @endif
        </span>
        @endif

        <input
            id="{{ $id }}"
            type="{{ $type ?? 'text' }}"
            class="form-control {{ $errors->has($model) ? 'is-invalid' : '' }} {{ $class ?? '' }}"
            placeholder="{{ $placeholder ?? '' }}"
            value="{{ $value ?? '' }}"
            {{  $isDisabled ? "disabled" : "" }}
            wire:model.{{ (!empty($defer) ? 'defer' : (!empty($live) ? 'live.debounce.150ms': 'lazy')) }}="{{ $model ?? '' }}"
            wire:keyup="{{ $keyup ?? '' }}"
            maxlength="{{ isset($maxLength) ?  $maxLength : -1 }}"
            {!!  !empty($enterAction) ? 'wire:keydown.enter="'. $enterAction .'"' : '' !!}
            autocomplete="{{ !empty($autocompleteOff) ? 'off' : 'on' }}"
            >

        @if(!empty($appendIcon) || !empty($appendText))
        <span class="input-group-text">
            @if(!empty($appendIcon))
                <i class="{{ $appendIcon }}"></i>
            @elseif(!empty($appendText))
                <span>{{ $appendText }}</span>
            @endif
        </span>
        @endif
    </div>

    @if(!empty($hint))
    <small class="form-text text-muted">{{ $hint }}</small>
    @endif


    @if(isset($model))
        @error($model) <span class="text-danger">{{ $message }}</span> @enderror
    @endif
</div>
