<div>

    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:content>

            <div>
                <div class="card border-light shadow-sm mb-4" style="min-height: 600px">
                    <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">

                        <h3 class="h5 mb-0">Customer List for {{$account->name}}</h3>
                    </div>

                    <div class="card-body">
                        <livewire:core.customer.table :account="$account" />
                    </div>
                </div>
            </div>

            </x-slot>

    </x-page>

</div>