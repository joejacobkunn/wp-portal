<?php

namespace App\Http\Livewire\Order;

use App\Classes\SX;
use App\Models\SX\Order;
use App\Models\SX\Customer;
use App\Models\SX\OrderLineItem;
use App\Models\Order\DnrBackorder;
use App\Enums\Order\BackOrderStatus;
use App\Events\Orders\OrderBreakTie;
use App\Events\Orders\OrderCancelled;
use App\Http\Livewire\Component\Component;
use App\Models\Core\Comment;
use App\Models\SX\Operator;
use App\Models\SX\WarehouseProduct;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class BackorderShow extends Component
{
    use LivewireAlert;

    public DnrBackorder $backorder;
    public $cancelOrderModal = false;
    public $followUpModal = false;
    public $cancelEmailSubject;
    public $cancelEmailContent;
    public $followUpSubject;
    public $followUpEmailContent;
    public $order_number;
    public $order_suffix;
    public $errorMessage = '';
    public $currentSession = '';
    public $tiedOrderAcknowledgement = false;
    public $operator;
    public $order_is_cancelled_manually_via_sx = false;

    public $emailFrom = 'weborders@weingartz.com';
    public $emailTo = '';
    
    public $breadcrumbs = [
        [
            'title' => 'Backorders',
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
        return $this->renderView('livewire.order.backorder-show');
    }

    public function mount($orderno, $ordersuf) 
    {
        $this->order_number = $orderno;
        $this->order_suffix = $ordersuf;
        $this->backorder = DnrBackorder::where('cono', auth()->user()->account->sx_company_number)->where('order_number', $orderno)->where('order_number_suffix', $ordersuf)->first();
        $this->authorize('view', $this->backorder);

        
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
        return 'This Backorder is currently in <strong>'. $this->backorder->status->label().'</strong> status';
    }

    public function getCommentAlertProperty()
    {
        if($this->backorder->status->value == BackOrderStatus::PendingReview->value)
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
            case BackOrderStatus::PendingReview->value:
            case BackOrderStatus::Ignore->value:
                $this->backorder->status = $status;
                $this->backorder->last_updated_by = auth()->user()->id;
                $this->backorder->save();
                break;
            case BackOrderStatus::Cancelled->value:
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

            case BackOrderStatus::FollowUp->value:
                    $this->followUpModal = true;
                    $this->emailTo = $this->customer->email;
                    
                    if (! $this->followUpSubject) {
                        $this->followUpSubject = 'Follow Up on Order #'.$this->order_number;
                    }
    
                    if (! $this->followUpEmailContent) {
                        $this->followUpEmailContent = 'Hello , We regret to inform you that the manufacture has let us know that Part Number(s) : '.implode(", ",Arr::pluck($this->dnr_line_items,'full_name')).'  is no longer available without any replacement. I apologize for this inconvenience, Would you like to go ahead with your remaining parts : '.implode(", ",Arr::pluck($this->non_dnr_line_items,'full_name')).' ?';
                    }
    
                    break;
    
        }
    }

    public function cancelOrder()
    {
        $this->backorder->status = BackOrderStatus::Cancelled->value;
        $this->backorder->stage_code = 9;
        $this->backorder->last_updated_by = auth()->user()->id;
        $this->backorder->save();
        $this->reset('cancelOrderModal');

        event(new OrderCancelled($this->backorder, $this->cancelEmailSubject, $this->cancelEmailContent, $this->emailTo));

    }

    public function sendEmail()
    {
            $this->backorder->status = BackOrderStatus::FollowUp->value;
            $this->backorder->last_updated_by = auth()->user()->id;
            $this->backorder->save();
            $this->reset('followUpModal');
            event(new OrderCancelled($this->backorder, $this->followUpSubject, $this->followUpEmailContent, $this->emailTo));
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

        foreach($this->order_line_items as $item){
            
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

        foreach($this->order_line_items->whereNotIn('shipprod', Arr::pluck($this->dnr_line_items,'shipprod')) as $item){
            $non_dnrs[] = [
                'shipprod' => $item->shipprod,
                'full_name' => $item->user3.' '.substr($item->shipprod,2).' '.trim($item->cleanDescription())
            ];

        }

        return $non_dnrs;
    }


    public function newCommentCreated($comment)
    {

        if($this->backorder->status->value == BackOrderStatus::PendingReview->value)
        {
            $this->backorder->status = BackOrderStatus::FollowUp->value;
            $this->backorder->last_updated_by = auth()->user()->id;
            $this->backorder->save();
        }

        $sx_client = new SX();

        $sx_response = $sx_client->create_order_note($comment['comment'], $this->backorder->order_number);


    }

    //check to see if any end user has an active session for the order
    public function getCurrentSession()
    {
        return Order::select('openinit')->where('cono', auth()->user()->account->sx_company_number)->where('orderno', $this->order_number)->where('ordersuf', $this->order_suffix)->first()->openinit;
    }

    public function getOperatorInfo($operator)
    {
        return Operator::select('name', 'email', 'phoneno', 'modphoneno', 'site', 'slstitle')->where('cono',auth()->user()->account->sx_company_number)->where('slsrep',$operator)->first();
    }

    public function getIsTiedOrderProperty()
    {
        foreach($this->order_line_items as $item){
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

        event(new OrderBreakTie($this->backorder, $tied_line_items, auth()->user()));
    }

    public function moveToErrorsTab($error)
    {
        //update status to error
        $this->backorder->status = BackOrderStatus::Error->value;
        $this->backorder->save();

        //add comment about the error response
        Comment::create([
            'user_id' => auth()->user()->id,
            'commentable_type' => 'App\Models\Order\DnrBackorder',
            'commentable_id' => $this->backorder->id,
            'comment' => 'SX prevented cancellation : '.$error,
        ]);

        $this->reset('cancelOrderModal');
    }

    public function checkOrderCancelStatus()
    {
        $stage_code = Order::select('stagecd')->where('cono', auth()->user()->account->sx_company_number)->where('orderno', $this->order_number)->where('ordersuf', $this->order_suffix)->first()->stagecd;
        return ($stage_code == 9) ? $this->order_is_cancelled_manually_via_sx = true : $this->order_is_cancelled_manually_via_sx = false;
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
