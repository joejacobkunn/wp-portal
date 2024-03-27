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
use App\Models\Core\Warehouse;
use App\Models\Order\NotificationTemplate;
use App\Models\Order\Order;
use App\Models\SX\Operator;
use App\Models\SX\Shipping;
use App\Models\SX\WarehouseProduct;
use App\Services\Kinect;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Laravel\Telescope\Storage\EntryModel;
use Laravel\Telescope\Telescope;
use Illuminate\Support\Str;

class Show extends Component
{
    use LivewireAlert;

    public Order $order;
    public $cancelOrderModal = false;
    public $followUpModal = false;
    public $shippingModal = false;
    public $receivingModal = false;
    public $notificationModal = false;
    public $emailSubject;
    public $emailContent;
    public $order_number;
    public $order_suffix;
    public $errorMessage = '';
    public $currentSession = '';
    public $tiedOrderAcknowledgement = false;
    public $operator;
    public $order_is_cancelled_manually_via_sx = false;
    public $order_data_sync_timestamp;
    public $manualPlaceholders = [];

    public $emailFrom = 'orders@weingartz.com';
    public $shippingEmail = 'shipping@weingartz.com';
    public $receivingEmail;
    public $emailTo = '';
    public $smsPhone;
    public $smsMessage;
    public $templates = [];
    public $templateId;

