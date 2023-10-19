<div>
    <h1 class="mt-4 mb-4">{{ $dashboard->name }}</h1>

    @if ($dashboard->is_active)

        <div class="row" wire:poll.60s="updateTableData">
            @foreach ($dashboard->reports as $i => $report_id)
                <div class="@if (count($dashboard->reports) > 1) col-6 @else col-12 @endif">

                    @php $report = App\Models\Report\Report::find($report_id) @endphp
                    <div class="card border-primary">
                        <div class="card-header">
                            <span class="badge bg-secondary float-end" wire:poll><i class="fas fa-sync-alt"></i> Refreshed
                                {{ $timestamp->diffForHumans() }}</span>
                            <h4>{{ $report->name }}</h4>
                        </div>
                        <div class="card-body">
                            @if ($i == 0)
                                <livewire:reporting.reporting-table :query="$report->query" :groupby="$report->group_by" broadcast="1">
                            @endif

                            @if ($i == 1)
                                <livewire:reporting.second-reporting-table :query="$report->query" :groupby="$report->group_by"
                                    broadcast="1">
                            @endif

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-light-danger color-danger">
            <i class="bi bi-exclamation-circle"></i>
            This dashboard is deactivated.
        </div>
    @endif

</div>
