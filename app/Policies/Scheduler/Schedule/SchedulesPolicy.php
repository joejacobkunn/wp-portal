<?php

namespace App\Policies\Scheduler\Schedule;

use App\Models\Core\User;
use App\Models\Scheduler\Schedule;

class SchedulesPolicy
{

 /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('scheduler.schedule.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Schedule $schedule): bool
    {
        return $user->can('scheduler.schedule.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function store(User $user): bool
    {
        return $user->can('scheduler.schedule.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Schedule $schedule): bool
    {
        return $user->can('scheduler.schedule.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Schedule $schedule): bool
    {
        return $user->can('scheduler.schedule.manage');
    }
}
