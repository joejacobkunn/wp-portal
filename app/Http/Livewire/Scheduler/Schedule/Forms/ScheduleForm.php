<?php
 namespace App\Http\Livewire\Scheduler\Schedule\Forms;

use App\Classes\SX;
use App\Contracts\DistanceInterface;
use App\Events\Scheduler\EventCancelled;
use App\Events\Scheduler\EventComplete;
use App\Events\Scheduler\EventDispatched;
use App\Events\Scheduler\EventRescheduled;
use App\Events\Scheduler\EventScheduled;
use App\Models\Core\CalendarHoliday;
use App\Models\Core\Warehouse;
use App\Models\Order\Order;
use App\Models\Scheduler\NotificationTemplate;
use App\Models\Scheduler\Schedule;
use App\Models\Scheduler\Shifts;
use App\Models\Scheduler\TruckSchedule;
use App\Models\Scheduler\Zipcode;
use App\Models\Scheduler\Zones;
use App\Models\SX\Order as SXOrder;
use App\Models\SX\OrderLineItem;
use App\Rules\ValidateScheduleDate;
use App\Rules\ValidateScheduleTime;
use App\Rules\ValidateSlotsforSchedule;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Livewire\Form;

class ScheduleForm extends Form
{
    public ?Schedule $schedule;

    public $type;
    public $sx_ordernumber;
    public $suffix;
    public $schedule_date;
    public $schedule_time;
    public $status;
    public $orderInfo;
    protected $SXOrderInfo;
    public $zipcodeInfo;
    public $created_by;
    public $shipping;
    public $orderTotal;
    public $disabledDates;
    public $truckSchedules = [];
    public $enabledDates = [];
    public $scheduleType;
    public $ServiceStatus = false;
    public $serialNumbers;
    public $line_item;
    public $serviceZip;
    public $notes;
    public $addressKey = '1232234';
    public $service_address;
    public $service_address_temp; //used for updating address
    public $cancel_reason;
    public $reschedule_reason;
    public $unconfirmedAddressTypes;
    public $showAddressModal;
    public $not_purchased_via_weingartz;
    public $addressVerified = false;
    public $addressFromOrder;
    public $selectedTruckSchedule;
    public $recommendedAddress;
    public $notifyUser = true;
    public $phone;
    public $email;

    public $alertConfig = [
        'status' => false,
        'message' => '',
        'icon' => '',
        'class' => '',
        'show_url' => false,
        'url' => '',
        'params' => '',
        'urlText' => '',
    ];



    public function delete()
    {
        $this->schedule->delete();
    }

    public function getScheduledId()
    {
        return $this->schedule?->id ?? null;
    }

    public function getTotalInvoiceData($items, $sx_customer_number, $whse )
    {
        $invoice_request = [
            'sx_operator_id' => auth()->user()->sx_operator_id,
            'sx_customer_number' => $sx_customer_number ?? 1,
            'warehouse' => $whse,
            'cart' => $items,
        ];

        $sx = new SX();
        return $sx->get_total_invoice_data($invoice_request);
    }

    public function getActiveDays()
    {
        $holidays = CalendarHoliday::listAll();
        return ['holidays' => $holidays];
    }

    public function getDistance()
    {
        $google = app(DistanceInterface::class);
        $warehouse = Warehouse::where('short' , $this->orderInfo->whse)->first();

        $distance = $google->findDistance($warehouse->address, $this->service_address);

        if ($distance['status'] === 'OK') {
            $elements = $distance['rows'][0]['elements'][0] ?? null;
            $this->shipping['distance'] = $elements['distance']['text'] ?? null;
            $this->shipping['duration'] =   $elements['duration']['text'] ?? null;
        }
    }

    public function getSerialNumbers($orderno, $suffix)
    {
        if (config('sx.mock')) {
            $serials = [];
            foreach ($this->orderInfo->line_items['line_items'] as $item) {
                if (mt_rand(0, 1)) {
                    $obj = new \stdClass();
                    $obj->prod = $item['shipprod'];
                    $obj->serialno = rand(100000, 999999);
                    $serials[] = $obj;
                }
            }
            return $serials;
        }

        return DB::connection('sx')->select("select s.prod,s.serialno from pub.icets s where s.cono = ? and s.ordertype = 'o' and s.orderno = ? and s.ordersuf = ? with(nolock)",[10, $orderno, $suffix]);
    }

    public function extractZipCode($address)
    {
        preg_match('/\b[A-Z]{2}\s+(\d{5})\b/', $address, $matches);

        return $matches[1] ?? null;
    }

