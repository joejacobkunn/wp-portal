<?php

namespace App\Listeners\Orders;

use App\Classes\SX;
use App\Enums\Order\OrderStatus;
use App\Events\Orders\OrderCancelled;
use App\Models\Core\User;
use App\Notifications\Orders\OrderCancelledNotification;
use App\Services\Kenect;
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
                    ->notify(new OrderCancelledNotification($event->order, $event->mailSubject, $event->mailContent, $event->customer_name));

        //add custom log

        activity()
            ->causedBy(User::find($event->order->last_updated_by))
            ->performedOn($event->order)
            ->event('custom')
            ->log('Sent Cancellation Email "'.$event->mailSubject.'" to customer');


         //sent sms
         
         if($event->sms_enabled)
         {
            $kenect = new Kenect();
            $kenect->send($event->sms_phone, $event->sms_message);
         }

        //add note to sx order notes if cancelled

        if(!App::environment('local'))
        {
            $sx_client = new SX();
            $operator = User::find($event->order->last_updated_by);
            $sx_response = $sx_client->create_order_note('Order cancelled by '.$operator->name.'('.$operator->sx_operator_id.') via Portal due to no longer available parts',$event->order->order_number);
    
        }


    
    }
}
