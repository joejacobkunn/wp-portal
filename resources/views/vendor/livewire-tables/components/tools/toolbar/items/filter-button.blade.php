@aware(['component', 'tableName'])
@props(['filterGenericData'])

@php
    $tableId = str_replace('.', '-', $component->getName());
@endphp

<div x-cloak x-show="!currentlyReorderingStatus" 
    x-data="{
        filterComponents: @entangle('filterComponents'),
        sendEvent(filterComponents) {
            window.dispatchEvent(new CustomEvent('{{ $tableId }}:table-filter:emit', {
                detail: {
                    value: Alpine.raw(filterComponents)
                }
            }))
        }
    }"
    x-init="() => {
        sendEvent(filterComponents);
    },
    $watch('filterComponents', (data) => {
        sendEvent(filterComponents);
    })"
                @class([
                    'ml-0 ml-md-2 mb-3 mb-md-0' => $component->isBootstrap4(),
                    'ms-0 ms-md-2 mb-3 mb-md-0' => $component->isBootstrap5() && $component->searchIsEnabled(),
                    'mb-3 mb-md-0' => $component->isBootstrap5() && !$component->searchIsEnabled(),
                ])
>
    <div
        @if ($component->isFilterLayoutPopover())
            x-data="{ filterPopoverOpen: false }"
            x-on:keydown.escape.stop="if (!this.childElementOpen) { filterPopoverOpen = false }"
            x-on:mousedown.away="if (!this.childElementOpen) { filterPopoverOpen = false }"
        @endif
        @class([
            'btn-group d-block d-md-inline' => $component->isBootstrap(),
            'relative block md:inline-block text-left' => $component->isTailwind(),
        ])
    >
        <div class="filter-down-div">
            <button
                type="button"
                @class([
                    'btn dropdown-toggle d-block w-100 d-md-inline' => $component->isBootstrap(),
                    'inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600' => $component->isTailwind(),
                ])
                @if ($component->isFilterLayoutPopover()) x-on:click="filterPopoverOpen = !filterPopoverOpen"
                    aria-haspopup="true"
                    x-bind:aria-expanded="filterPopoverOpen"
                    aria-expanded="true"
                @endif
                @if ($component->isFilterLayoutSlideDown()) x-on:click="filtersOpen = !filtersOpen" @endif
            >
                @lang('Filters')

                @if ($count = $component->getFilterBadgeCount())
                    <span @class([
                            'badge badge-info' => $component->isBootstrap(),
                            'ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-indigo-100 text-indigo-800 capitalize dark:bg-indigo-200 dark:text-indigo-900' => $component->isTailwind(),
                        ])>
                        {{ $count }}
                    </span>
                @endif

                @if($component->isTailwind())
                    <x-heroicon-o-funnel class="-mr-1 ml-2 h-5 w-5" />
                @else
                <span @class([
                    'caret' => $component->isBootstrap(),
                ])></span>
                @endif

            </button>
        </div>

        @if ($component->isFilterLayoutPopover())
            <x-livewire-tables::tools.toolbar.items.filter-popover :$filterGenericData />
        @endif

    </div>


    @script
    <script>
        (function () {
            let inProcessFlag = 0;
            let firstLoad = true

            if (typeof SlimSelect != 'function') {
                loadScript("https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.1/slimselect.min.js", initSelect);
            }

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
</div>
