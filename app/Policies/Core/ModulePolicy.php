<?php

namespace App\Policies\Core;

use App\Models\Core\Module;
use App\Models\Core\User;

class ModulePolicy
{
    /**
     * Create a new policy instance.
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

    public function view(User $user, Module $module)
    {
        return $user->can('modules.view');
    }

    public function update(User $user, Module $module)
    {
        return $user->hasRole('master-admin');
    }


}
