<div>
    @if($loaded)
    <div class="card border-light shadow-sm mb-4">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0" :key="'bew' . time()">
            @if($tabLoaded)
                <livewire:component.action-button :actionButtons="$actionButtons" :key="'comments' . time()">
            @endif
            <h3 class="h5 mb-0"><i class="fas fa-bars me-1"></i> Overview</h3>
        </div>

        <div class="card-body">
            @if($account->is_active)
                <div class="alert alert-light-success color-success" role="alert">
                    <i class="far fa-check-circle"></i> This account is active
                    <button class="btn btn-sm btn-outline-danger float-end mt-n1">Deactivate</button>
                </div>
            @else
                <div class="alert alert-light-danger color-danger" role="alert">
                    <i class="far fa-times-circle"></i> This account is deactivated
                    <button class="btn btn-sm btn-outline-success float-end mt-n1">Activate</button>
                </div>
            @endif

            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Name</h3>
                        <p class="small pe-4">{{ $account->name }}</p>
                    </div>
                </li>
                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Subdomain</h3>
                        <p class="small pe-4"><a href="http://{{ $account->subdomain }}.{{env('APP_DOMAIN_NAME')}}" target="_blank">https://{{ $account->subdomain }}.{{env('APP_DOMAIN_NAME')}}</a></p>
                    </div>
                </li>
                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Admin</h3>
                        <p class="small pe-4">{{ $account->admin->name ?? $account->admin->email ?? 'Not Set' }}</p>
                    </div>
                </li>
                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Created At</h3>
                        <p class="small pe-4">{{ $account->created_at->format(config('app.default_datetime_format')) ; }}</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    @endif
</div>