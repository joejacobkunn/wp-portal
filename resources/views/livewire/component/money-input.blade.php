<div class="x-moneyinput">
    @php
        $id = str_replace('.', '_', ($id ?? "") . $model . "_currency-field");
        $isDisabled = !empty($disabled) && $disabled;
    @endphp
    
    <div class="{{ empty($hideIcon) ? 'input-group' : '' }}">
        <span class="input-group-text {{ !empty($hideIcon) ? 'd-none' : '' }}">
            <i class="fas {{ $icon }}"></i>
        </span>
        <input 
            id="{{ $id }}"
            type="text" 
            class="form-control" 
            placeholder="{{ $placeholder ?? '' }}"
            autocomplete="off"
            {{  $isDisabled ? "disabled" : "" }}
            value="{{ $value }}">
    </div>
</div>

@script
<script>
    (function () {
        var keepFormat = '{{ $keepFormat ?? false }}'
        var isLazy = '{{ $lazy ?? false }}'
        var moneyInputTimer = moneyInputTimer || null;
        var moneyVal = document.getElementById('{{ $id }}').value
        if (moneyVal) {
            document.getElementById('{{ $id }}').value = formatCurrency(moneyVal);
        }

        function nonFormattedValue(value) {
            return parseFloat(value.replace(/\,/g, '')).toFixed(parseInt('{{ $fractionDigits }}'))
        }

        function formatCurrency(value) {
            var formatter = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
                currencyDisplay: 'code',
                maximumFractionDigits: parseInt('{{ $fractionDigits }}'),
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

        function setData(formattedValue) {
            if (moneyInputTimer) {
                clearTimeout(moneyInputTimer)
            }

            let value = nonFormattedValue(formattedValue);
            if (!isNaN(value)) {
                moneyInputTimer = setTimeout(() => {
                    $wire.updateValueInput(value)
                }, 600)
            }
        }

        document.getElementById('{{ $id }}').addEventListener('change', function (e) {
            let value = nonFormattedValue(e.target.value);
            if (!isNaN(value)) {
                $wire.updateValueInput(value)
            }
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
    })()
</script>
@endscript