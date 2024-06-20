<?php

namespace App\Policies\Equipment\Warranty;

use App\Models\Core\User;

class BrandWarrantyPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function viewAny(User $user)
    {
        return $user->can('equipment.warranty.view');
    }

    public function view(User $user)
    {
        return $user->can('equipment.warranty.view');
    }

    public function store(User $user)
    {
        return $user->can('equipment.warranty.manage');
    }

    public function update(User $user)
    {
        return $user->can('equipment.warranty.manage');
    }

    public function delete(User $user)
    {
        return $user->can('equipment.warranty.manage');
    }
}
