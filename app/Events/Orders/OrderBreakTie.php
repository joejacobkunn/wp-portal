<?php

namespace App\Events\Orders;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderBreakTie
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $backorder;

    public $tied_line_items;

    public $auth_user;

    /**
     * Create a new event instance.
     */
    public function __construct($backorder, $tied_line_items, $auth_user)
    {
        $this->backorder = $backorder;
        $this->tied_line_items = $tied_line_items;
        $this->auth_user = $auth_user;
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
