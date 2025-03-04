<?php

namespace App\Policies\Scheduler\ServiceArea;

use App\Models\Core\User;
use App\Models\Scheduler\Zones;

class ZonesPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('scheduler.serice-area.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Zones $zone): bool
    {
        return $user->can('scheduler.serice-area.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function store(User $user): bool
    {
        return $user->can('scheduler.serice-area.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Zones $zone): bool
    {
        return $user->can('scheduler.serice-area.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Zones $zone): bool
    {
        return $user->can('scheduler.serice-area.manage');
    }
}
