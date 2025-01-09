<?php

namespace App\Policies\Scheduler;

use App\Models\Core\User;
use App\Models\Scheduler\NotificationTemplate;

class TemplatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('scheduler.template.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, NotificationTemplate $template): bool
    {
        return $user->can('scheduler.template.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function store(User $user): bool
    {
        return $user->can('scheduler.template.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, NotificationTemplate $template): bool
    {
        return $user->can('scheduler.template.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, NotificationTemplate $template): bool
    {
        return $user->can('scheduler.template.manage');
    }
}
