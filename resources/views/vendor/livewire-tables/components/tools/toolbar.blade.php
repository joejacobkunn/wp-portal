@aware(['component', 'tableName'])
@props(['filterGenericData'])

@php
    $tableId = str_replace('.', '-', $component->getName());
@endphp

@if ($component->hasConfigurableAreaFor('before-toolbar'))
    @include($component->getConfigurableAreaFor('before-toolbar'), $component->getParametersForConfigurableArea('before-toolbar'))
@endif

<div @class([
        'd-md-flex justify-content-between mb-3' => $component->isBootstrap(),
        'md:flex md:justify-between mb-4 px-4 md:p-0' => $component->isTailwind(),
    ])
>
    <div @class([
            'd-md-flex' => $component->isBootstrap(),
            'w-full mb-4 md:mb-0 md:w-2/4 md:flex space-y-4 md:space-y-0 md:space-x-2' => $component->isTailwind(),
        ])
    >
        <div x-cloak x-show="!currentlyReorderingStatus">
            @if ($component->hasConfigurableAreaFor('toolbar-left-start'))
                @include($component->getConfigurableAreaFor('toolbar-left-start'), $component->getParametersForConfigurableArea('toolbar-left-start'))
            @endif
        </div>
        
        @if ($component->reorderIsEnabled())
            <x-livewire-tables::tools.toolbar.items.reorder-buttons />
        @endif
        
        @if ($component->searchIsEnabled() && $component->searchVisibilityIsEnabled())
            <x-livewire-tables::tools.toolbar.items.search-field />
        @endif

        @if ($component->filtersAreEnabled() && $component->filtersVisibilityIsEnabled() && $component->hasVisibleFilters())
            <x-livewire-tables::tools.toolbar.items.filter-button :$filterGenericData />
        @endif

        @if ($component->hasConfigurableAreaFor('toolbar-left-end'))
            <div x-cloak x-show="!currentlyReorderingStatus">
                @include($component->getConfigurableAreaFor('toolbar-left-end'), $component->getParametersForConfigurableArea('toolbar-left-end'))
            </div>
        @endif
    </div>

    <div x-cloak x-show="!currentlyReorderingStatus"         
        @class([
            'd-md-flex' => $component->isBootstrap(),
            'md:flex md:items-center space-y-4 md:space-y-0 md:space-x-2' => $component->isTailwind(),
        ])
    >
        @if ($component->hasConfigurableAreaFor('toolbar-right-start'))
            @include($component->getConfigurableAreaFor('toolbar-right-start'), $component->getParametersForConfigurableArea('toolbar-right-start'))
        @endif

        @if ($component->showBulkActionsDropdownAlpine())
            <x-livewire-tables::tools.toolbar.items.bulk-actions />
        @endif
        
        @if ($component->columnSelectIsEnabled())
            <x-livewire-tables::tools.toolbar.items.column-select /> 
        @endif

        @if ($component->paginationIsEnabled() && $component->perPageVisibilityIsEnabled())
            <x-livewire-tables::tools.toolbar.items.pagination-dropdown /> 
        @endif

        @if ($component->hasConfigurableAreaFor('toolbar-right-end'))
            @include($component->getConfigurableAreaFor('toolbar-right-end'), $component->getParametersForConfigurableArea('toolbar-right-end'))
        @endif
    </div>
</div>
@if (
    $component->filtersAreEnabled() &&
    $component->filtersVisibilityIsEnabled() &&
    $component->hasVisibleFilters() &&
    $component->isFilterLayoutSlideDown()
)
    <x-livewire-tables::tools.toolbar.items.filter-slidedown :$filterGenericData />
@endif


@if ($component->hasConfigurableAreaFor('after-toolbar'))
    <div x-cloak x-show="!currentlyReorderingStatus" >
        @include($component->getConfigurableAreaFor('after-toolbar'), $component->getParametersForConfigurableArea('after-toolbar'))
    </div>
@endif

@if ($component->hasFilters())
    @script
    <script>
        (function () {
            let inProcessFlag = 0;
            let firstLoad = true

            if (typeof SlimSelect != 'function') {
                loadScript("https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.1/slimselect.min.js", initSelect);
            }

            alert("S");
            function initSelect(attrValues) {
                if (inProcessFlag) return

                if (! document.querySelector('#datatable-{{ $component->getId() }} select:not([data-ssid],#table-perPage')) return

                inProcessFlag = 1
                document.querySelectorAll('#datatable-{{ $component->getId() }} select:not([data-ssid],#table-perPage').forEach((el) => {
                    el.classList.remove('form-select')
                    if (typeof SlimSelect == 'function') {


                        let id = '#' + el.getAttribute('id')
                        let select = new SlimSelect({ 
                            select: id,
                            allowDeselect: true,
                            onChange: (info) => {
                                
                                if (firstLoad) {
                                    firstLoad = false 
                                    return
                                };

                                let val = info.value
                                if (el.hasAttribute('multiple')) {
                                    val = info.map((v) => v.value)
                                }
                                
                                $wire.set(el.getAttribute('field-key'), val).then(() => {
                                    initSelect()
                                })

                            },
                        })
                        
                        if (firstLoad && el.getAttribute('field-key')) {
                            let values = $wire.get(el.getAttribute('field-key'))
                            select.set(values)
                        }

                        if ( attrValues 
                            && attrValues.hasOwnProperty(el.getAttribute('field-index'))
                            && select.selected() != attrValues[el.getAttribute('field-index')]
                            ) {
                            select.set(attrValues[el.getAttribute('field-index')])
                        }

                    }
                })

                inProcessFlag = 0
            }

            window.addEventListener('{{ $tableId }}:table-filter:emit', (e) => {
                if (! document.querySelector('{{ $tableId }} .table-filter-col .ss-main')) {
                    initSelect(e.detail.value);
                }
            })
        })()
    </script>
    @endscript
@endif