<?php

namespace App\Http\Livewire\Core\Role;

use App\Http\Livewire\Component\Component;
use App\Models\Core\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Resources\Transformers\PermissionGroupCollection;

class Show extends Component
{
    use AuthorizesRequests;
    
    public Role $role;
    public $moduleName = 'Administration';

    public $editRole = false;
    public $locations = [];
    public $roles = [];
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
            'route_name' => 'core.roles.index'
        ],
    ];

    public $actionButtons = [
        [
            'icon' => 'fa-edit',
            'color' => 'primary',
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
        $this->role->reportsTo = $this->role->reportsTo()->basicSelect()->first();
        $this->role->permission_list = (new PermissionGroupCollection($this->role->getAllPermissions()))->aggregateGroup()->toArray();

        return $this->renderView('livewire.core.role.show');
    }

    public function rules() {
        return [
            'role.label' => 'required|min:3|unique:roles,label,'.$this->role->id,
            'role.reporting_role' => 'nullable',
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
        $this->roles = Role::where('level', '>=', auth()->user()->role->level)
            ->select('label', 'id')
            ->where('id', '!=', $this->role->id)
            ->orderBy('level')
            ->get()
            ->toArray();

        $this->selectedPermissions = $this->role->getAllPermissions()->pluck('name')->toArray();
        $this->permissionGroups = (new PermissionGroupCollection(auth()->user()->getAllPermissions()))->aggregateGroup()->toArray();
        $this->editRole = true;
    }

    public function save()
    {
        $this->authorize('update', $this->role);
        $this->validate();

        $roleData['reporting_role'] = null;
        if ($this->role->reporting_role) {
            $reportingRole = Role::find($this->role->reporting_role);
            $this->role->reporting_role = $reportingRole->id;
            $this->role->level = $reportingRole->level + 1;
        } else {
            $this->role->reporting_role = null;
            $this->role->level = 0;
        }

        $this->role->save();

        //associate permissions to role which is allowed to current user
        $allowedPermissions = auth()->user()->getAllPermissions();
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

        return redirect()->route('core.roles.index');
    }

    public function cancel()
    {
        //reset dirty attributes to original
        $this->role->setRawAttributes($this->role->getOriginal());
        $this->editRole = false;
    }
}
