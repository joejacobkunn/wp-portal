<div wire:init="getStatusCounts">

    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Orders for {{ $account->name }}</x-slot>

        <x-slot:content>

            @include('livewire.order.partials.metrics')

            <div class="card border-light shadow-sm mb-4" style="min-height: 600px">
                <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                </div>

                <div class="card-body">
                    @if ($dnr_count > 0)
                        <div class="alert alert-light-warning color-warning">
                            <button type="button" wire:click="filter('is_dnr', '2')"
                                class="btn btn-sm btn-outline-secondary float-end">Show Pending DNR
                                Orders</button>
                            <i class="fas fa-exclamation-circle"></i>
                            There are <strong>{{ $dnr_count }} DNR Order(s)</strong> that are Pending Review
                        </div>
                    @endif

                    <livewire:order.table
                        lazy>
                </div>
            </div>


        </x-slot>
    </x-page>
</div>
