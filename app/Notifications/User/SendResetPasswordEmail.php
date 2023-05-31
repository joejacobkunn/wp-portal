<?php

namespace App\Notifications\User;

use App\Models\Core\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendResetPasswordEmail extends Notification
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
        $subdomain = $this->user->isMasterAdmin() ? 'admin' : $this->account->subdomain;

        return (new MailMessage)
            ->subject('Reset Password')
            ->greeting('Hello!')
            ->line('WP-Portal received a request to reset your password, please click the link below, or copy and paste in your browser.')
            ->action('Reset', route('auth.password.show_reset', [
                'route_subdomain' => $subdomain,
                'c' => base64_encode($this->user->metadata->user_token),
            ]))
            ->line("If you don't want to reset your password, please ignore this message and your password will not be changed.");
    }
}
