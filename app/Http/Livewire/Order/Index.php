<?php

namespace App\Http\Livewire\Order;

use App\Http\Livewire\Component\Component;
use App\Models\Order\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\Environment\Domain;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    public $account;

    public $orders = [];

    public $breadcrumbs = [
        [
            'title' => 'Orders',
        ],
    ];

    public function render()
    {
        return $this->renderView('livewire.order.index');
    }

    public function mount()
    {
        $this->account = account();
        $this->orders = Order::where('cono',10)
                    ->where('invoicedt','=','2022-03-30')
                    ->where('whse', 'UTIC')
                    ->get();
    }
}
