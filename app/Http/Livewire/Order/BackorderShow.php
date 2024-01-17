<?php

namespace App\Http\Livewire\Order;

use App\Http\Livewire\Component\Component;
use App\Models\Order\DnrBackorder;
use App\Models\SX\Customer;
use App\Models\SX\Order;
use App\Models\SX\OrderLineItem;

class BackorderShow extends Component
{
    public Order $order;
    public $customer;
    public DnrBackorder $backorder;
    public $order_line_items = [];

    
    public $breadcrumbs = [
        [
            'title' => 'Backorders',
            'route_name' => 'order.index',
        ],
        [
            'title' => 'Order Details',
        ]

    ];


    public function render()
    {
        return $this->renderView('livewire.order.backorder-show');
    }

    public function mount($orderno, $ordersuf) 
    {
        $this->backorder = DnrBackorder::where('cono', auth()->user()->account->sx_company_number)->where('order_number', $orderno)->where('order_number_suffix', $ordersuf)->first();
        
        if(!config('sx.mock'))
        {
            $this->order = Order::where('cono', auth()->user()->account->sx_company_number)->where('orderno', $orderno)->where('ordersuf', $ordersuf)->first();
            $this->customer = Customer::where('cono', auth()->user()->account->sx_company_number)->where('custno', $this->backorder->sx_customer_number)->first();
            $this->order_line_items = OrderLineItem::where('cono', auth()->user()->account->sx_company_number)->where('orderno', $orderno)->where('ordersuf', $ordersuf)->get();
        }
    }
}
