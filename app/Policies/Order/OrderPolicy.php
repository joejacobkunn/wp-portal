<?php

namespace App\Policies\Order;

use App\Models\Core\User;
use App\Models\Order\Order;
use Illuminate\Auth\Access\Response;

class OrderPolicy
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
        return $user->can('order.view')
                ? true
                : false;
    }

    public function view(User $user, Order $order)
    {
        return $user->can('order.view')
            ? true
            : false;

    }

    public function manage(User $user, Order $order): bool
    {
        return $user->can('order.manage')
            ? true
            : false;

    }




}
