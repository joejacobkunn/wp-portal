<?php

namespace App\Policies\Scheduler;

use App\Models\Core\User;
use App\Models\Scheduler\Truck;

class TruckPolicy
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
        return account()->hasModule('scheduler') &&
                $user->can('scheduler.truck.view');

    }

    public function view(User $user, Truck $truck)
    {
        dd($truck);
        return account()->hasModule('scheduler') &&
                $user->can('scheduler.truck.view');
    }


    public function store(User $user)
    {
        return account()->hasModule('scheduler') &&
                $user->can('scheduler.truck.manage');

    }

    public function update(User $user, Truck $truck)
    {
        return account()->hasModule('scheduler') &&
                $user->can('scheduler.truck.manage');

    }

    public function delete(User $user, Truck $truck)
    {
        return account()->hasModule('scheduler') &&
                $user->can('scheduler.truck.manage');

    }
}
