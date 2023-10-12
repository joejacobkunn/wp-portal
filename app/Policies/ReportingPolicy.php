<?php

namespace App\Policies;

use App\Models\Core\User;
use App\Models\Report\Report;

class ReportingPolicy
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
        return $user->can('reporting.view');
    }

    public function view(User $user, Report $report)
    {
        return $user->can('reporting.view');
    }

    public function store(User $user)
    {
        return $user->can('reporting.manage');
    }

    public function update(User $user, Report $report)
    {
        return $user->can('reporting.manage');
    }

    public function delete(User $user, Report $report)
    {
        return $user->can('reporting.manage');
    }

}
