<?php

namespace App\Notifications\Warranty;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;

class WarrantyExportNotification extends Notification
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
        $subject = 'Your Warranty report is ready';
        $fullFilePath = storage_path('app/public/'.config('warranty.record_path').$this->path);

        $mail = (new MailMessage)
                ->from('noreply@weingartz.com')
                ->subject($subject)
                ->line('Your warranty report is ready to download. Please find the attachment in the email');

                $mail->attach($fullFilePath, [
                    'as' => 'warranty_report_'.now()->format('Y_m_d').'.csv',
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
        ];
    }
}
