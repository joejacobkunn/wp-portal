<div>
    <h1 class="mt-4 mb-4">{{ $dashboard->name }}</h1>

    <div class="row" wire:poll.70s="updateTableData">
        @foreach ($dashboard->reports as $i => $report_id)
            <div class="col-6">

                @php $report = App\Models\Report\Report::find($report_id) @endphp
                <div class="card border-primary">
                    <div class="card-header">
                        <span class="badge bg-secondary float-end" wire:poll><i class="fas fa-sync-alt"></i> Refreshed
                            {{ $timestamp->diffForHumans() }}</span>
                        <h4>{{ $report->name }}</h4>
                    </div>
                    <div class="card-body">
                        @if ($i == 0)
                            <livewire:reporting.reporting-table :query="$report->query" :groupby="$report->group_by">
                        @endif

                        @if ($i == 1)
                            <livewire:reporting.second-reporting-table :query="$report->query" :groupby="$report->group_by">
                        @endif

                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
