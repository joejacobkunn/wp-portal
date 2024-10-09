<?php

namespace App\Notifications\Orders;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;

class OrdersImportNotification extends Notification
{
    use Queueable;

    public $path;

    /**
     * Create a new notification instance.
     */
    public function __construct($path = null)
    {
        $this->path = $path;
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
        $subject = 'Order Export Notification';
        $fullFilePath = storage_path('app/public/'.config('order.url').$this->path);

        $mail = (new MailMessage)
                ->from('noreply@weingartz.com')
                ->subject($subject)
                ->line('Order Export process completed');

                $mail->attach($fullFilePath, [
                    'as' => 'order-export.csv',
                ]);

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
            // Additional data can be returned here if needed
        ];
    }
}
