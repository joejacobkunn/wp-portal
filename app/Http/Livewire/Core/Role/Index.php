<?php

namespace App\Http\Livewire\Core\Role;

use App\Models\Core\Role;
use Illuminate\Support\Str;
use App\Http\Livewire\Component\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Resources\Transformers\PermissionGroupCollection;

class Index extends Component
{
    use AuthorizesRequests;
    
    public Role $role;

    //attributes
    public $addRole = false;
    public $roles = [];
    public $permissionGroups = [];
    public $selectedPermissions = [];

    public $breadcrumbs = [
        [
            'title' => 'Roles',
            'route_name' => 'core.roles.index'
        ],
    ];

    protected $validationAttributes = [
        'role.label' => 'Role Name',
    ];

    protected $rules = [
        'role.label' => 'required|min:3|unique:roles,label',
        'role.reporting_role' => 'nullable',
    ];

    public function __construct($id = null)
    {
        $this->role = new Role;
        $this->role->label = NULL;
        $this->role->reporting_role = NULL;
        
        parent::__construct($id);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        $this->authorize('viewAny', Role::class);
        return $this->renderView('livewire.core.role.index')->extends('livewire-app', ['moduleName' => $this->moduleName ?? '', 'breadcrumbs' => $this->breadcrumbs]);
    }

    public function create()
    {
        $this->authorize('store', Role::class);
        $this->roles = Role::where('level', '>=', auth()->user()->role->level)
            ->select('label', 'id')
            ->orderBy('level')
            ->get();

        $this->permissionGroups = (new PermissionGroupCollection(auth()->user()->getAllPermissions()))->aggregateGroup()->toArray();

        array_push($this->breadcrumbs, ['title' => 'Create']);

        $this->addRole = true;
    }

    public function submit()
    {
        $this->authorize('store', Role::class);
        $this->validate();

        $roleData = [
            'name' => Str::slug($this->role->label),
            'guard_name' => 'web'
        ];
        
        if ($this->role->reporting_role) {
            $reportingRole = Role::find($this->role->reporting_role);
            $roleData['reporting_role'] = $reportingRole->id;
            $roleData['level'] = $reportingRole->level + 1;
        } else {
            $currentRole = auth()->user()->roles()->first();
            $roleData['reporting_role'] = $currentRole->reporting_role;
            $roleData['level'] = $currentRole->level;
        }

        $this->role->fill($roleData);
        $this->role->created_by = auth()->user()->id;
        $this->role->save();

        //associate permissions to role which is allowed to current user
        $allowedPermissions = auth()->user()->getAllPermissions();
        $selectedPermissions = $allowedPermissions->whereIn('name', $this->selectedPermissions);
        $this->role->givePermissionTo([$selectedPermissions]);

        $this->reset();
        $this->addRole = false;

        session()->flash('success', 'Role created !');
    }

    public function cancel()
    {
        $this->reset();

        $this->addRole = false;
    }
}
