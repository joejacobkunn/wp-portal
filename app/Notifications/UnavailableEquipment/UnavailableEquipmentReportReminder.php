<?php

namespace App\Notifications\UnavailableEquipment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UnavailableEquipmentReportReminder extends Notification
{
    use Queueable;

    public $report;

    /**
     * Create a new notification instance.
     */
    public function __construct($report)
    {
        $this->report = $report;
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
                    ->subject('Unavailable Equipment Report is due for '.$this->report->report_date->toFormattedDateString())
                    ->line('Please complete your unavailable/demo report by '.$this->report->report_date->addDays(7)->toFormattedDateString().'. Click on the below link to get started.')
                    ->action('Go to Report', 'https://ped.powerweb.app/equipment/unavailable/report/'.$this->report->id.'/show');
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
