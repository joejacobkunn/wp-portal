<div class="row px-2">
    <div class="card border-light shadow-sm mb-4">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
            <livewire:component.action-button :actionButtons="$actionButtons">
                <h3 class="h5 mb-0">User Overview</h3>
        </div>

        <div class="card-body">

            @if(!$user->is_inactive)
            <div class="alert alert-success" role="alert">

                @can('users.manage')
                <button wire:click="$toggle('deactivate_modal')" type="button" class="btn btn-outline-danger float-end my-n1" wire:key="is_active_{{ now() }}">
                    <div wire:loading wire:target="$toggle('deactivate_modal')">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    Deactivate
                </button>
                @endcan
                
                <i class="fa fa-check" aria-hidden="true"></i> This User is Active
            </div>
            @else
            <div class="alert alert-danger" role="alert">
                <button wire:click="activate()" type="button" class="btn btn-outline-success float-end my-n1" wire:key="is_active_{{ now() }}">
                    <div wire:loading wire:target="activate">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </div>
                    Activate
                </button>
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> This User is Deactivated
            </div>
            @endif

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
                        <p class="small pe-4">{{ $user->roles->first()?->label }}</p>
                    </div>
                    <div>
                </li>

            </ul>
        </div>
    </div>
</div>