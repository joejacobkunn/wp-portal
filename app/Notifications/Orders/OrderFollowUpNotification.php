<?php

namespace App\Notifications\Orders;

use Illuminate\Bus\Queueable;
use App\Enums\Order\OrderStatus;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderFollowUpNotification extends Notification
{
    use Queueable;

    public $order;

    public $mailSubject;

    public $mailContent;

    public $customerName;


    /**
     * Create a new notification instance.
     */
    public function __construct($order, $mailSubject, $mailContent, $customerName)
    {
        $this->order = $order;
        $this->mailSubject = $mailSubject;
        $this->mailContent = $mailContent;
        $this->customerName = ($this->order->status->value != OrderStatus::ShipmentFollowUp->value) ? $customerName : '';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
                ->from('orders@weingartz.com', 'Weingartz Orders')
                ->cc('orders@weingartz.com')
                ->subject($this->mailSubject)
                ->greeting('Hello '.ucwords(strtolower($this->customerName)))
                ->line(new HtmlString($this->mailContent));

        if(in_array($this->order->status->value,[OrderStatus::ShipmentFollowUp->value,OrderStatus::ReceivingFollowUp->value]))
        {
            $mail->action('View Order', route('order.show', $this->order->id));
        }

        $mail->salutation("\r\n Regards,  \r\n Weingartz Support.");

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
