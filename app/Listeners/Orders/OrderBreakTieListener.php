<?php

namespace App\Listeners\Orders;

use App\Classes\SX;
use App\Events\Orders\OrderBreakTie;
use App\Events\Orders\OrderCancelled;
use App\Models\Core\Comment;
use App\Notifications\Orders\OrderBreakTieNotification;
use App\Notifications\Orders\OrderCancelledNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notifiable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\App;

class OrderBreakTieListener
{
    use Notifiable;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function routeNotificationForMail()
    {
        return App::environment() == 'production' ? 'purchasing@wandpmanagement.com' : "mmeister@powereqp.com";
    }


    /**
     * Handle the event.
     */
    public function handle(OrderBreakTie $event): void
    {
        //first lets create a comment with the tie info
        $lines = [];

        foreach($event->tied_line_items as $line_item){
            $lines[] = 'Line Item #'.$line_item->lineno.' '.$line_item->shipprod. ' Tie to PO '.$line_item->orderaltno.' has been broken';
        }

        foreach($lines as $line)
        {
            Comment::create([
                'user_id' => $event->auth_user->id,
                'commentable_type' => 'App\Models\Order\DnrBackorder',
                'commentable_id' => $event->backorder->id,
                'comment' => $line,
            ]);
        }

        //send email

        $this->notify(new OrderBreakTieNotification($event->backorder, $lines));
    }
}
