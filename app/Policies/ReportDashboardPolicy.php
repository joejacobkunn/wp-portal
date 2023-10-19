<?php

namespace App\Policies;

use App\Models\Core\User;
use App\Models\Report\Dashboard;

class ReportDashboardPolicy
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
        return $user->can('reporting-dashboard.manage');
    }

    public function view(User $user, Dashboard $dashboard)
    {
        return $user->can('reporting-dashboard.manage');
    }

    public function store(User $user)
    {
        return $user->can('reporting-dashboard.manage');
    }

    public function update(User $user, Dashboard $dashboard)
    {
        return $user->can('reporting-dashboard.manage');
    }

    public function delete(User $user, Dashboard $dashboard)
    {
        return $user->can('reporting-dashboard.manage');
    }

}
