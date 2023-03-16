<?php

namespace App\Policies\Core;

use App\Models\Core\User;
use App\Models\Core\Location;

class LocationPolicy
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

    public function viewAny(User $user)
    {
        return $user->can('locations.view');
    }

    public function view(User $user, Location $location)
    {
        return $user->can('locations.view');
    }

    public function store(User $user)
    {
        return $user->can('locations.manage');
    }

    public function update(User $user, Location $location)
    {
        return $user->can('locations.manage');
    }

    public function delete(User $user, Location $location)
    {
        return $user->can('locations.manage');
    }


}
