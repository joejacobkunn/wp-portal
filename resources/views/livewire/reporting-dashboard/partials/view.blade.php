<div class="row px-2">
    <div class="card border-light shadow-sm mb-4">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
            @can('reporting.manage')
                <livewire:component.action-button :actionButtons="$actionButtons">
                @endcan
                <h3 class="h5 mb-0">{{ $dashboard->name }}</h3>
        </div>

        <div class="card-body">

            @if ($dashboard->is_active)
                <div class="alert alert-light-success color-success">
                    <i class="bi bi-check-circle"></i> This dashboard is active
                </div>
            @else
                <div class="alert alert-light-danger color-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i> This dashboard is deactivated
                </div>
            @endif

            <ul class="list-group list-group-flush">

                <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                    <div>
                        <h3 class="h6 mb-2">Included Reports</h3>
                        <p class="small pe-4">
                            @foreach (App\Models\Report\Report::whereIn('id', $dashboard->reports)->get() as $report)
                                <span class="badge bg-secondary">{{ $report->name }}</span>
                            @endforeach
                        </p>
                    </div>
                </li>

                <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                    <div>
                        <h3 class="h6 mb-1">Public URL</h3>
                        <p class="small pe-4">{{ route('reporting-dashboard.broadcast', ['dashboard' => $dashboard]) }}
                        </p>
                    </div>
                </li>

            </ul>

        </div>
    </div>

</div>
