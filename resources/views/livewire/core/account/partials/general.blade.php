<div>
    <div class="card border-light shadow-sm mb-4">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0" :key="'bew' . time()">
            <livewire:component.action-button :actionButtons="$actionButtons" :key="'comments' . time()">
            <h3 class="h5 mb-0"><i class="fas fa-bars me-1"></i> Overview</h3>
        </div>

        <div class="card-body">
            <livewire:component.alert
                :level="$this->statusAlertClass"
                :message="$this->statusAlertMessage"
                :messageIcon="$this->statusAlertMessageIcon"
                :hasAction="$this->statusAlertHasAction"
                :actionButtonClass="$this->statusAlertActionButtonClass"
                :actionButtonName="$this->statusAlertActionButtonName"
                :actionButtonAction="'updateStatus'"
                wire:key="{{ 'status_alert_'.$account->id.'_' . $account->is_active->value }}"
            />

            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Name</h3>
                        <p class="small pe-4">{{ $account->name }}</p>
                    </div>
                </li>

                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">SX Account</h3>
                        <p class="small pe-4">{{ $account->sxAccount?->name }}</p>
                    </div>
                    <span class="badge bg-light-primary float-end">CONO {{$account->sxAccount?->cono}}</span>

                </li>

                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Subdomain</h3>
                        <p class="small pe-4"><a href="http://{{ $account->subdomain }}.{{config('app.domain')}}"
                                target="_blank">https://{{ $account->subdomain }}.{{config('app.domain')}}</a></p>
                    </div>
                </li>
                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Admin</h3>
                        <p class="small pe-4">{{ $account->admin?->email ?: 'Not Set' }}</p>
                    </div>
                </li>
                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div class="account-logo-wrapper">
                        <h3 class="h6 mb-1">Logo</h3>
                        <div class="logo-container">
                            @if(isFileExists($account))
                                <img class="logo" src="{{ $account->getFirstMediaUrl('documents') }}" alt="Account Logo">
                            @else
                                <p>Logo not uploaded yet.</p>
                            @endif
                        </div>
                    </div>
                </li>
                <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                    <div>
                        <h3 class="h6 mb-1">Created At</h3>
                        <p class="small pe-4">{{ $account->created_at->format(config('app.default_datetime_format')) ;
                            }}</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
