<?php

namespace App\Policies\Scheduler;

use App\Models\Core\User;
use App\Models\Scheduler\Shifts;

class ShiftPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('scheduler.shift.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Shifts $shift): bool
    {
        return $user->can('scheduler.shift.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function store(User $user): bool
    {
        return $user->can('scheduler.shift.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Shifts $shift): bool
    {
        return $user->can('scheduler.shift.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Shifts $shift): bool
    {
        return $user->can('scheduler.shift.manage');
    }
}
