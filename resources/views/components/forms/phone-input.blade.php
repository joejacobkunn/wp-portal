<div class="form-group x-phoneinput">

    @php
        $id = str_replace('.', '_', ($id ?? "") . $model . "_phone-field");
    @endphp

    <label>{{ $label ?? '' }}</label>
    <div class="{{ empty($hideIcon) ? 'input-group' : '' }}">
        <span class="input-group-text {{ !empty($hideIcon) ? 'd-none' : '' }}">
            <i class="fas {{ $icon ?? 'fa-phone' }}"></i>
        </span>
        <input 
            id="{{ $id }}"
            type="text" 
            class="form-control {{ $errors->has($model) ? 'is-invalid' : '' }} {{ $class ?? '' }}" 
            placeholder="{{ $placeholder ?? '' }}"
            autocomplete="off"
        >
    </div>

    @if(isset($model))
        @error($model) <span class="text-danger">{{ $message }}</span> @enderror
    @endif
</div>

<script>
    var keepFormat = '{{ $keepFormat ?? false }}'
    var phoneInputTimer = phoneInputTimer || null;
    var phoneVal = @this.get('{{ $model }}')
    var isLazy = '{{ $lazy ?? false }}'
    
    if (phoneVal) {
        document.getElementById('{{ $id }}').value = formatNumber(phoneVal);
    }


    function nonFormattedValue(value) {
        return value.toString().replace(/\D/g, '')
    }

    function formatNumber(value) {
        let res = value;
        value = nonFormattedValue(value)
        let x;
        
        if (value.length < 3) {
            return res
        } else if (value.length == 3) {
            x = value.match(/(\d{3})/);
            res = '(' + x[1] + ') ';
        } else if (value.length == 6) {
            x = value.match(/(\d{3})(\d{3})/);
            res = '(' + x[1] + ') ' + x[2] + '-';
        } else if (value.length == 10) {
            x = value.match(/(\d{3})(\d{3})(\d{4})/);
            res = '(' + x[1] + ') ' + x[2] + '-' + x[3];
        }

        return res
    }

    document.getElementById('{{ $id }}').addEventListener('keydown', function (e) {
        let value = nonFormattedValue(e.target.value)
        var x;

        //backspace and arrow keys
        if ([8, 9, 37, 39].includes(e.keyCode) || (e.keyCode == 65 && e.ctrlKey)) {
            return
        }

        //support numbers and max 10 nums
        if (parseInt(e.key) != e.key || value.length > 9 ) {
            e.preventDefault();
        }

        e.target.value = formatNumber(e.target.value);
    });

    document.getElementById('{{ $id }}').addEventListener('change', function (e) {
        @this.set('{{ $model }}', nonFormattedValue(e.target.value))
    })

    function setData(value) {
        if (phoneInputTimer) {
            clearTimeout(phoneInputTimer)
        }

        if (value != undefined) {
            phoneInputTimer = setTimeout(() => {
                @this.set('{{ $model }}', value)
            }, 600)
        }
    }

    document.getElementById('{{ $id }}').addEventListener('keyup', function (e) {
        let value = e.target.value
        if (!keepFormat) {
            value = nonFormattedValue(value)
        }

        if(!isLazy) {
            setData(value)
        }
    })
</script>