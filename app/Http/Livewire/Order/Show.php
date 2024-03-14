<?php

namespace App\Http\Livewire\Order;

use App\Classes\SX;
use App\Models\SX\Order as SXOrder;
use App\Models\SX\Customer;
use App\Models\SX\OrderLineItem;
use App\Models\Order\DnrBackorder;
use App\Enums\Order\OrderStatus;
use App\Events\Orders\OrderBreakTie;
use App\Events\Orders\OrderCancelled;
use App\Events\Orders\OrderFollowUp;
use App\Http\Livewire\Component\Component;
use App\Models\Core\Comment;
use App\Models\Order\Order;
use App\Models\SX\Operator;
use App\Models\SX\WarehouseProduct;
use App\Services\Kinect;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Show extends Component
{
    use LivewireAlert;

    public Order $order;
    public $cancelOrderModal = false;
    public $followUpModal = false;
    public $shippingModal = false;
    public $receivingModal = false;
    public $cancelEmailSubject;
    public $cancelEmailContent;
    public $followUpSubject;
    public $followUpEmailContent;
    public $shippingSubject;
    public $shippingEmailContent;
    public $receivingSubject;
    public $receivingEmailContent;
    public $order_number;
    public $order_suffix;
    public $errorMessage = '';
    public $currentSession = '';
    public $tiedOrderAcknowledgement = false;
    public $operator;
    public $order_is_cancelled_manually_via_sx = false;

    public $emailFrom = 'orders@weingartz.com'; //oeehp, sro, add taken by number, sms
    public $shippingEmail = 'shipping@weingartz.com'; //uticarecieving@weingartz.com, follow up on recieving
    public $receivingEmail = 'shipping@weingartz.com';
    public $emailTo = '';
    
    public $breadcrumbs = [
        [
            'title' => 'Orders',
            'route_name' => 'order.index',
        ],
        [
            'title' => 'Order Details',
        ]

    ];

    protected $listeners = [
        'newCommentCreated',
        'clipboardCopied',
        'closeModal'
    ];

    public $required_line_item_columns = [
        'oeel.orderno',
        'oeel.ordersuf',
        'oeel.shipto',
        'oeel.lineno',
        'oeel.qtyord',
        'oeel.proddesc',
        'oeel.price',
        'oeel.shipprod',
        'oeel.statustype',
        'oeel.prodcat',
        'oeel.prodline',
        'oeel.specnstype',
        'oeel.qtyship',
        'oeel.ordertype',
        'oeel.netamt',
        'oeel.orderaltno',
        'oeel.user8',
        'oeel.vendno',
        'oeel.whse',
        'oeel.returnfl',
        'icsp.descrip',
        'icsl.user3',
        'icsl.whse',
        'icsl.prodline',
    ];


    public function render()
    {
        return $this->renderView('livewire.order.show');
    }

    public function mount() 
    {
        // $kinect = new Kinect();
        // dd($kinect->send('5863658884', 'hi this is a test'));
        $this->order_number = $this->order->order_number;
        $this->order_suffix = $this->order->order_number_suffix;
        $this->authorize('view', $this->order);
    }

    /**
     * Properties
     */
    public function getStatusAlertClassProperty()
    {
        return $this->order->status->class();
    }

    public function getStatusActionLabelProperty()
    {
        return $this->order->isPendingReview() ? 'Review' : 'Action';
    }

    public function getStatusAlertMessageProperty()
    {
        return 'This Order is currently in <strong>'. $this->order->status->label().'</strong> status';
    }

    public function getCommentAlertProperty()
    {
        if($this->order->status->value == OrderStatus::PendingReview->value)
            return 'Adding a comment will update this backorder to <strong>Follow Up</strong> status. All comments are internal only and will be synced to OEEH notes in SX.';
        else
            return 'All comments are internal only and will be synced to OEEH notes in SX.';
    }
    /** Properties Ends */

    public function closePopup($attr)
    {
        $this->{$attr} = false;
    }

    public function toggleOrderStatus($status)
    {
        switch ($status) {
            case OrderStatus::PendingReview->value:
            case OrderStatus::Ignore->value:
                $this->order->status = $status;
                $this->order->last_updated_by = auth()->user()->id;
                $this->order->save();
                break;
            case OrderStatus::Cancelled->value:
                $this->cancelOrderModal = true;
                $this->order_is_cancelled_manually_via_sx = $this->order->stagecd == 9 ? 1 : 0;
                $this->emailTo = $this->customer->email;

                
                if (! $this->cancelEmailSubject) {
                    $this->cancelEmailSubject = 'Order #'.$this->order_number.'-'.$this->order_suffix.' Cancelled';
                }

                if (! $this->cancelEmailContent) {
                    $this->cancelEmailContent = 'We regret to inform you that the manufacture has let us know that Part Number(s) : '.implode(', ',Arr::pluck($this->dnr_line_items,'full_name')).' is no longer available without any replacement. I have cancelled the order and refunded your credit authorization, but your financial institution may hold the authorization for a short time. We apologize for any inconvenience this may cause. If you have any questions, please feel free to reply to this email.';
                }

                $this->currentSession = $this->getCurrentSession();

                if(!empty($this->currentSession)){
                    $this->operator = $this->getOperatorInfo($this->currentSession);
                }


                break;

            case OrderStatus::FollowUp->value:
                    $this->followUpModal = true;
                    $this->emailTo = $this->customer->email;
                    
                    if (! $this->followUpSubject) {
                        $this->followUpSubject = 'Follow Up on Order #'.$this->order_number;
                    }
    
                    if (! $this->followUpEmailContent) {
                        $this->followUpEmailContent = 'Hello , We regret to inform you that the manufacture has let us know that Part Number(s) : '.implode(", ",Arr::pluck($this->dnr_line_items,'full_name')).'  is no longer available without any replacement. I apologize for this inconvenience, Would you like to go ahead with your remaining parts : '.implode(", ",Arr::pluck($this->non_dnr_line_items,'full_name')).' ?';
                    }
    
                    break;

            case OrderStatus::ShipmentFollowUp->value:
                    $this->shippingModal = true;
                    
                    if (! $this->shippingSubject) {
                        $this->shippingSubject = 'Follow Up on Shipment for Order #'.$this->order_number;
                    }
    
                    if (! $this->shippingEmailContent) {
                        $this->shippingEmailContent = 'All items are in stock for Order #'.$this->order_number.'. Please ship today if possible.';
                    }
    
                    break;
    
    
        }
    }

    public function cancelOrder()
    {
        $this->authorize('manage', $this->order);
        $this->order->status = OrderStatus::Cancelled->value;
        $this->order->stage_code = 9;
        $this->order->last_updated_by = auth()->user()->id;
        $this->order->save();
        $this->reset('cancelOrderModal');

        event(new OrderCancelled($this->order, $this->cancelEmailSubject, $this->cancelEmailContent, $this->emailTo));

    }

    public function sendEmail()
    {
        $this->authorize('manage', $this->order);
            $this->order->status = OrderStatus::FollowUp->value;
            $this->order->last_updated_by = auth()->user()->id;
            $this->order->save();
            $this->reset('followUpModal');
            event(new OrderFollowUp($this->order, $this->followUpSubject, $this->followUpEmailContent, $this->emailTo));
    }

    public function sendShippingEmail()
    {
        $this->authorize('manage', $this->order);
        $this->order->status = OrderStatus::ShipmentFollowUp->value;
        $this->order->last_updated_by = auth()->user()->id;
        $this->order->save();
        $this->reset('shippingModal');
        event(new OrderFollowUp($this->order, $this->shippingSubject, $this->shippingEmailContent, $this->shippingEmail));

    }

    public function getCustomerProperty()
    {
        return Customer::where('cono', auth()->user()->account->sx_company_number)->where('custno', $this->order->sx_customer_number)->first();
    }

    public function getSxOrderProperty()
    {
        return SXOrder::where('cono', auth()->user()->account->sx_company_number)->where('orderno', $this->order_number)->where('ordersuf', $this->order_suffix)->first();
    }


    public function getSxOrderLineItemsProperty()
    {
        return OrderLineItem::select($this->required_line_item_columns)
        ->leftJoin('icsp', function (JoinClause $join) {
            $join->on('oeel.cono','=','icsp.cono')
            ->on('oeel.shipprod', '=', 'icsp.prod');
                //->where('icsp.cono', $this->customer->account->sx_company_number);
        })
        ->leftJoin('icsl', function (JoinClause $join) {
            $join->on('oeel.cono','=','icsl.cono')
                ->on('oeel.whse', '=', 'icsl.whse')
                ->on('oeel.vendno', '=', 'icsl.vendno')
                ->on('oeel.prodline', '=', 'icsl.prodline');
        })
        ->where('oeel.orderno', $this->order_number)->where('oeel.ordersuf', $this->order_suffix)
        ->where('oeel.cono', auth()->user()->account->sx_company_number)
        ->orderBy('oeel.lineno', 'asc')
        ->get();
    }

    public function getDnrLineItemsProperty()
    {
        $dnrs = [];

        foreach($this->sx_order_line_items as $item){
            
            if (strtoupper($item->statustype) == 'A') {
                $available = $item->qtyship + $item->qtyrel;
                if ($item->qtyord != $available) {
                    $dnr_warehouse_product = WarehouseProduct::where('cono', 10)
                        ->where('whse', $item->whse)
                        ->where('prod', $item->shipprod)
                        ->where('statustype', 'X')
                        ->get();

                    if ($dnr_warehouse_product->isNotEmpty()) {
                        $dnrs[] = [
                            'shipprod' => $item->shipprod,
                            'full_name' => $item->user3.' '.substr($item->shipprod,2).' '.trim($item->cleanDescription())
                        ];
                    }
                }
            }

        }

        return $dnrs;
    }

    public function getNonDnrLineItemsProperty()
    {
        $non_dnrs = [];

        foreach($this->sx_order_line_items->whereNotIn('shipprod', Arr::pluck($this->dnr_line_items,'shipprod')) as $item){
            $non_dnrs[] = [
                'shipprod' => $item->shipprod,
                'full_name' => $item->user3.' '.substr($item->shipprod,2).' '.trim($item->cleanDescription())
            ];

        }

        return $non_dnrs;
    }


    public function newCommentCreated($comment)
    {

        if($this->order->status->value == OrderStatus::PendingReview->value)
        {
            $this->order->status = OrderStatus::FollowUp->value;
            $this->order->last_updated_by = auth()->user()->id;
            $this->order->save();
        }

        $sx_client = new SX();

        $sx_response = $sx_client->create_order_note($comment['comment'], $this->order->order_number);


    }

    //check to see if any end user has an active session for the order
    public function getCurrentSession()
    {
        return SXOrder::select('openinit')->where('cono', auth()->user()->account->sx_company_number)->where('orderno', $this->order_number)->where('ordersuf', $this->order_suffix)->first()->openinit;
    }

    public function getOperatorInfo($operator)
    {
        return Operator::select('name', 'email', 'phoneno', 'modphoneno', 'site', 'slstitle')->where('cono',auth()->user()->account->sx_company_number)->where('slsrep',$operator)->first();
    }

    public function getIsTiedOrderProperty()
    {
        foreach($this->sx_order_line_items as $item){
            if(in_array($item->getTied() ,['PO', 'Warehouse Transfer'])) return true;
        }
        
        return false;
    }

    public function breakTieForOrder()
    {
        $tied_line_items = [];

        foreach($this->order_line_items as $item){
            if(in_array($item->getTied() ,['PO', 'Warehouse Transfer'])) {
                $line_item = OrderLineItem::where('cono', auth()->user()->account->sx_company_number)->where('orderno', $item->orderno)->where('ordersuf', $item->ordersuf)->first();
                $tied_line_items[] = $line_item;
                DB::connection('sx')->statement("UPDATE pub.oeel SET orderaltno = 0, linealtno = 0, ordertype = '' WHERE cono = ".auth()->user()->account->sx_company_number." AND orderno = ".$item->orderno." AND ordersuf = ".$item->ordersuf." AND lineno =".$item->lineno);
            }
        }

        event(new OrderBreakTie($this->order, $tied_line_items, auth()->user()));
    }

    public function checkOrderCancelStatus()
    {
        $stage_code = SXOrder::select('stagecd')->where('cono', auth()->user()->account->sx_company_number)->where('orderno', $this->order_number)->where('ordersuf', $this->order_suffix)->first()->stagecd;
        return ($stage_code == 9 || App::environment('local')) ? $this->order_is_cancelled_manually_via_sx = true : $this->order_is_cancelled_manually_via_sx = false;
    }

    public function clipboardCopied()
    {
        $this->alert('success','Copied to clipboard');
    }

    public function closeModal()
    {
        $this->reset('cancelOrderModal');
        $this->reset('followUpModal');
    }


}
