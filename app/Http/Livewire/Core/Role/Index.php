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
    public $roleTypes = [];
    public $selectedType = 'account_type';

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
        'selectedType' => 'required',
    ];

    protected $listeners = [
        'selectedType:changed' => 'roleTypeChanged',
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

        $this->roleTypes = Role::getRoleTypes();
    }

    public function render()
    {
        return $this->renderView('livewire.core.role.index');
    }

    public function roleTypeChanged($name, $value, $recheckValidation = true)
    {
        $this->fieldUpdated($name, $value, $recheckValidation);
        $this->setPermissionGroups();
    }

    public function create()
    {
        $this->authorize('store', Role::class);

        $this->setPermissionGroups();
        array_push($this->breadcrumbs, ['title' => 'Create']);
        $this->addRole = true;
    }

    public function setPermissionGroups()
    {
        $this->permissionGroups = (new PermissionGroupCollection($this->getPermissions()))->aggregateGroup()->toArray();
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

        if($this->selectedType == 'all') {
            $this->role->master_type = true;
            $this->role->account_type = true;
        } else if($this->selectedType == 'master_type') {
            $this->role->master_type = true;
            $this->role->account_type = false;
        }else if($this->selectedType == 'account_type') {
            $this->role->master_type = false;
            $this->role->account_type = true;
        }

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
        return Permission::query()
        ->when($this->selectedType == 'master_type', function($query) {
            $query->where('master_type', 1);
        })->when($this->selectedType == 'account_type', function($query) {
            $query->where('account_type', 1);
        })->get();
    }
}
