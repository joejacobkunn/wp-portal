<?php

namespace App\Events\Scheduler;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventReminder
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $schedule;

    public $template;

    /**
     * Create a new event instance.
     */
    public function __construct($schedule, $template)
    {
        $this->schedule = $schedule;
        $this->template = $template;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
