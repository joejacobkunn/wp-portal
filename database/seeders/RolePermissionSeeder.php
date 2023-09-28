<?php

namespace Database\Seeders;

use App\Models\Core\Permission;
use App\Models\Core\Role;
use App\Models\Core\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class RolePermissionSeeder extends Seeder
{
    protected $masterRole;

    protected $roles = [];

    protected $roleTypeMap = [
        'master' => [
            'master-admin'
        ],
        'account' => [
            'super-admin',
            'user'
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedRoles();

        $this->roles = Role::select('id', 'name', 'guard_name')->get()->keyBy('name');
        $permissionGroups = $this->parsePermissions('mappings/permissions', true);

        $defaultRoles = [];
        foreach ($this->roleTypeMap as $type => $roleMap) {
            $defaultRoles = array_merge($defaultRoles, $roleMap);
        }

        foreach ($permissionGroups as $permissionGroup) {
            foreach ($permissionGroup['items'] as $permission) {
                $permissionUserMap = $permission['user_groups'] ?? $permissionGroup['user_groups'];

                $permissionItem = Permission::updateOrCreate([
                    'name' => $permission['name'],
                ], [
                    'name' => $permission['name'],
                    'label' => $permission['label'],
                    'group_name' => $permissionGroup['group'],
                    'description' => $permission['description'] ?? null,
                    'master_type' => (int) (!empty(array_intersect($permissionUserMap, $this->roleTypeMap['master']))),
                    'account_type' => (int) (!empty(array_intersect($permissionUserMap, $this->roleTypeMap['account']))),
                ]);

                foreach ($defaultRoles as $role) {
                    if (in_array($role, $permissionGroup['user_groups'])) {
                        $this->attachToRoles($permissionItem, $role);
                    }
                }
            }
        }
    }

    public function seedRoles()
    {
        $this->seedMasterRole();
        $this->seedSuperAdminRole();
        $this->seedUserRole();
        $this->seedDefaultRole();
    }

    /**
     * Create master role & admin if not exist
     */
    public function seedMasterRole()
    {
        $this->masterRole = Role::updateOrCreate(
            ['name' => 'master-admin'],
            [
                'label' => 'Master Admin',
                'name' => 'master-admin',
                'is_preset' => 1,
                'master_type' => true,
                'account_type' => false
            ]
        );

        $user = User::where('email', config('permission.master_user_email'))->firstOrNew();
        if (! $user->id) {
            $user->name = 'Master Admin';
            $user->email = config('permission.master_user_email');
            $user->abbreviation = 'MA';
            $user->save();
        }

        if (! $user->hasRole('master-admin')) {
            $user->assignRole($this->masterRole);
        }
    }

    /**
     * Create superadmin role
     */
    public function seedSuperAdminRole()
    {
        Role::updateOrCreate(
            ['name' => 'super-admin'],
            [
                'label' => 'Super Admin',
                'name' => 'super-admin',
                'is_preset' => 1,
                'master_type' => false,
                'account_type' => true
            ]
        );
    }

    /**
     * Create user role
     */
    public function seedUserRole()
    {
        Role::updateOrCreate(
            ['name' => 'user'],
            [
                'label' => 'User',
                'name' => 'user',
                'is_preset' => 1,
                'master_type' => false,
                'account_type' => true
            ]
        );
    }

    /**
     * Create Default Role
     */
    public function seedDefaultRole()
    {
        Role::updateOrCreate(
            ['name' => 'default-user'],
            [
                'label' => 'Default User',
                'name' => 'default-user',
                'is_preset' => 1,
                'master_type' => false,
                'account_type' => true
            ]
        );
    }

    /**
     * Attach permission to defined role types
     */
    public function attachToRoles($permission, $role)
    {
        if (isset($this->roles[$role])) {
            $this->roles[$role]->givePermissionTo($permission);
        }
    }

    /**
     * Parse permissions
     */
    public function parsePermissions($filePath, $path = false)
    {
        if (! $path) {
            return json_decode(file_get_contents(__DIR__.'/'.$filePath), true);
        }

        $allFiles = Arr::sort(Storage::disk('database')->files('seeders/'.$filePath));
        $basePath = str_replace('seeders', '', __DIR__);

        $data = [];
        foreach ($allFiles as $file) {
            $data = array_merge($data, json_decode(file_get_contents($basePath.$file), true));
        }

        return $data;
    }
}
