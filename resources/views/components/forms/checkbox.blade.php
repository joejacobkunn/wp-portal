<div>
    <label>
        {!! Form::checkbox($name ?? '', $value ?? '', $checked ?? null, ['id' => ($id ?? 'checkbox'), 'wire:model.lazy' => ($model ?? '') ]) !!}
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