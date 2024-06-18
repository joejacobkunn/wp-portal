<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Report for {{ $report->report_date->toFormattedDateString() }}</x-slot>

        <x-slot:description>Please review the below unavailable equipments with locations</x-slot>

        <x-slot:content>


            <div class="row">
                <div class="col-sm-12">
                    <div class="card border-light shadow-sm mb-4">
                        <div class="card-body">
                            @if($editForm)
                                @include('livewire.equipment.unavailable.report.partials.equipment-location-form')
                            @else
                                @include('livewire.equipment.unavailable.report.partials.equipment-location-view')
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            @include('livewire.equipment.unavailable.report.partials.equipment-update-modal')

        </x-slot>

    </x-page>
</div>
