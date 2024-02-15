<?php

namespace App\Notifications\Orders;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCancelledNotification extends Notification
{
    use Queueable;

    public $order;

    public $mailSubject;

    public $mailContent;

    /**
     * Create a new notification instance.
     */
    public function __construct($order, $mailSubject, $mailContent)
    {
        $this->order = $order;
        $this->mailSubject = $mailSubject;
        $this->mailContent = $mailContent;
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
        return (new MailMessage)
                ->from('orders@weingartz.com', 'Weingartz Web Orders')
                ->cc('orders@weingartz.com')
                ->subject($this->mailSubject)
                ->greeting('Hello!')
                ->line(nl2br($this->mailContent))
                ->salutation("\r\n\r\n Regards,  \r\n Weingartz Support.");
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
