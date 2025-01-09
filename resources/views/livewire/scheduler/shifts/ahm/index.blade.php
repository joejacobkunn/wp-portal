<div>

    <div class="card border-light shadow-sm zones-tab">

        <div class="card-body">
            @if (!$editRecord)
            <div class="alert alert-light-primary color-primary d-flex justify-content-between align-items-center">
                <p class="mb-0">
                    View and manage <strong>{{strtoupper(Str::headline($this->type))}} Shifts</strong> for
                    <strong>{{ $warehouse->title }}</strong>
                    here
                </p>
                <livewire:component.action-button :actionButtons="$actionButtons" :key="'comments' . time()">
            </div>
            @endif
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    @if ($editRecord)
                        @include('livewire.scheduler.shifts.ahm.partials.form', [
                            'button_text' => 'Update Shifts',
                        ])
                    @else
                        @include('livewire.scheduler.shifts.ahm.partials.view')
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

