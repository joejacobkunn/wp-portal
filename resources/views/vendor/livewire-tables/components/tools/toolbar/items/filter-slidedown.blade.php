@aware(['component', 'tableName'])
@props(['filterGenericData'])

<div x-cloak x-show="!currentlyReorderingStatus && filtersOpen" 
    @class([
        'container table-filter-div' => $component->isBootstrap(),
    ])
    @if($component->isTailwind())
    x-transition:enter="transition ease-out duration-100"
    x-transition:enter-start="transform opacity-0"
    x-transition:enter-end="transform opacity-100"
    x-transition:leave="transition ease-in duration-75"
    x-transition:leave-start="transform opacity-100"
    x-transition:leave-end="transform opacity-0"
    @endif
>
    @foreach ($component->getFiltersByRow() as $filterRowIndex => $filterRow)
        <div
            @class([
                'row col-12' => $component->isBootstrap(),
                'grid grid-cols-12 gap-6 px-4 md:p-0 mb-6' => $component->isTailwind(),
            ])
            row="{{ $filterRowIndex }}"
        >
            @foreach ($filterRow as $filter)
                <div
                    @class([
                        'space-y-1 mb-4' => 
                            $component->isBootstrap(),
                        'col-12 col-sm-9 col-md-6 col-lg-3' => 
                            $component->isBootstrap() && 
                            !$filter->hasFilterSlidedownColspan(),
                        'col-12 col-sm-6 col-md-6 col-lg-3' =>
                            $component->isBootstrap() &&
                            $filter->hasFilterSlidedownColspan() &&
                            $filter->getFilterSlidedownColspan() == 2,
                        'col-12 col-sm-3 col-md-3 col-lg-3' =>
                            $component->isBootstrap() &&
                            $filter->hasFilterSlidedownColspan() &&
                            $filter->getFilterSlidedownColspan() == 3,
                        'col-12 col-sm-1 col-md-1 col-lg-1' =>
                            $component->isBootstrap() &&
                            $filter->hasFilterSlidedownColspan() &&
                            $filter->getFilterSlidedownColspan() == 4,
                        'space-y-1 col-span-12' => 
                            $component->isTailwind(),
                        'sm:col-span-6 md:col-span-4 lg:col-span-2' => 
                            $component->isTailwind() && 
                            !$filter->hasFilterSlidedownColspan(),
                        'sm:col-span-12 md:col-span-8 lg:col-span-4' =>
                            $component->isTailwind() &&
                            $filter->hasFilterSlidedownColspan() &&
                            $filter->getFilterSlidedownColspan() == 2,
                        'sm:col-span-9 md:col-span-4 lg:col-span-3' =>
                            $component->isTailwind() &&
                            $filter->hasFilterSlidedownColspan() &&
                            $filter->getFilterSlidedownColspan() == 3,
                    ])
                    id="{{ $tableName }}-filter-{{ $filter->getKey() }}-wrapper"
                >
                    {{ $filter->setGenericDisplayData($filterGenericData)->render() }}
                </div>
            @endforeach
        </div>
    @endforeach


    @script
    <script>
        (function () {
            let inProcessFlag = 0;
            let firstLoad = true

            if (typeof SlimSelect == 'function') {
                setTimeout(() => {
                    initSelect()
                }, 50)
            } else {
                loadScript("https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.1/slimselect.min.js", initSelect);
            }

            function initSelect() {
                if (inProcessFlag) return

                if (! document.querySelector('#datatable-{{ $component->getId() }} .table-filter-div select')) return

                inProcessFlag = 1
                document.querySelectorAll('#datatable-{{ $component->getId() }} .table-filter-div select').forEach((el) => {
                    el.classList.remove('form-select')
                    if (typeof SlimSelect == 'function') {


                        let id = '#' + el.getAttribute('id')
                        let select = new SlimSelect({ 
                            select: id,
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
                    }
                })

                inProcessFlag = 0
            }

        })()
    </script>
    @endscript
</div>
