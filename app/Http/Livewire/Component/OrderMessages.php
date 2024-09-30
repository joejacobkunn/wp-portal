<?php

namespace App\Http\Livewire\Component;

use App\Models\Order\Message;
use Livewire\Component;
use Livewire\WithPagination;

class OrderMessages extends Component
{
    use WithPagination;

    public $orderId;
    public $orderSuffix;

    public function mount()
    {
    }
    public function render()
    {
        return view('livewire.component.order-messages', [
            'messages' => Message::paginate(10),
        ]);
    }
}
