<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Reports</x-slot>

        <x-slot:content>



            <div class="card border-light shadow-sm mb-4" style="min-height: 600px">
                <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="home-tab" href="{{ route('equipment.unavailable.index') }}"
                                role="tab" aria-controls="home" aria-selected="true">Equipments</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="profile-tab" data-bs-toggle="tab" href="#profile"
                                role="tab" aria-controls="profile" aria-selected="false" tabindex="-1">Reports
                                @if ($pending_report_count)
                                    <span class="badge bg-light-secondary">{{ $pending_report_count }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-4" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel"
                            aria-labelledby="home-tab">

                            <livewire:equipment.unavailable.report.table lazy>

                        </div>
                    </div>

                </div>
            </div>


        </x-slot>
    </x-page>
</div>
