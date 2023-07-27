<div class="row px-2">
    <div class="card border-light shadow-sm mb-4">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
            <livewire:component.action-button :actionButtons="$actionButtons">
                <h3 class="h5 mb-0">User Overview</h3>
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
                    wire:key="{{ 'status_alert_'.$user->id.'_' . $user->is_active->value }}"
                />

            <ul class="list-group list-group-flush">


                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Name</h3>
                        <p class="small pe-4">{{ $user->name ?? '-' }}</p>
                    </div>
                    <div>
                </li>

                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Email</h3>
                        <p class="small pe-4">{{ $user->email }}</p>
                    </div>
                    <div>
                </li>

                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Role</h3>
                        <p class="small pe-4">{{ $user->email }}</p>
                    </div>
                    <div>
                </li>

                <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                    <div>
                        <h3 class="h6 mb-1">Affiliate</h3>
                        <p class="small pe-4">{{ $user->email }}</p>
                    </div>
                    <div>
                </li>

            </ul>
        </div>
    </div>
</div>
