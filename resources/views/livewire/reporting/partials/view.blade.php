<div class="row px-2">
    <div class="card border-light shadow-sm mb-4">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
            @can('reporting.manage')
                <livewire:component.action-button :actionButtons="$actionButtons">
                @endcan
                <h3 class="h5 mb-0">{{ $report->name }}</h3>
        </div>

        <div class="card-body">

            <p class="card-text">
                {{ $report->description }}
            </p>

            @can('reporting.manage')
                <div class="divider">
                    <div class="divider-text"><a data-bs-toggle="collapse" href="#collapseExample" role="button">Load More</a>
                    </div>
                </div>

                <div class="collapse" id="collapseExample">

                    <ul class="list-group list-group-flush">

                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <div>
                                <h3 class="h6 mb-1">Query</h3>
                                <p class="small pe-4"><code>{{ $report->query }}</code></p>
                            </div>
                        </li>

                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <div>
                                <h3 class="h6 mb-1">Group By</h3>
                                <p class="small pe-4">{{ $report->group_by }}</p>
                            </div>
                        </li>

                    </ul>

                </div>
            @endcan
        </div>
    </div>

    <div class="card border-light shadow-sm mb-4">
        <div class="card-header border-gray-300 p-3 mb-2 mb-md-0">

            <button wire:click="$dispatch('refreshDatatable')" class="btn btn-sm icon btn-primary float-end"><i
                    class="fas fa-sync-alt"></i></button>

            <h3 class="h5 mb-0">
                Data for {{ $report->name }} as of
                <kbd>{{ now()->timezone('America/Detroit')->toDayDateTimeString() }}</kbd>
            </h3>
        </div>


        <div class="card-body">
            <livewire:reporting.reporting-table :query="$report->query" :groupby="$report->group_by">
        </div>
    </div>


</div>
