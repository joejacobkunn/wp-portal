<?php

namespace App\Policies\Order;

use App\Models\Core\User;
use App\Models\Order\DnrBackorder;
use Illuminate\Auth\Access\Response;

class BackorderPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {

    }

    public function before(User $user, $ability)
    {
        if ($user->hasRole('super-admin') || $user->hasRole('master-admin')) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return $user->can('order.dnr-backorder.view')
                ? Response::allow()
                : Response::deny('You do not have permission to view backorders. Ask your administrator to grant permission and try again.');
    }

    public function view(User $user, DnrBackorder $backorder)
    {
        return $user->can('order.dnr-backorder.view')
            ? Response::allow()
            : Response::deny('You do not have permission to view backorders. Ask your administrator to grant permission and try again.');

    }

    public function manage(User $user, DnrBackorder $backorder): bool
    {
        return $user->can('order.dnr-backorder.manage')
            ? Response::allow()
            : Response::deny('You do not have permission to view backorders. Ask your administrator to grant permission and try again.');

    }




}
