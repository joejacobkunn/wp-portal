<?php

namespace App\Http\Livewire\Order;

use App\Models\SX\Order;
use App\Models\SX\Customer;
use App\Models\SX\OrderLineItem;
use App\Models\Order\DnrBackorder;
use App\Enums\Order\BackOrderStatus;
use App\Events\Orders\OrderCancelled;
use App\Http\Livewire\Component\Component;

class BackorderShow extends Component
{
    public Order $order;
    public $customer;
    public DnrBackorder $backorder;
    public $order_line_items = [];
    public $cancelOrderModal = false;
    public $cancelEmailSubject;
    public $cancelEmailContent;
    
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
                    $this->cancelEmailSubject = 'Order Cancelled!';
                }

                if (! $this->cancelEmailContent) {
                    $this->cancelEmailContent = 'We regret to inform you that the manufacture has let us know that Part Number: is no longer available without any replacement. I have cancelled the order and refunded your credit authorization, but your financial institution may hold the authorization for a short time. We apologize for any inconvenience this may cause. If you have any questions, please feel free to reply to this email.';
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

}
