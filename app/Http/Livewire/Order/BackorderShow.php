<?php

namespace App\Http\Livewire\Order;

use App\Models\SX\Order;
use App\Models\SX\Customer;
use App\Models\SX\OrderLineItem;
use App\Models\Order\DnrBackorder;
use App\Enums\Order\BackOrderStatus;
use App\Events\Orders\OrderCancelled;
use App\Http\Livewire\Component\Component;
use App\Models\SX\WarehouseProduct;

class BackorderShow extends Component
{
    public DnrBackorder $backorder;
    public $cancelOrderModal = false;
    public $cancelEmailSubject;
    public $cancelEmailContent;
    public $order_number;
    public $order_suffix;
    
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
        $this->order_number = $orderno;
        $this->order_suffix = $ordersuf;
        $this->backorder = DnrBackorder::where('cono', auth()->user()->account->sx_company_number)->where('order_number', $orderno)->where('order_number_suffix', $ordersuf)->first();
        
        if(!config('sx.mock'))
        {
            // $this->order = Order::where('cono', auth()->user()->account->sx_company_number)->where('orderno', $orderno)->where('ordersuf', $ordersuf)->first();
            // $this->customer = Customer::where('cono', auth()->user()->account->sx_company_number)->where('custno', $this->backorder->sx_customer_number)->first();
            // $this->order_line_items = OrderLineItem::where('cono', auth()->user()->account->sx_company_number)->where('orderno', $orderno)->where('ordersuf', $ordersuf)->get();
        }
    }

    /**
     * Properties
     */
    public function getStatusAlertClassProperty()
    {
        return $this->backorder->status->class();
    }

    public function getStatusActionLabelProperty()
    {
        return $this->backorder->isPendingReview() ? 'Review' : 'Action';
    }

    public function getStatusAlertMessageProperty()
    {
        return 'This Backorder is '. $this->backorder->status->label();
    }
    /** Properties Ends */

    public function closePopup($attr)
    {
        $this->{$attr} = false;
    }

    public function toggleOrderStatus($status)
    {
        switch ($status) {
            case BackOrderStatus::PendingReview->value:
            case BackOrderStatus::Ignore->value:
                $this->backorder->status = $status;
                $this->backorder->save();
                break;
            case BackOrderStatus::Cancelled->value:
                $this->cancelOrderModal = true;
                
                if (! $this->cancelEmailSubject) {
                    $this->cancelEmailSubject = 'Order #'.$this->order_number.'-'.$this->order_suffix.' Cancelled';
                }

                if (! $this->cancelEmailContent) {
                    $this->cancelEmailContent = 'We regret to inform you that the manufacture has let us know that Part Number(s) : '.implode(", ",$this->dnr_line_items).' is no longer available without any replacement. I have cancelled the order and refunded your credit authorization, but your financial institution may hold the authorization for a short time. We apologize for any inconvenience this may cause. If you have any questions, please feel free to reply to this email.';
                }

                break;
        }
    }

    public function cancelOrder()
    {
        $this->backorder->status = BackOrderStatus::Cancelled->value;
        $this->backorder->save();
        $this->reset('cancelOrderModal');

        event(new OrderCancelled($this->backorder, $this->cancelEmailSubject, $this->cancelEmailContent));
    }

    public function getCustomerProperty()
    {
        return Customer::where('cono', auth()->user()->account->sx_company_number)->where('custno', $this->backorder->sx_customer_number)->first();
    }

    public function getOrderProperty()
    {
        return Order::where('cono', auth()->user()->account->sx_company_number)->where('orderno', $this->order_number)->where('ordersuf', $this->order_suffix)->first();
    }


    public function getOrderLineItemsProperty()
    {
        return OrderLineItem::where('cono', auth()->user()->account->sx_company_number)->where('orderno', $this->order_number)->where('ordersuf', $this->order_suffix)->get();
    }

    public function getDnrLineItemsProperty()
    {
        $dnrs = [];

        $line_items = OrderLineItem::where('cono', auth()->user()->account->sx_company_number)->where('orderno', $this->order_number)->where('ordersuf', $this->order_suffix)->get();

        foreach($line_items as $item){
            
            if (strtoupper($item->statustype) == 'A') {
                $available = $item->qtyship + $item->qtyrel;
                if ($item->qtyord != $available) {
                    $dnr_warehouse_product = WarehouseProduct::where('cono', 10)
                        ->where('whse', $item->whse)
                        ->where('prod', $item->shipprod)
                        ->where('statustype', 'X')
                        ->get();

                    if ($dnr_warehouse_product->isNotEmpty()) {
                        $dnrs[] = $item->shipprod;
                    }
                }
            }

        }

        return $dnrs;
    }


}
