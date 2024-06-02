<?php

namespace App\Listeners\Orders;

use App\Classes\SX;
use App\Events\Orders\OrderFollowUp;
use App\Models\Core\User;
use App\Notifications\Orders\OrderFollowUpNotification;
use App\Services\Kenect;
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
            ->log('Sent '.$event->order->status->value.' Email "'.$event->mailSubject);


                     //sent sms
         
         if($event->sms_enabled)
         {
            $kenect = new Kenect();
            $kenect->send($event->sms_phone, $event->sms_message);
         }

    
            //add note to sx order notes if there was a follow up
            if(!App::environment('local'))
            {
                $sx_client = new SX();
                $operator = User::find($event->order->last_updated_by);
                $sx_response = $sx_client->create_order_note('Followed Up by '.$operator->name.'('.$operator->sx_operator_id.') via Portal on '.now()->toDayDateTimeString(),$event->order->order_number);
            }
    
    }
}