    public $smsEnabled = true;
    public $emailEnabled = true;
    
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
        'closeModal',
        'templateChanged'
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
        'oeel.stkqtyord',
        'oeel.stkqtyship',
        'oeel.returnfl',
        'icsp.descrip',
        'icsl.user3',
        'icsl.whse',
        'icsl.prodline',
    ];

    public function rules()
    {
        return [
            'emailTo' => 'required|email',
            'emailSubject' => 'required',
            'emailContent' => 'required'
        ];
    }


    public function render()
    {
        return $this->renderView('livewire.order.show');
    }

    public function mount() 
    {
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

    public function getShippingProperty()
    {
        if(in_array($this->order->stage_code, [3,4,5])) return Shipping::select(['trackerno', 'freightamt', 'shipviaty', 'actweight', 'operinit'])->where('cono', auth()->user()->account->sx_company_number)->where('orderno',$this->order->order_number)->where('ordersuf',$this->order->order_number_suffix)->first();
        return null;
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
        $this->notificationModal = false;
    }

    public function toggleOrderStatus($status)
    {
        $this->emailContent = '';
        $this->emailSubject = '';
        $this->templateId = '';
        $this->emailTo = '';


        switch ($status) {
            case OrderStatus::PendingReview->value:
            case OrderStatus::Ignore->value:
                $this->order->status = $status;
                $this->order->last_updated_by = auth()->user()->id;
                $this->order->save();
                break;
            case OrderStatus::Cancelled->value:
                $this->templates = NotificationTemplate::active()->where('type','Cancelled')->get();
                $this->cancelOrderModal = true;
                $this->order_is_cancelled_manually_via_sx = $this->order->stagecd == 9 ? 1 : 0;
                $this->emailTo = $this->customer->email;
                $this->notificationModal = true;
                break;

            case OrderStatus::FollowUp->value:
                    $this->templates = NotificationTemplate::active()->where('type','Customer Follow Up')->get();
                    $this->emailTo = $this->customer->email;
                    $this->smsPhone = $this->customer->phoneno;
                    $this->followUpModal = true;   
                    $this->notificationModal = true;
                    break;

            case OrderStatus::ShipmentFollowUp->value:
                    $this->shippingModal = true;
                    $this->templates = NotificationTemplate::active()->where('type','Shipment Follow Up')->get();
                    $this->notificationModal = true;
                    break;

            case OrderStatus::ReceivingFollowUp->value:
                    $this->receivingModal = true;
                    $this->emailTo = $this->order->getWarehouseEmail();
                    $this->templates = NotificationTemplate::active()->where('type','Receiving Follow Up')->get();
                    $this->notificationModal = true;                    
                    break;
    
        }

    }

    public function cancelOrder()
    {
        $this->validate();
        $this->authorize('manage', $this->order);
        $this->order->status = OrderStatus::Cancelled->value;
        $this->order->stage_code = 9;
        $this->order->last_updated_by = auth()->user()->id;
        $this->order->save();
        $this->closePopup('cancelOrderModal');

        event(new OrderCancelled($this->order, $this->emailSubject, $this->emailContent, $this->emailTo));

    }

    public function sendEmail()
    {
        $this->authorize('manage', $this->order);
            $this->order->status = OrderStatus::FollowUp->value;
            $this->order->last_updated_by = auth()->user()->id;
            $this->order->last_followed_up_at = now();
            $this->order->save();
            $this->closePopup('followUpModal');
            event(new OrderFollowUp($this->order, $this->emailSubject, $this->emailContent, $this->emailTo));
    }

    public function sendShippingEmail()
    {
        $this->authorize('manage', $this->order);
        $this->order->status = OrderStatus::ShipmentFollowUp->value;
        $this->order->last_updated_by = auth()->user()->id;
        $this->order->last_followed_up_at = now();
        $this->order->save();
        $this->closePopup('shippingModal');
        event(new OrderFollowUp($this->order, $this->emailSubject, $this->emailContent, $this->shippingEmail));

    }

    public function sendReceivingEmail()
    {
        $this->authorize('manage', $this->order);
        $this->order->status = OrderStatus::ReceivingFollowUp->value;
        $this->order->last_updated_by = auth()->user()->id;
        $this->order->last_followed_up_at = now();
        $this->order->save();
        $this->closePopup('receivingModal');
        event(new OrderFollowUp($this->order, $this->emailSubject, $this->emailContent, $this->receivingEmail));
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


    public function getBackorderLineItemsProperty()
    {
        $backorders = [];

        foreach($this->sx_order_line_items as $item){

            $backorder_count = intval($item->stkqtyord) - intval($item->stkqtyship);

            if($backorder_count > 0)
            $backorders[] = [
                'shipprod' => $item->shipprod,
                'full_name' => $item->user3.' '.substr($item->shipprod,2).' '.trim($item->cleanDescription())
            ];

        }

        return $backorders;
    }


    public function newCommentCreated($comment)
    {

        if($this->order->status->value == OrderStatus::PendingReview->value)
        {
            $this->order->status = OrderStatus::FollowUp->value;
            $this->order->last_updated_by = auth()->user()->id;
            $this->order->last_followed_up_at = now();
            $this->order->save();
        }

        if(!App::environment('local'))
        {
            $sx_client = new SX();
            $sx_response = $sx_client->create_order_note($comment['comment'], $this->order->order_number);
        }


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
        $this->reset('receivingModal');
        $this->reset('notificationModal');
    }


    private function fillTemplateVariables($template)
    {
        $processed_template = $template;
        if(Str::contains($template,'[CustomerName]')) $processed_template = Str::replace('[CustomerName]', $this->customer->name, $template);
        if(Str::contains($template,'[CustomerEmail]')) $processed_template = Str::replace('[CustomerEmail]', $this->customer->email, $template);
        if(Str::contains($template,'[CustomerPhone]')) $processed_template = Str::replace('[CustomerPhone]', $this->customer->phone, $template);
        if(Str::contains($template,'[OrderNumber]')) $processed_template = Str::replace('[OrderNumber]', $this->order->order_number, $template);
        if(Str::contains($template,'[LineItems]')) $processed_template = Str::replace('[LineItems]', $this->formatLineItems($this->sx_order_line_items), $template);
        if(Str::contains($template,'[BackorderLineItems]')) $processed_template = Str::replace('[BackorderLineItems]', $this->formatOtherLineItems($this->backorder_line_items), $template);
        if(Str::contains($template,'[DNRItems]')) $processed_template = Str::replace('[DNRItems]', $this->formatOtherLineItems($this->dnr_line_items), $template);
        if(Str::contains($template,'[NonDNRItems]')) $processed_template = Str::replace('[NonDNRItems]', $this->formatOtherLineItems($this->non_dnr_line_items), $template);
        if(Str::contains($template,'[WarehousePhone]')) $processed_template = Str::replace('[WarehousePhone]', Warehouse::where('short', strtolower($this->order->whse))->first()->phone, $template);
        if(Str::contains($template,'[ShipVia]')) $processed_template = Str::replace('[ShipVia]', $this->shipping?->getCarrier() , $template);
        if(Str::contains($template,'[ShippingTrackingNumber]')) $processed_template = Str::replace('[ShippingTrackingNumber]', $this->shipping?->trackerno , $template);
        return $processed_template;
    }

    private function formatLineItems($line_items)
    {
        $html = '<ul>';
        
        foreach($line_items as $item)
        {
            $html .= '<li>'.$item->user3.' '.substr($item->shipprod,2).' '.trim($item->cleanDescription()).'</li>';
        }

        $html .= '</ul>';

        return $html;
    }

    private function formatOtherLineItems($line_items)
    {
        $html = '<ul>';
        
        foreach($line_items as $item)
        {
            $html .= '<li>'.$item['full_name'].'</li>';
        }

        $html .= '</ul>';

        return $html;
    }

    public function templateChanged($name, $value, $recheckValidation = false)
    {
        $this->fieldUpdated($name, $value, $recheckValidation);
        $template = NotificationTemplate::find($this->templateId);
        $this->emailSubject = $this->fillTemplateVariables($template->email_subject);
        $this->emailContent = $this->fillTemplateVariables($template->email_content);
        $this->smsMessage = $this->fillTemplateVariables($template->sms_content);
        //$this->manualPlaceholders = $this->getManualPlaceholders($this->emailContent);
    }

    public function getManualPlaceholders()
    {
        $pattern = '/\{([^}]*)\}/';

        if(preg_match_all($pattern, $this->emailContent, $matches)) {
         return $matches[0];
        }

        return [];
    }


}
