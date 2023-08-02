<?php

namespace App\Policies\Core;

use App\Models\Core\Customer;
use App\Models\Core\User;
use Illuminate\Auth\Access\Response;

class CustomerPolicy
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
        // @TODO Commented because for admin showing domain error
        // if ($user->hasRole('master-admin')) {
        //     return true;
        // }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('customers.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Customer $customer): bool
    {
        abort_if(!$this->hasAccess($user, $customer), 401);

        return $user->can('customers.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('locations.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Customer $customer): bool
    {
        abort_if(!$this->hasAccess($user, $customer), 401);

        return $user->can('locations.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Customer $customer): bool
    {
        abort_if(!$this->hasAccess($user, $customer), 401);

        return $user->can('locations.manage');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Customer $customer): bool
    {
        abort_if(!$this->hasAccess($user, $customer), 401);

        return $user->can('locations.manage');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Customer $customer): bool
    {
        abort_if(!$this->hasAccess($user, $customer), 401);

        return $user->can('locations.manage');
    }

    //Private functions
    private function hasAccess(User $user, Customer $customer)
    {
        return $user->account_id == $customer->account_id;
    }
}
