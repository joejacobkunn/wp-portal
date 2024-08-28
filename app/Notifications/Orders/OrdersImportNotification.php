<?php

namespace App\Notifications\Orders;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class OrdersImportNotification extends Notification
{
    use Queueable;

    public $totalCount;
    public $failedCount;
    public $path;

    /**
     * Create a new notification instance.
     */
    public function __construct($totalCount, $failedCount, $path=null)
    {
        $this->totalCount = $totalCount;
        $this->failedCount = $failedCount;
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
        $subject = 'Order Import Notification';

        $mail = (new MailMessage)
                ->from('noreply@weingartz.com')
                ->subject($subject)
                ->line('Order import process completed')
                ->line('Summary')
                ->line(new HtmlString(
                    '<ul>
                    <li> Total processed : '.$this->totalCount -$this->failedCount .'/'.$this->totalCount.'</li>
                    <li> Failed : '.$this->failedCount.'</li>
                    '
                ));
        if ($this->path) {

            $mail->action('Download Failed Records', url('/').'/storage/'.$this->path);

        }


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
