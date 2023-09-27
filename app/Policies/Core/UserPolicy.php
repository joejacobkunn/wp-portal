<?php

namespace App\Policies\Core;

use App\Models\Core\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->isMasterAdmin()) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return $user->can('users.view');
    }

    public function view(User $user, User $model)
    {
        return $user->can('users.view');
    }

    public function store(User $user)
    {
        return $user->can('users.manage');
    }

    public function update(User $user, User $model)
    {
        return $user->can('users.manage');
    }

    public function delete(User $user, User $model)
    {
        return $user->can('users.manage');
    }

    public function manageRole(User $user)
    {
        return $user->can('users.manage.role');
    }
}
