<div class="form-group">

    @php
        $id = ($id ?? "") . $model . "_textarea-field";
    @endphp

    <label>{{ $label ?? '' }}</label>
    
    {!! Form::textarea($id, $model, [
        'class' => 'form-control ' . ($errors->has($model) ? 'is-invalid' : '') . ' ' .($class ?? ''), 
        'placeholder' => ($placeholder ?? ''), 
        'rows' => ($rows ?? 2),
        'wire:model.' . (!empty($lazy) ? 'lazy': 'debounce.500ms') => $model ?? '',
        ]) !!}

    @if(!empty($hint))
    <div>
        <small class="form-text text-muted">{{ $hint }}</small>
    </div>
    @endif

    @if(isset($model))
        @error($model) <span class="text-danger">{{ $message }}</span> @enderror
    @endif
</div>