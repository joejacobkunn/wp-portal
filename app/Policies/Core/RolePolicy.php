<?php

namespace App\Policies\Core;

use App\Models\Core\Role;
use App\Models\Core\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function before(User $user, $ability)
    {
        if ($user->hasRole('master-admin')) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return $user->can('roles.view');
    }

    public function view(User $user, Role $role)
    {
        return $user->can('roles.view');
    }

    public function store(User $user)
    {
        return $user->can('roles.manage');
    }

    public function update(User $user, Role $role)
    {
        return $user->can('roles.manage');
    }

    public function delete(User $user, Role $role)
    {
        return $user->can('roles.manage') && 
                !in_array($role->name,[Role::MASTER_ROLE,Role::SUPER_ADMIN_ROLE, Role::USER_ROLE, Role::DEFAULT_USER_ROLE]);
    }
}
