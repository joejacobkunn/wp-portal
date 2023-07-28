<?php

namespace App\Http\Livewire\Core\Role;

use App\Models\Core\Role;
use App\Models\Core\Permission;
use App\Http\Livewire\Component\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Resources\Transformers\PermissionGroupCollection;

class Show extends Component
{
    use AuthorizesRequests;
    
    public Role $role;

    public $editRole = false;
    public $locations = [];
    public $permissionGroups = [];
    public $selectedPermissions = [];

    protected $listeners = [
        'deleteRecord' => 'delete',
        'closeModal' => 'closeModal',
        'edit' => 'edit',
    ];

    protected $validationAttributes = [
        'role.label' => 'Role Name',
    ];

    public $breadcrumbs = [
        [
            'title' => 'Roles',
            'route_name' => 'core.role.index'
        ],
    ];

    public $actionButtons = [
        [
            'icon' => 'fa-edit',
            'color' => 'info',
            'listener' => 'edit'
        ],
        [
            'icon' => 'fa-trash',
            'color' => 'danger',
            'confirm' => true,
            'confirm_header' => "Confirm Delete",
            'listener' => 'deleteRecord'
        ],
    ];

    public function render()
    {
        $this->authorize('view', $this->role);
        $this->role->permission_list = (new PermissionGroupCollection($this->role->getAllPermissions()))->aggregateGroup()->toArray();

        return $this->renderView('livewire.core.role.show');
    }

    public function rules() {
        return [
            'role.label' => 'required|min:3|unique:roles,label,'.$this->role->id,
        ];
    }

    public function mount(Role $role)
    {
        $this->authorize('view', $role);
        array_push($this->breadcrumbs, ['title' => $role->label]);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function edit()
    {
        $this->authorize('update', $this->role);
        $this->selectedPermissions = $this->role->getAllPermissions()->pluck('name')->toArray();
        $this->permissionGroups = (new PermissionGroupCollection($this->getPermissions()))->aggregateGroup()->toArray();
        $this->editRole = true;
    }

    public function save()
    {
        $this->authorize('update', $this->role);
        $this->validate();

        $this->role->save();

        //associate permissions to role which is allowed to current user
        $allowedPermissions = $this->getPermissions();
        $selectedPermissions = $allowedPermissions->whereIn('name', $this->selectedPermissions);
        $this->role->syncPermissions([$selectedPermissions]);
        $this->role->permission_list = (new PermissionGroupCollection($this->role->getAllPermissions()))->aggregateGroup()->toArray();

        $this->editRole = false;

        session()->flash('success', 'Role updated !');
    }

    public function delete()
    {
        $this->authorize('delete', $this->role);
        
        //check for existing users with this role
        $existingUserCount = $this->role->hasModels()->count();
        if ($existingUserCount) {
            session()->flash('warning', "Error, there ".($existingUserCount > 1 ? 'are' : 'is')." $existingUserCount users associated with this role, re-assign them to different role before continue!");
            return;
        }

        //check for other roles reporting to this role
        $reportingRoleCount = Role::where('reporting_role', $this->role->id)->count();
        if ($reportingRoleCount) {
            session()->flash('warning', "Warning, there ".($reportingRoleCount > 1 ? 'are' : 'is')." $reportingRoleCount roles reporting to this role!");
            return;
        }
        
        $this->role->delete();
        session()->flash('success', 'Role deleted !');

        return redirect()->route('core.role.index');
    }

    public function cancel()
    {
        //reset dirty attributes to original
        $this->role->setRawAttributes($this->role->getOriginal());
        $this->editRole = false;
    }

    protected function getPermissions()
    {
        $permissions = Permission::query();
        if (auth()->user()->isMasterAdmin()) {
            $permissions->where('master_type', 1);
        } else  {
            $permissions->where('account_type', 1);
        }

        return $permissions->get();
    }
}
