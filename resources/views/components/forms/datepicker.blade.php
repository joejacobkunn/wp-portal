<div class="x-datepicker">
    @php
        $id = str_replace('.', '_', ($id ?? "") . $model . "_datepicker");
        $format = (!empty($format) ? $format : (!empty($enableTime) ? 'Y-m-d H:i' : 'Y-m-d'))
    @endphp

    <label>{{ $label ?? '' }}</label>

    <div class="{{ empty($hideIcon) ? 'input-group' : '' }}">
        <span class="input-group-text {{ !empty($hideIcon) ? 'd-none' : '' }}">
            <i class="fas {{ $icon ?? 'fa-calendar-day' }}"></i>
        </span>
        <input 
            type="{{ $type ?? 'text' }}"
            id="{{ $id }}"
            class="form-control {{ $errors->has($model) ? 'is-invalid' : '' }} {{ $class ?? '' }}" 
            placeholder="{{ $placeholder ?? '' }}"
            value="{{ $value ?? '' }}"
            wire:model.{{ !empty($lazy) ? 'lazy' : 'debounce.500ms' }}="{{ $model ?? '' }}"
            autocomplete="off"
        >
    </div>

    @if(isset($model))
        @error($model) <span class="text-danger">{{ $message }}</span> @enderror
    @endif
</div>

<script>
    flatpickr("#{{ $id }}", {
        enableTime: Boolean('{{ !empty($enableTime) }}'),
        dateFormat: "{{ $format }}",
        minDate: "{{ $minDate ?? '' }}",
        maxDate: "{{ $maxDate ?? '' }}",
    });
</script>