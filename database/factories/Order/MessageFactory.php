<?php

namespace Database\Factories\Order;

use App\Models\Order\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * 
     * 
     */

    protected $model = Message::class;

    protected $mediums = ['sms', 'email'];

    protected $statuses = ['Follow Up', 'Cancelled'];

    public function definition(): array
    {
        return [
            'order_number' => fake()->randomNumber(8, true),
            'order_suffix' => 0,
            'medium' => $this->mediums[array_rand($this->mediums)],
            'subject' => fake()->sentence(),
            'content' => fake()->text(),
            'status' => $this->statuses[array_rand($this->statuses)],
            'contact' => fake()->email
        ];
    }
}
