<?php

namespace App\Listeners\Orders;

use App\Classes\SX;
use App\Events\Orders\OrderFollowUp;
use App\Models\Core\User;
use App\Notifications\Orders\OrderFollowUpNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\App;



class OrderFollowUpListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderFollowUp $event): void
    {
            //send email

            Notification::route('mail', App::environment() == 'production' ? $event->email : "mmeister@powereqp.com")
            ->notify(new OrderFollowUpNotification($event->order, $event->mailSubject, $event->mailContent, $event->customer_name));

            //add custom log

            activity()
            ->causedBy(User::find($event->order->last_updated_by))
            ->performedOn($event->order)
            ->event('custom')
            ->log('Sent Follow Up Email "'.$event->mailSubject);
    
            //add note to sx order notes if there was a follow up
    
            $sx_client = new SX();
            $operator = User::find($event->order->last_updated_by);
            $sx_response = $sx_client->create_order_note('Order Followed Up by '.$operator->name.'('.$operator->sx_operator_id.') via Portal : '.$event->mailSubject,$event->order->order_number);
    
    }
}
