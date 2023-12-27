@aware(['component', 'tableName'])

@php

$id = $id ?? 'DT';
    $customAttributes = [
        'wrapper' => $this->getTableWrapperAttributes(),
        'table' => $this->getTableAttributes(),
        'thead' => $this->getTheadAttributes(),
        'tbody' => $this->getTbodyAttributes(),
    ];
@endphp

@if ($component->isTailwind())
    <div
        wire:key="{{ $tableName }}-twrap"
        id="{{ $id }}"
        {{ $attributes->merge($customAttributes['wrapper'])
            ->class(['shadow overflow-y-auto border-b border-gray-200 dark:border-gray-700 sm:rounded-lg' => $customAttributes['wrapper']['default'] ?? true])
            ->except('default') }}
    >
        <table
            wire:key="{{ $tableName }}-table"
            {{ $attributes->merge($customAttributes['table'])
                ->class(['min-w-full divide-y divide-gray-200 dark:divide-none' => $customAttributes['table']['default'] ?? true])
                ->except('default') }}
        >
            <thead wire:key="{{ $tableName }}-thead"
                {{ $attributes->merge($customAttributes['thead'])
                    ->class(['bg-gray-50 dark:bg-gray-800' => $customAttributes['thead']['default'] ?? true])
                    ->except('default') }}
            >
                <tr>
                    {{ $thead }}
                </tr>
            </thead>

            <tbody
                wire:key="{{ $tableName }}-tbody"
                id="{{ $tableName }}-tbody"
                {{ $attributes->merge($customAttributes['tbody'])
                        ->class(['bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-none' => $customAttributes['tbody']['default'] ?? true])
                        ->except('default') }}
            >
                {{ $slot }}
            </tbody>

            @if (isset($tfoot))
                <tfoot wire:key="{{ $tableName }}-tfoot">
                    {{ $tfoot }}
                </tfoot>
            @endif
        </table>
    </div>
@elseif ($component->isBootstrap())
    <div wire:key="{{ $tableName }}-twrap"
        id="{{ $id }}"
        {{ $attributes->merge($customAttributes['wrapper'])
            ->class(['table-responsive' => $customAttributes['wrapper']['default'] ?? true])
            ->except('default') }}
    >
        <table
            wire:key="{{ $tableName }}-table"
            {{ $attributes->merge($customAttributes['table'])
                ->class(['laravel-livewire-table table' => $customAttributes['table']['default'] ?? true])
                ->except('default')
            }}
        >
            <thead
                wire:key="{{ $tableName }}-thead"
                {{ $attributes->merge($customAttributes['thead'])
                    ->class(['' => $customAttributes['thead']['default'] ?? true])
                    ->except('default') }}
            >
                <tr>
                    {{ $thead }}
                </tr>
            </thead>

            <tbody
                wire:key="{{ $tableName }}-tbody"
                id="{{ $tableName }}-tbody"
                {{ $attributes->merge($customAttributes['tbody'])
                        ->class(['' => $customAttributes['tbody']['default'] ?? true])
                        ->except('default') }}
            >
                {{ $slot }}
            </tbody>

            @if (isset($tfoot))
                <tfoot wire:key="{{ $tableName }}-tfoot">
                    {{ $tfoot }}
                </tfoot>
            @endif
        </table>
    </div>
@endif

@if(!empty($component->showRowInfo))
<script type="text/javascript">
    [...document.querySelectorAll('#{{ $id }} [data-toggle="row-popover"]')].map(tooltipTriggerEl => {

            tooltipTriggerEl.addEventListener('mouseover', function(e) {
                if (document.querySelector('.dt-row-popover')) {
                    document.querySelector('.dt-row-popover').style.left = e.pageX + 'px'
                    document.querySelector('.dt-row-popover').style.top = (e.pageY  + 15)+ 'px'
                } else {
                    var divElement = document.createElement( "div" )
                    divElement.classList.add('popover', 'dt-row-popover')
                    divElement.innerHTML = '<div class="popover-body">'+ tooltipTriggerEl.dataset.content +'</div>'
                    document.querySelector('body').appendChild(divElement)
                    document.querySelector('.dt-row-popover').style.left = e.pageX + 'px'
                    document.querySelector('.dt-row-popover').style.top = (e.pageY + 15) + 'px'
                }
            });

            tooltipTriggerEl.addEventListener('mouseleave', function(e) {
                if (document.querySelector('.dt-row-popover')) {
                    document.querySelector('.dt-row-popover').remove()
                } 
            });
        }
    )
</script>
@endif