    protected function syncSXOrder($sx_order)
    {
        $line_items = $this->getSxOrderLineItemsProperty($sx_order['orderno'],$sx_order['ordersuf']);
        $wt_status = $this->checkForWarehouseTransfer($sx_order,$line_items);

        $portal_order = Order::updateOrCreate(
            [
                'order_number' => $sx_order['orderno'],
                'order_number_suffix' => $sx_order['ordersuf'],
                'cono' => $sx_order['cono']
            ],
            [
                'whse' => strtolower($sx_order['whse']),
                'taken_by' => $sx_order['takenby'],
                'order_date' => Carbon::parse($sx_order['enterdt'])->format('Y-m-d'),
                'stage_code' => $sx_order['stagecd'],
                'sx_customer_number' => $sx_order['custno'],
                'is_sro' => $this->isSro($sx_order,$line_items->toArray()),
                'ship_via' => strtolower($sx_order['shipviaty']),
                'qty_ship' => $sx_order['totqtyshp'],
                'qty_ord' => $sx_order['totqtyord'],
                'promise_date' => $sx_order['promisedt'],
                'line_items' => ['line_items' => $line_items->toArray() ?: []],
                'is_sales_order' => $this->isSales($line_items->toArray()),
                'is_web_order' => $sx_order['user6'] == '6' ? 1 : 0,
                'warehouse_transfer_available' => ($wt_status == 'wt') ? true : false,
                'partial_warehouse_transfer_available' => ($wt_status == 'p-wt') ? true : false,
                'golf_parts' => $sx_order['user6'] == '6' ? $sx_order->hasGolfParts($line_items) : null,
                'non_stock_line_items' => $sx_order->hasNonStockItems($line_items),
                'last_line_added_at' => Carbon::parse($sx_order['enterdt'])->format('Y-m-d'),
                'status' => 'Pending Review',
                'shipping_info' => $sx_order->constructAddress($sx_order)
            ]
        );

        return $portal_order;

    }

    protected function getSxOrderLineItemsProperty($order_number, $order_suffix, $cono = 10)
    {
        $required_line_item_columns = [
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
            'oeel.cono',
            'oeel.enterdt',
        ];

        return OrderLineItem::select($required_line_item_columns)
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
        ->where('oeel.orderno', $order_number)->where('oeel.ordersuf', $order_suffix)
        ->where('oeel.cono', $cono)
        ->orderBy('oeel.lineno', 'asc')
        ->get();
    }

    protected function isSales($line_items)
    {
        foreach($line_items as $line_item)
        {
            if(!str_contains($line_item['prodline'], 'LR-E') && str_contains($line_item['prodline'], '-E'))
            {
                return true;
            }
        }

        return false;
    }

    protected function isSro($order,$line_items)
    {
        $is_sro = $order['user1'] == 'SRO' ? 1 : 0;

        if($is_sro) return true;

        foreach($line_items as $line_item)
        {
            if(str_contains($line_item['prodline'], 'LR-E'))
            {
                return true;
            }
        }

        return false;

    }

    protected function checkForWarehouseTransfer($sx_order, $line_items)
    {
        $line_item_level_statuses = [];

        if($sx_order->isBackOrder())
        {
            foreach($line_items as $line_item)
            {
                $backorder_count = intval($line_item->stkqtyord) - intval($line_item->stkqtyship);

                if($backorder_count > 0 && strtolower($line_item->ordertype) != 't')
                {
                    $inventory_levels = $line_item->checkInventoryLevelsInWarehouses(array_diff(['ann','ceda','farm','livo','utic','wate', 'zwhs', 'ecom'], [strtolower($line_item->whse)]));

                    foreach($inventory_levels as $inventory_level)
                    {
                        $available_stock = $inventory_level->qtyonhand - ($inventory_level->qtycommit + $inventory_level->qtyreservd);

                        if($available_stock >= $backorder_count) $line_item_level_statuses[] = 'wt'; //full wt available
                        if(!($available_stock >= $backorder_count) && $available_stock > 0) $line_item_level_statuses[] = 'p-wt'; //partial wt transfer

                    }
                }
            }
        }

        if(in_array('wt',$line_item_level_statuses)) return 'wt';
        if(in_array('p-wt',$line_item_level_statuses)) return 'p-wt';

        return 'n/a';

    }

    public function updateContact()
    {
        $this->schedule->fill([
            'email' => $this->email,
            'phone' => $this->phone
        ]);

        $this->schedule->save();
    }

}
