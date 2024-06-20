<div>
    @php
        $attributes = [
            'id' => ($id ?? ''), 
            'disabled' => !empty($disabled) ? true : false,
        ];

        if (!empty($model)) {
            $attributes['wire:model.lazy'] = $model;
        }

    @endphp
    <label class="checkbox-container {{ $class ?? '' }}">
        {!! Form::checkbox($name ?? '', $value ?? '', $checked ?? null, $attributes) !!}
        <span class="checkmark"></span>
        {!! $label ?? '' !!}

        @if(!empty($hint))
        <sup><i class="fas fa-question-circle"
            data-bs-toggle="tooltip"
            data-bs-custom-class="custom-tooltip"
            data-bs-placement="right" 
            data-bs-title="{{ $hint  }}"></i></sup>
        @endif
    </label>
</div>