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
        $this->masterRole = Role::where('name', 'master-admin')->firstOrNew();

        if (! $this->masterRole->id) {
            $this->masterRole->label = 'Master Admin';
            $this->masterRole->name = 'master-admin';
            //$this->masterRole->level = 0;
            $this->masterRole->is_preset = 1;
            $this->masterRole->save();
        }

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
        $superAdminRole = Role::where('name', 'super-admin')->firstOrNew();

        if (! $superAdminRole->id) {
            $superAdminRole->label = 'Super Admin';
            $superAdminRole->name = 'super-admin';
            //$superAdminRole->level = 100;
            //$superAdminRole->reporting_role = $this->masterRole->id;
            $superAdminRole->is_preset = 1;
            $superAdminRole->save();
        }
    }

    /**
     * Create user role
     */
    public function seedUserRole()
    {
        $userRole = Role::where('name', 'user')->firstOrNew();
        $superAdminRole = Role::where('name', 'super-admin')->first();

        if (! $userRole->id) {
            $userRole->label = 'User';
            $userRole->name = 'user';
            //$userRole->level = 200;
            $userRole->is_preset = 1;
            //$userRole->reporting_role = $superAdminRole->id;
            $userRole->save();
        }
    }

    /**
     * Create Default Role
     */
    public function seedDefaultRole()
    {
        $userRole = Role::where('name', 'default')->firstOrNew();
        if (! $userRole->id) {
            $userRole->label = 'Default';
            $userRole->name = 'default';
            $userRole->is_preset = 1;
            $userRole->save();
        }
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
