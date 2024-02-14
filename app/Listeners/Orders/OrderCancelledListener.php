<?php

namespace App\Listeners\Orders;

use App\Classes\SX;
use App\Events\Orders\OrderCancelled;
use App\Models\Core\User;
use App\Notifications\Orders\OrderCancelledNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notifiable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;

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
     * Handle the event.
     *
     * @return void
     */
    public function handle(OrderCancelled $event): void
    {
    
        //send email

        Notification::route('mail', App::environment() == 'production' ? $event->email : "mmeister@powereqp.com")
                    ->notify(new OrderCancelledNotification($event->order, $event->mailSubject, $event->mailContent));

        //add custom log

        activity()
            ->causedBy(User::find($event->order->last_updated_by))
            ->performedOn($event->order)
            ->event('custom')
            ->log('Sent Email "'.$event->mailSubject.'" to customer');
    }
}
