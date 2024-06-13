<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Report for {{ $report->report_date->toFormattedDateString() }}</x-slot>

        <x-slot:description>Please review the below unavailable equipments with locations</x-slot>

        <x-slot:content>


            <div class="row">
                <div class="col-sm-12">
                    <div class="card border-light shadow-sm mb-4">

                        <div class="card-body">

                            <div class="alert alert-light-primary color-primary">
                                <i class="fas fa-info-circle"></i> Please confirm each equipment with their current
                                location by completing the checklist
                                and/or enter location if not
                                set
                            </div>

                            <ul class="list-group">
                                @foreach ($unavailableEquipments as $equipment)
                                    <li class="list-group-item">
                                        <span class="badge bg-light-info float-end"
                                            wire:click='updateEquipmentLocation("{{ $equipment->id }}")'><i
                                                class="fas fa-edit"></i></span>

                                        @if (!empty($equipment->current_location))
                                            <span
                                                class="badge bg-light-primary badge-pill badge-round ms-1 float-end"><i
                                                    class="fas fa-map-marker-alt"></i>
                                                {{ $equipment->current_location }}</span>
                                        @else
                                            <span
                                                class="badge bg-light-warning badge-pill badge-round ms-1 float-end"><i
                                                    class="fas fa-exclamation-triangle"></i> Not
                                                Set</span>
                                        @endif
                                        <input id="checkbox-1" class="form-check-input me-1" type="checkbox"
                                            value="" aria-label="..."
                                            @if (empty($equipment->current_location)) disabled @endif>
                                        <label for="checkbox-1">{{ $equipment->product_name }}
                                            ({{ $equipment->serial_number }})</label>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="row">
                                <div class="col-md-12 mt-3">
                                    <x-forms.textarea label="Notes" model="location" lazy />
                                </div>
                            </div>

                            <a href="#" class="btn btn-success">Submit Report</a>

                        </div>
                    </div>

                </div>
            </div>

            @include('livewire.equipment.unavailable.report.partials.equipment-update-modal')

        </x-slot>

    </x-page>
</div>
