<div class="row px-2">
    <div class="card border-light shadow-sm mb-4">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">


            @can('roles.manage')
                @if(!$role->is_preset) 
                    <livewire:component.action-button
                        :actionButtons="$actionButtons"
                    >
                @endif
            @endcan
            <h3 class="h5 mb-0"><i class="fas fa-bars me-1"></i> Overview</h3>
        </div>
        
        <div class="card-body">

            <ul class="list-group list-group-flush">

                <li class="list-group-item d-flex align-items-center justify-content-between px-0 border-bottom">
                    <div>
                        <h3 class="h6 mb-1">Role Name</h3>
                        <p class="small pe-4">{{ $role->label }}</p>
                    </div>
                    <div>
                </li>
            </ul>
        </div>
    </div>
    <div class="card border-light shadow-sm mb-4">
        <div class="card-header border-gray-300 p-3 mb-4 mb-md-0">
            <h3 class="h5 mb-0"><i class="fas fa-key me-1"></i> Permissions</h3>
        </div>
        <div class="card-body">
            <div class="accordion permission-accordion">
                @forelse($role->permission_list as $group => $permissionGroup)
                    <div class="accordion-item mb-2">
                        <h2 class="accordion-header" id="heading{{ $group }}">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $group }}" aria-expanded="true" aria-controls="collapse-{{ $group }}">
                            {{ $permissionGroup["group_name"] }}
                        </button>
                        </h2>
                        
                        <div id="collapse-{{ $group }}" class="accordion-collapse collapse show" aria-labelledby="heading{{ $group }}">
                        <div class="accordion-body">
                            <div class="row">
                                @foreach ($permissionGroup["permissions"] as $permission)
                                <div class="col-md-3 mb-3">
                                    <label>
                                        <i class="fas fa-check-circle"></i> {{ $permission->label }}
                                    </label>
                                    @if(!empty($permission->description))
                                    <sup><i class="fas fa-question-circle"
                                        data-bs-toggle="tooltip"
                                        data-bs-custom-class="custom-tooltip"
                                        data-bs-placement="right" 
                                        data-bs-title="{{ $permission->description  }}"></i></sup>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        </div>
                    </div>
                @empty
                    <p>No permissions found.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>