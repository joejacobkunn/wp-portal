<div class="x-datepicker">
    @php
        $id = str_replace('.', '_', ($id ?? "") . $model . "_datepicker");
        $format = (!empty($format) ? $format : (!empty($enableTime) ? 'Y-m-d H:i' : 'Y-m-d'));
        $isDisabled = !empty($disabled) && $disabled;
        $readonly = !empty($readonly) ? $readonly : '';
    @endphp

    <div class="{{ empty($hideIcon) ? 'input-group' : '' }}" id="div-{{ $id }}">
        <span class="input-group-text {{ !empty($hideIcon) ? 'd-none' : '' }}">
            <i class="fas {{ $icon }}"></i>
        </span>
        <input 
            type="{{ $type ?? 'text' }}"
            id="{{ $id }}"
            autocomplete="off"
            placeholder="{{ $placeholder }}"
            value="{{ $value }}"
            class="form-control {{ !$isDisabled ? 'bg-white' : '' }} {{ $errors->has($model) ? 'is-invalid' : '' }} {{ $class ?? '' }}" 
            wire:model.live="value"
            {{  $isDisabled ? "disabled" : "" }}
            {{  $readonly ? "readonly" : "" }}
        >

        @if(!empty($clearable))
        <span class="input-group-text clear-btn px-1" title="Clear">
            <i class="fas fa-times text-danger opacity-50"></i>
        </span>
        @endif
    </div>
</div>

@assets
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" data-navigate-once>
<script src="https://cdn.jsdelivr.net/npm/flatpickr" data-navigate-once></script>
@endassets

@script
<script>
    (function () {
        let pickerType = '{{ $type }}';
        let datepicker;
        if (pickerType == 'datepicker') {
            datepicker = flatpickr("#{{ $id }}", {
                enableTime: Boolean('{{ !empty($enableTime) }}'),
                dateFormat: "{{ $format }}",
                minDate: "{{ $minDate ?? '' }}",
                maxDate: "{{ $maxDate ?? '' }}",
            });
        } else if (pickerType == 'timepicker') {
            datepicker = flatpickr("#{{ $id }}", {
                enableTime: true,
                dateFormat: "{{ $format }}",
                noCalendar:  true,
                time_24hr: Boolean('{{ empty($twelveHourClock) }}')
            });
        }

        document.addEventListener('{{ $id }}:datepicker:change', function (e) {
            let data = e.detail

            if (data.minDate) {
                datepicker.set('minDate', data.minDate)
            }

            if (data.maxDate) {
                datepicker.set('maxDate', data.maxDate)                
            }
        })

        document.getElementById('{{ $id }}').addEventListener('keydown', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
            }
        });

        document.getElementById('div-{{ $id }}').addEventListener('click', function (e) {
            if (e.target.matches('.clear-btn') || e.target.matches('.clear-btn i')) {
                $wire.set('value', null)
            }
        });
    })()
</script>
@endscript