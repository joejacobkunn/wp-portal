<?php

namespace App\Listeners\User;

use App\Events\User\UserCreated;
use App\Notifications\User\SendInvitationEmail;
use Illuminate\Notifications\Notifiable;

class UserCreatedNotification
{
    use Notifiable;

    public $emailAddress;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        return $this->emailAddress;
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $user = $event->user;
        $this->emailAddress = $user->email;
        $this->notify(new SendInvitationEmail($user));
    }
}
