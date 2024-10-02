<?php

namespace App\Http\Livewire\Component;

use App\Models\Order\Message;
use Livewire\Component;
use Livewire\WithPagination;

class OrderMessages extends Component
{
    use WithPagination;

    public $orderNumber;
    public $orderSuffix;

    public $orderMessageModal =false;
    public $selectedMessage;

    protected $listeners = [
        'closeOrderMessage' => 'closeOrderMessage'
    ];

    public function render()
    {
        return view('livewire.component.order-messages', [
            'messages' => Message::where('order_number',$this->orderNumber)->where('order_suffix',$this->orderSuffix)->latest()->paginate(10),
        ]);
    }

    public function viewMessage($id)
    {
        $this->orderMessageModal = true;
        $this->selectedMessage = Message::find($id);
    }

    public function closeOrderMessage()
    {
        $this->orderMessageModal = false;
    }
}
