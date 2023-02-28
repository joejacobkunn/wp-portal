<div class="form-group x-moneyinput">

    @php
        $id = str_replace('.', '_', ($id ?? "") . $model . "_currency-field");
    @endphp

    <label>{{ $label ?? '' }}</label>

    <div class="{{ empty($hideIcon) ? 'input-group' : '' }}">
        <span class="input-group-text {{ !empty($hideIcon) ? 'd-none' : '' }}">
            <i class="fas {{ $icon ?? 'fa-dollar-sign' }}"></i>
        </span>
        <input 
            id="{{ $id }}"
            type="text" 
            class="form-control {{ $errors->has($model) ? 'is-invalid' : '' }} {{ $class ?? '' }}" 
            placeholder="{{ $placeholder ?? '' }}"
            value="{{ $value ?? '' }}"
            autocomplete="off"
        >
    </div>

    @if(isset($model))
        @error($model) <span class="text-danger">{{ $message }}</span> @enderror
    @endif
</div>

<script>
    var keepFormat = '{{ $keepFormat ?? false }}'
    var isLazy = '{{ $lazy ?? false }}'
    var moneyInputTimer = moneyInputTimer || null;
    var moneyVal = @this.get('{{ $model }}')
    if (moneyVal) {
        document.getElementById('{{ $id }}').value = formatCurrency(moneyVal);
    }

    function nonFormattedValue(value) {
        return parseFloat(value.replace(/\,/g, '')).toFixed(parseInt('{{ $fractionDigits ?? 2 }}'))
    }

    function formatCurrency(value) {
        var formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            currencyDisplay: 'code',
            maximumFractionDigits: parseInt('{{ $fractionDigits ?? 2 }}'),
        });
            
        return formatter.format(value).replace(/\USD/g, '').trim().replace(/\D00$/, '');
    }

    document.getElementById('{{ $id }}').addEventListener('keydown', function (e) {
        let value = e.target.value.replace(/\,/g, '')
        var x;

        //backspace and arrow keys, commas
        if ([8, 37, 39, 188, 190, 110].includes(e.keyCode) || (e.keyCode == 65 && e.ctrlKey)) {
            return
        }

        //support numbers
        if (parseInt(e.key) != e.key) {
            e.preventDefault();
        }

    });

    function setData(value) {
        if (moneyInputTimer) {
            clearTimeout(moneyInputTimer)
        }

        moneyInputTimer = setTimeout(() => {
            @this.set('{{ $model }}', value)
        }, 600)
    }

    document.getElementById('{{ $id }}').addEventListener('change', function (e) {
        @this.set('{{ $model }}', nonFormattedValue(e.target.value))
    })

    document.getElementById('{{ $id }}').addEventListener('keyup', function (e) {
        let value = e.target.value
        if (!keepFormat) {
            value = value.replace(/\,/g, '')
        }

        if(!isLazy) {
            setData(nonFormattedValue(e.target.value))
        }

        if ([8, 37, 39, 188, 190, 110].includes(e.keyCode) || (e.keyCode == 65 && e.ctrlKey)) {
            return
        }

        if (value) {
            e.target.value = formatCurrency(value);
        }
    })
</script>