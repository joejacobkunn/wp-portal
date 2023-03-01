<?php

namespace App\Policies\Core;

use App\Models\Core\Account;
use App\Models\Core\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function before(User $user, $ability)
    {
        if ($user->hasRole('master-admin')) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return $user->can('accounts.view');
    }

    public function view(User $user, Account $account)
    {
        return $user->can('accounts.view');
    }

    public function store(User $user)
    {
        return $user->can('accounts.manage');
    }

    public function update(User $user, Account $account)
    {
        return $user->can('accounts.manage');
    }

    public function delete(User $user, Account $account)
    {
        return $user->can('accounts.manage');
    }
}
