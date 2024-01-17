<?php

namespace App\Listeners\Orders;

use App\Events\Orders\OrderCancelled;
use App\Notifications\Orders\OrderCancelledNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notifiable;
use Illuminate\Queue\InteractsWithQueue;

class OrderCancelledListener
{
    use Notifiable;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        return "jkunnummyalil@wandpmanagement.com";
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(OrderCancelled $event): void
    {
        $this->notify(new OrderCancelledNotification($event->order, $event->mailContent));
    }
}
