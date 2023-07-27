<?php

namespace App\Notifications\User;

use App\Models\Core\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendInvitationEmail extends Notification
{
    use Queueable;

    public $user;

    public $account;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->account = $user->account;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if ($this->user->isMasterAdmin()) {
            $accountTitle = 'WP Portal';
            $subdomain = config('constants.admin_subdomain');
        } else {
            $accountTitle = $this->account->name;
            $subdomain = $this->account->subdomain;
        }

        return (new MailMessage)
            ->subject('Welcome to W&P Portal')
            ->greeting('Hello!')
            ->line("You have been set as the admin for {$accountTitle}, Please login to portal using microsoft account.")
            ->action('JOIN NOW', route('auth.login.view', [
                'route_subdomain' => $subdomain,
            ]));
    }
}
