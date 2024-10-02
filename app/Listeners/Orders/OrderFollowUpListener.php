<?php

namespace App\Listeners\Orders;

use App\Classes\SX;
use App\Events\Orders\OrderFollowUp;
use App\Models\Core\User;
use App\Models\Order\Message;
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

            if($this->eligibleForEmail($event) && $event->email_enabled)
            {
                Notification::route('mail', App::environment() == 'production' ? $event->email : "mmeister@powereqp.com")
                ->notify(new OrderFollowUpNotification($event->order, $event->mailSubject, $event->mailContent, $event->customer_name));

                Message::create([
                    'order_number' => $event->order->order_number,
                    'order_suffix' => $event->order->order_number_suffix,
                    'medium' => 'email',
                    'subject' => $event->mailSubject,
                    'content' => $event->mailContent,
                    'status' => $event->order->status,
                    'contact' => $event->email
                ]);

                            //add custom log

            activity()
            ->causedBy(User::find($event->order->last_updated_by))
            ->performedOn($event->order)
            ->event('custom')
            ->log('Sent '.$event->order->status->value.' Email "'.$event->mailSubject);

    
            }



                     //sent sms
         
         if($event->sms_enabled)
         {
            $kenect = new Kenect();
            $kenect->send($event->sms_phone, $event->sms_message);
            
            activity()
            ->causedBy(User::find($event->order->last_updated_by))
            ->performedOn($event->order)
            ->event('custom')
            ->log('Sent text to phone '.$event->sms_phone.' ('.$event->order->status->value.')');


            Message::create([
                'order_number' => $event->order->order_number,
                'order_suffix' => $event->order->order_number_suffix,
                'medium' => 'sms',
                'subject' => 'SMS via Kenect',
                'content' => $event->sms_message,
                'status' => $event->order->status,
                'contact' => $event->sms_phone
            ]);

         }

    
            //add note to sx order notes if there was a follow up
            if(!App::environment('local'))
            {
                $sx_client = new SX();
                $operator = User::find($event->order->last_updated_by);
                $sx_response = $sx_client->create_order_note($event->order->status->value.' by '.$operator->name.'('.$operator->sx_operator_id.') via Portal on '.now()->toDayDateTimeString(),$event->order->order_number);
            }
    
    }

    private function eligibleForEmail($event)
    {
        if(empty($event->email)) return false;
        if(empty($event->mailContent)) return false;
        if(empty($event->mailSubject)) return false;

        return true;
    }
}
