<div class="form-group x-input">

    @php
        $id = ($id ?? "") . $model . "_input-field";
    @endphp

    <label>{{ $label ?? '' }}</label>

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
            wire:model.{{ !empty($lazy) ? 'lazy' : 'debounce.500ms' }}="{{ $model ?? '' }}"
            autocomplete="{{ $autocompleteOn ?? 'off' }}"
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
    <p class="mb-0"><small class="text-muted">{{ $hint }}</small></p>
    @endif
    
    @if(isset($model))
        @error($model) <span class="text-danger">{{ $message }}</span> @enderror
    @endif
</div>