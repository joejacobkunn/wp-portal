<div>

    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:content>

            <div>
                @unless ($addRecord)

                    <div class="card border-light shadow-sm mb-4" style="min-height: 600px">
                        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                            @can('products.manage')
                                <button disabled wire:click='create' class="btn btn-sm btn-outline-primary float-end"><i
                                        class="fas fa-plus"></i>
                                    New
                                    Product</button>
                            @endcan
                            <h3 class="h5 mb-0">Product List for {{ $account->name }}</h3>
                        </div>

                        <div class="card-body">
                            <livewire:product.table :account="$account" key="{{ now() }}" lazy />
                        </div>

                    </div>
                @else
                    <div class="card border-light shadow-sm mb-4" style="min-height: 600px">
                        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                            <h3 class="h5 mb-0">Create Customer for {{ $account->name }}</h3>
                        </div>

                        <div class="card-body">
                        </div>
                    </div>
                @endunless

            </div>

        </x-slot>

    </x-page>
</div>
