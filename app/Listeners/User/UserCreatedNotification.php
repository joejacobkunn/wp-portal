<?php

namespace App\Listeners\User;

use App\Events\User\UserCreated;
use Illuminate\Notifications\Notifiable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\User\SendInvitationEmail;

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
     * @param  \App\Events\User\UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $user = $event->user;
        $this->emailAddress = $user->email;
        $user->setResetToken();
        $this->notify(new SendInvitationEmail($user));
    }
}
