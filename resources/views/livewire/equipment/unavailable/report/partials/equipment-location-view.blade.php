<div>

    <ul class="list-group">
        @foreach ($unavailableEquipments as $equipment)
            @if(in_array($equipment->id, $selectedEquipments))
            <li class="list-group-item">
                {{ $equipment->product_name . '(' . $equipment->serial_number .')' }}
                <span class="badge bg-light-primary badge-pill badge-round ms-1 float-end">
                    <i class="fas fa-map-marker-alt me-1"></i>{{ $equipment->current_location }}
                </span>
            </li>
            @endif
        @endforeach
    </ul>

    <div class="row">
        <div class="col-md-12 mt-3">
            {{ $notes }}
        </div>
    </div>
</div>