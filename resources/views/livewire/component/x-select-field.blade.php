<div>
    <div id="div-{{ $fieldDomId }}" class="x-multiselect">
        <select id="{{ $fieldDomId }}" name="{{ $fieldName }}" {{ $multiple ? 'multiple' : ''}} {{ $disabled ? 'disabled' : ''}}>
            @if($selectAllOption)
                <optgroup label="Select All" class="select-all-item">
            @endif
            @foreach ($items as $item)
                <option value="{{ $item['value'] }}"
                    {{ !empty($item['default']) && !$multiple && empty($selectedItem) ? 'selected' : '' }}
                    {{ !empty($item['disabled']) && !$defaultOptionSelectable ? 'disabled' : '' }}
                    {{ in_array($item['value'], $selectedItem) ? 'selected' : '' }}>{{ $item['text'] }}</option>
            @endforeach
            @if($selectAllOption)
            </optgroup>
            @endif
        </select>
    </div>

    <script>
        var isMultiple = false
        @if($multiple)
            isMultiple = true
        @endif

        var selectTimer = selectTimer || null;

        if (typeof SlimSelect == 'function') {
            initPlugin()
        } else {
            loadScript("https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.1/slimselect.min.js", initPlugin);
        }

        function initPlugin() {
            var selectField = new SlimSelect({
                select: '#{{ $fieldDomId }}',
                allowDeselect: Boolean('{{ $allowDeselect }}'),
                deselectLabel: '<i class="fas fa-times"></i>',
                placeholder: '{{ $placeholder }}',
                showSearch: Boolean('{{ empty($hideSearch) }}'),
                searchText: '{{ !empty($noResultText) ? $noResultText : "No Results" }}',
                searchPlaceholder: '{{ !empty($searchPlaceholder) ? $searchPlaceholder : "Search" }}',
                closeOnSelect: isMultiple ? false : true,
                showContent: '{{ !empty($direction) && in_array($direction, ["auto", "up", "down"]) ? $direction : "auto" }}',
                selectByGroup: isMultiple ? true : false,
                onChange: (info) => {
                    let values = !isMultiple ? info.value : info.map((v) => v.value)
                    setData(values)
                }
            })

            function setData (values) {

                setTimeout(() => {
                    if (!document.querySelector('select[name={{ $fieldName }}]').closest('.x-multiselect').querySelector('.input-loading')) {
                        let loaderDom = document.createElement('i');
                        loaderDom.classList.add('fa', 'fa-spinner', 'fa-spin', 'input-loading')
                        document.querySelector('select[name={{ $fieldName }}]').closest('.x-multiselect').querySelector('.ss-main').appendChild(loaderDom)
                    }
                }, 200)

                if (selectTimer) {
                    clearTimeout(selectTimer)
                }

                selectTimer = setTimeout(() => {
                    document.dispatchEvent(new CustomEvent("browser:{{ $fieldId }}:changed", { "detail": {
                        value: values
                    } }));

                    @this.setValue(values)
                    selectField.data.setSelected(values)
                }, 600)
            }

            @if($selectAllOption)

            let selectAllFlag = false
            let rootElement = document.getElementsByClassName(selectField.config.id)[0]
            rootElement.addEventListener('click', (event) => {
                let targetElement = event.target
                if(targetElement.classList.contains('fa-times') || targetElement.classList.contains('ss-option')) {
                    setTimeout(() => {
                        initializeListeners();
                    }, 300)
                }
            }, true);

            initializeListeners();
            function initializeListeners() {
                let allFieldCount = selectField.data.data[0].options.length
                if (selectField.data.searchValue) {
                    allFieldCount = selectField.data.data[0].options.filter((v) => v.text.includes(selectField.data.searchValue)).length
                }

                let selectAllFlag = allFieldCount == selectField.data.data[0].options.filter((v) => v.selected).length
                var element = document.getElementsByClassName(selectField.config.id)[0].getElementsByClassName('ss-optgroup-label')[0]
                if (!selectAllFlag) {
                    element.innerHTML = "Select All"
                    element.classList.remove('deselect-option')
                } else {
                    element.innerHTML = "Deselect All"
                    element.classList.add('deselect-option')
                }

                element.addEventListener("click", function () {
                    let element = document.getElementsByClassName('ss-optgroup-label')[0]
                    if (!selectAllFlag) {
                        selectAllFlag = true
                        selectField.close()
                        initializeListeners()
                    } else {
                        selectField.data.setSelected('')
                        selectField.render()
                        setData([])
                        selectAllFlag = false
                        initializeListeners()
                    }
                })
            }

                @endif
        }
    </script>

    <script wire:ignore>
       (function () {
        var element = document.querySelector("select[name={{ $fieldName }}]")

        if (!window.listeners) {
            window.listeners = [];
        }

        if (!element.getAttribute("emitListener") && !window.listeners.includes('{{ $listener }}' + ':emit')) {
            window.addEventListener('{{ $listener }}' + ':emit', callbackListener)
            element.setAttribute("emitListener", true)
            window.listeners.push('{{ $listener }}' + ':emit')
        }
    })()

    function callbackListener(event) {
        if (document.querySelector('select[name={{ $fieldName }}]') && document.querySelector('select[name={{ $fieldName }}]').closest('.x-multiselect').querySelector('.input-loading')) {
            document.querySelector('select[name={{ $fieldName }}]').closest('.x-multiselect').querySelector('.input-loading').remove();
        }
    }
    </script>
</div>
