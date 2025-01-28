<div>
    <div class="alert alert-light-primary color-primary">
        <i class="fas fa-info-circle"></i> Please confirm each equipment with their current
        location by completing the checklist
        and/or enter location if not
        set
    </div>

    <ul class="list-group">
        @foreach ($unavailableEquipments as $equipment)
            <li class="list-group-item">
                @can('equipment.unavailable.manage')
                    <span class="badge bg-light-info float-end"
                        wire:click='updateEquipmentLocation("{{ $equipment->id }}")'><i class="fas fa-edit"></i></span>
                @endcan

                @if (!empty($equipment->current_location))
                    <span class="badge bg-light-primary badge-pill badge-round ms-1 float-end"><i
                            class="fas fa-map-marker-alt"></i>
                        {{ $equipment->current_location }}</span>
                @else
                    <span class="badge bg-light-warning badge-pill badge-round ms-1 float-end"><i
                            class="fas fa-exclamation-triangle"></i> Not
                        Set</span>
                @endif

                <div class="float-start">
                    <x-forms.checkbox :label="$equipment->product_name . '(' . $equipment->serial_number . ')'" model="selectedEquipments" :disabled="empty($equipment->current_location)" :value="$equipment->id"
                        class="mb-3" />
                </div>
            </li>
        @endforeach
    </ul>

    <div class="row">
        <div class="col-md-12 mt-3">
            <x-forms.textarea label="Notes" model="notes" lazy />
        </div>
    </div>

    @if ($errors->has('selectedEquipments'))
        <div class="alert alert-danger">
            <strong>Error</strong>
            <p>{{ $errors->get('selectedEquipments')[0] }}</p>
        </div>
    @endif

    <button type="button" class="btn btn-success" wire:click="submitReport">Submit Report</button>

</div>
