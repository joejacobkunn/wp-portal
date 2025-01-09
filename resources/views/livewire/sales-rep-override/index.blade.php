<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Sales Rep Override</x-slot>
        <x-slot:description>{{ $addRecord ? 'Create Sales Rep Record' : 'Manage Sales Rep Data' }}</x-slot>
        <x-slot:content>
            <div class="card border-light shadow-sm warranty-tab">
                @if (!$addRecord)
                    <div class="card-header border-gray-300 p-3 mb-4">
                        @can('customer.sales-rep-override.manage')
                            <button wire:click="create" class="btn btn-primary btn-lg btn-fab"><i
                                    class="fas fa-plus"></i></button>
                        @endcan
                    </div>
                @endif
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            @if ($addRecord)
                                @include('livewire.sales-rep-override.partials.form', [
                                    'button_text' => 'Add New',
                                ])
                            @else
                                <livewire:sales-rep-override.table lazy
                                    wire:key="{{ 'sales-rep-table'}}">
                            @endif

                        </div>
                    </div>

                </div>
            </div>
        </x-slot>
    </x-page>
</div>
