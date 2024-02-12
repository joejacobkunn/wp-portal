<?php

namespace App\Listeners\Orders;

use App\Classes\SX;
use App\Events\Orders\OrderCancelled;
use App\Notifications\Orders\OrderCancelledNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notifiable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\App;

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
    public function routeNotificationForMail(OrderCancelled $event)
    {
        return App::environment() == 'production' ? $event->email : "mmeister@powereqp.com";
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(OrderCancelled $event): void
    {
        $this->notify(new OrderCancelledNotification($event->order, $event->mailSubject, $event->mailContent));
    }
}
