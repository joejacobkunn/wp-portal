<div>
    <x-page :breadcrumbs="$breadcrumbs">

        <x-slot:title>Unavailable Equipments for {{ $account->name }}</x-slot>

        <x-slot:content>



            <div class="card border-light shadow-sm mb-4" style="min-height: 600px">
                <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
                </div>

                <div class="card-body">
                    @if ($this->configured)

                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="home-tab"
                                    href="{{ route('equipment.unavailable.index') }}" role="tab"
                                    aria-controls="home" aria-selected="true">Equipments</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="profile-tab"
                                    href="{{ route('equipment.unavailable.report.index') }}" role="tab"
                                    aria-controls="profile" aria-selected="false" tabindex="-1">Reports @if ($pending_report_count)
                                        <span class="badge bg-light-secondary">{{ $pending_report_count }}</span>
                                    @endif
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content mt-4" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel"
                                aria-labelledby="home-tab">
                                @if (!auth()->user()->can('equipment.unavailable.viewall'))
                                    <div class="alert alert-secondary">
                                        Showing all equipment in possession by
                                        <strong>{{ auth()->user()->name }} -
                                            {{ auth()->user()->unavailable_equipments_id }}</strong>
                                    </div>
                                @endif

                                <livewire:equipment.unavailable.table lazy>

                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Your account
                            has
                            not been configured in the Portal to use
                            this app. Contact Mark Meister to resolve this</div>
                    @endif
                </div>
            </div>


        </x-slot>
    </x-page>
</div>
