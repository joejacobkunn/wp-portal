<?php

namespace App\Events\Orders;

use App\Models\Core\Customer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCancelled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public $mailSubject;

    public $mailContent;

    public $email;

    public $customer_name;

    /**
     * Create a new event instance.
     */
    public function __construct($order, $mailSubject, $mailContent, $email)
    {
        $this->order = $order;
        $this->mailSubject = $mailSubject;
        $this->mailContent = $mailContent;
        $this->email = $email;
        $this->customer_name = Customer::where('sx_customer_number', $order->sx_customer_number)->first()?->name;
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
