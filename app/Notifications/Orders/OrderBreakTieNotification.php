<?php

namespace App\Notifications\Orders;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class OrderBreakTieNotification extends Notification
{
    use Queueable;

    public $backorder;

    public $lines;

    /**
     * Create a new notification instance.
     */
    public function __construct($backorder, $lines)
    {
        $this->backorder = $backorder;
        $this->lines = $lines;
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
            ->subject('Tied Order #'.$this->backorder->order_number.'-'.$this->backorder->order_number_suffix.' has been cancelled from a DNR backorder.')
            ->greeting('Hello!')
            ->line('Tied Order #'.$this->backorder->order_number.' has been cancelled. Please fix the tie at your earliest convenience. Details are given below:')
            ->line(new HtmlString("<br>"));

        foreach($this->lines as $line)
        {
            $mail->line($line);
        }

        $mail->line(new HtmlString("<br>"));

        $mail->line('To view more details about this backorder, you can visit the Portal by clicking the link below.');

        $mail->action('View in Portal', url('/orders/backorders/'.$this->backorder->order_number.'/'.$this->backorder->order_number_suffix.'/show'));

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
