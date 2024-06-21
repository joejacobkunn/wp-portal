<div>

    <h5 class="card-title">
        <span class="badge bg-light-success float-end">Completed on
            {{ $report->submitted_at->toFormattedDateString() }}</span>
        Equipments
    </h5>

    <ul class="list-group mt-4">
        @foreach ($unavailableEquipments as $equipment)
            @if (in_array($equipment->id, $selectedEquipments))
                <li class="list-group-item">
                    {{ $equipment->product_name . ' (' . $equipment->product_code . ')' }} <span
                        class="badge bg-light-secondary">Serial : {{ $equipment->serial_number }}</span>
                    <span class="badge bg-light-primary badge-pill badge-round ms-1 float-end">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ $equipment->current_location }}
                    </span>
                </li>
            @endif
        @endforeach
    </ul>

    @if (!empty($report->note))
        <div class="row">
            <div class="col-md-12 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Notes</h5>
                    </div>
                    <div class="card-body">
                        <p>
                            {{ $report->note }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
