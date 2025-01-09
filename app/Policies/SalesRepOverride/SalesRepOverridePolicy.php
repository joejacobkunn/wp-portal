<?php

namespace App\Policies\SalesRepOverride;

use App\Models\Core\User;
use App\Models\SalesRepOverride\SalesRepOverride;

class SalesRepOverridePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('customer.sales-rep-override.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SalesRepOverride $salesRepOverride): bool
    {
        return $user->can('customer.sales-rep-override.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function store(User $user, SalesRepOverride $salesRepOverride): bool
    {
        return $user->can('customer.sales-rep-override.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SalesRepOverride $salesRepOverride): bool
    {
        return $user->can('customer.sales-rep-override.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SalesRepOverride $salesRepOverride): bool
    {
        return $user->can('customer.sales-rep-override.manage');
    }
}
