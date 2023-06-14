<?php

namespace App\Policies\Vehicle;

use App\Models\Core\User;
use App\Models\Vehicle\Vehicle;

class VehiclePolicy
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
        if ($user->hasRole('super-admin')) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return account()->hasModule('vehicles') &&
                $user->can('vehicle.view');

    }

    public function store(User $user)
    {
        return account()->hasModule('vehicles') &&
                $user->can('vehicle.manage');

    }

    public function update(User $user, Vehicle $vehicle)
    {
        return account()->hasModule('vehicles') &&
                $user->can('vehicle.manage');

    }

    public function delete(User $user, Vehicle $vehicle)
    {
        return account()->hasModule('vehicles') &&
                $user->can('vehicle.manage');

    }
}
