<div>

    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:content>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="home-tab" href="{{ route('reporting.index') }}" role="tab"
                        aria-controls="home" aria-selected="true">Reports</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="profile-tab" href="{{ route('reporting-dashboard.index') }}"
                        aria-controls="profile" aria-selected="false" tabindex="-1">Dashboards</a>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div>
                        <div class="card border-light shadow-sm mt-4" style="min-height: 600px">
                            <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                                @if (!$addDashboard)
                                    <button wire:click="create" class="btn btn-sm btn-outline-primary float-end"><i
                                            class="fa-solid fa-plus"></i> New
                                        Dashboard</button>
                                @endif
                                <h3 class="h5 mb-0">
                                    {{ !$addDashboard ? 'Manage Report Dashboards here' : 'Create a New Report dashboard here' }}
                                </h3>
                            </div>

                            <div class="card-body">
                                @if ($addDashboard)
                                    @include('livewire.reporting-dashboard.partials.form', [
                                        'button_text' => 'Add Report Dashboard',
                                    ])
                                @else
                                    @include('livewire.reporting-dashboard.partials.listing')
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
</div>


</x-slot>

</x-page>

</div>
