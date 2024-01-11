@aware(['component', 'tableName'])

@php
    $searchPopoverTitle = $component->searchPopoverTitle ?? "<i class='fa fa-search me-1'></i>  Search By";
    if (!empty($component->searchPopoverContent)) {
        $searchPopoverContent = $component->searchPopoverContent;
    } else {
        $searchPopoverContent = '<ul class="search-popover-ul">';
        foreach ($component->getSearchableColumns() as $column) {
            $searchPopoverContent .= '<li>'. $column->getTitle() .'</li>';
        }
        $searchPopoverContent .= '</ul>';
    }
@endphp

<div x-cloak x-show="!currentlyReorderingStatus"
    @class([
        'mb-3 mb-md-0 input-group dt-search-div' => $component->isBootstrap(),
        'flex rounded-md shadow-sm' => $component->isTailwind(),
    ])>
        <input
            wire:model{{ $component->getSearchOptions() }}="search"
            placeholder="{{ $component->getSearchPlaceholder() }}"
            type="text"
            {{ 
                $attributes->merge($component->getSearchFieldAttributes())
                ->class([
                    'block w-full border-gray-300 rounded-md shadow-sm transition duration-150 ease-in-out sm:text-sm sm:leading-5 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-none rounded-l-md focus:ring-0 focus:border-gray-300' => $component->isTailwind() && $component->hasSearch() && $component->getSearchFieldAttributes()['default'] ?? true,
                    'block w-full border-gray-300 rounded-md shadow-sm transition duration-150 ease-in-out sm:text-sm sm:leading-5 dark:bg-gray-700 dark:text-white dark:border-gray-600 rounded-md focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50' => $component->isTailwind() && !$component->hasSearch() && $component->getSearchFieldAttributes()['default'] ?? true,
                    'form-control' => $component->isBootstrap() && $component->getSearchFieldAttributes()['default'] ?? true,
                ])
                ->except('default') 
            }}
            data-toggle="popover"
            title="{{ $searchPopoverTitle }}"
            data-content="{{ $searchPopoverContent }}"
        />

        @if ($component->hasSearch())
        <div @class([
                    'd-inline-flex h-100 align-items-center ' => $component->isBootstrap(),
                ])>
                <div
                    wire:click="clearSearch"

                    @class([
                            'btn btn-outline-secondary d-inline-flex h-100 align-items-center' => $component->isBootstrap(),
                            'inline-flex h-full items-center px-3 text-gray-500 bg-gray-50 rounded-r-md border border-l-0 border-gray-300 cursor-pointer sm:text-sm dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600' => $component->isTailwind(),
                        ])
                >
                @if($component->isTailwind())
                <x-heroicon-m-x-mark class='w-4 h-4' />
                @else
                <x-heroicon-m-x-mark style='width:1em; height:1em;'  />
                @endif
                    </div>
            </div>
        @endif


</div>

{{-- <script type="text/javascript">
    [...document.querySelectorAll('.dt-search-div [data-toggle="popover"]')].map(tooltipTriggerEl => {
            new bootstrap.Popover(tooltipTriggerEl, {
                placement: 'top',
                trigger: 'focus',
                content: tooltipTriggerEl.dataset.content,
                html: true,
                template: '<div class="popover dt-search-popover" role="tooltip"><div class="popover-arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>'
            })
        }
    )
</script> --}}