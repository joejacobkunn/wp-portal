<?php

namespace App\Http\Livewire\Core\Role;

use App\Models\Core\Role;
use Illuminate\Support\Str;
use App\Http\Livewire\Component\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Resources\Transformers\PermissionGroupCollection;
use App\Models\Core\Permission;

class Index extends Component
{
    use AuthorizesRequests;
    
    public Role $role;

    //attributes

    public $addRole = false;
    public $permissionGroups = [];
    public $selectedPermissions = [];

    public $breadcrumbs = [
        [
            'title' => 'Roles',
            'route_name' => 'core.role.index'
        ],
    ];

    protected $validationAttributes = [
        'role.label' => 'Role Name',
    ];

    protected $rules = [
        'role.label' => 'required|min:3|unique:roles,label',
    ];

    public function __construct($id = null)
    {
        $this->role = new Role;
        $this->role->label = NULL;
        
        parent::__construct($id);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount()
    {
        $this->authorize('viewAny', Role::class);
    }

    public function render()
    {
        return $this->renderView('livewire.core.role.index');
    }

    public function create()
    {
        $this->authorize('store', Role::class);

        $this->permissionGroups = (new PermissionGroupCollection($this->getPermissions()))->aggregateGroup()->toArray();
        array_push($this->breadcrumbs, ['title' => 'Create']);
        $this->addRole = true;
    }

    public function submit()
    {
        $this->authorize('store', Role::class);
        $this->validate();

        $accountId = !auth()->user()->isMasterAdmin() ? app('domain')->getClientId() : null;
        $roleData = [
            'name' => Str::slug($this->role->label) . '-custom-' . ($accountId ? $accountId : 'master'),
            'guard_name' => 'web',
            'account_id' => $accountId,
        ];
        
        $this->role->fill($roleData);
        $this->role->created_by = auth()->user()->id;
        $this->role->save();

        //associate permissions to role which is allowed to current user
        $allowedPermissions = $this->getPermissions();
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
