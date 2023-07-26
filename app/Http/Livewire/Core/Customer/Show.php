<?php

namespace App\Http\Livewire\Core\Customer;

use App\Classes\SX;
use App\Http\Livewire\Component\Component;
use App\Models\Core\Customer;
use App\Models\SRO\Customer as SROCustomer;
use App\Models\SX\Order;
use App\Models\SX\OrderLineItem;
use App\Traits\HasTabs;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Database\Query\JoinClause;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Show extends Component
{
    use HasTabs, LivewireAlert;

    public Customer $customer;

    public $credit_status;

    public $sro_customer;

    public $customer_has_good_credit_status = true;

    public $notes = [];

    public $open_line_item_modal = false;

    protected $order_line_items = [];

    //protected $orders = [];

    protected $open_orders = [];

    public $order_details;

    public $past_orders = [];

    public SX $sx_client;

    public $required_order_columns = [
        'orderno',
        'ordersuf',
        'stagecd',
        'enterdt',
        'totqtyord',
        'totordamt',
        'whse',
        'transtype',
        'takenby',
        'totqtyshp',
        'user1 as is_sro',
        'refer',
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
        'icsp.descrip',
        'icsl.user3',
        'icsl.whse',
        'icsl.prodline',
    ];

    protected $listeners = ['closeModal', 'clipboardCopied'];

    public $breadcrumbs = [
        [
            'title' => 'Customers',
            'route_name' => 'core.customer.index',
        ],
    ];

    public $tabs = [
        'open_orders' => 'Open Orders',
        'other_orders' => 'Other Orders',
    ];

    public $tab;

    public $open_order_tab = true;

    public $past_order_tab = false;

    public $queryString = ['tab'];

    public function mount()
    {
        $this->sro_customer = SROCustomer::where('sx_customer_id', $this->customer->sx_customer_number)->first();
        array_push($this->breadcrumbs, ['title' => $this->customer->name]);
    }

    public function render()
    {
        return $this->renderView('livewire.core.customer.show', ['order_line_items' => $this->order_line_items]);
    }

    public function loadData()
    {
        $this->getCreditStatus();
        $this->getNotes();
        //$this->getOpenOrders();
    }

    public function getCreditStatus()
    {
        if (empty($this->credit_status)) {
            $sx_client = new SX();
            $this->credit_status = $sx_client->check_credit_status(['request' => ['companyNumber' => $this->customer->account->sx_company_number, 'customerNumber' => $this->customer->sx_customer_number, 'operatorInit' => 'wpa']]);
            if ($this->credit_status['message'] == 'NO SALES ALLOWED!' || str_contains($this->credit_status['message'], 'HOLD')) {
                $this->customer_has_good_credit_status = false;
            }
        }

    }

    public function getNotes()
    {
        if (empty($this->notes)) {
            $sx_client = new SX();
            $this->notes = $sx_client->get_notes(['request' => ['companyNumber' => $this->customer->account->sx_company_number, 'primaryKey' => $this->customer->sx_customer_number, 'operatorInit' => 'SRO', 'notesType' => 'c', 'requiredNotesOnlyFlag' => true, 'recordLimit' => 10]]);
        }
    }

    public function getOrdersProperty()
    {
        return Order::select($this->required_order_columns)
            ->where('custno', $this->customer->sx_customer_number)
            ->where('cono', $this->customer->account->sx_company_number)
            ->whereIn('enterdt', $this->getDatesFromRange(now()->subYear(5)->format('Y-m-d'), now()->format('Y-m-d'), $format = 'Y-m-d'))
            ->orderBy('enterdt', 'desc')
            ->get();
    }

    public function fetchOrderDetails($order_no, $order_suffix, $sro_number, $order_type)
    {
        $this->open_line_item_modal = true;
        if ($sro_number) {
            $this->order_line_items = $sro_number;
        } else {
            $this->order_line_items = OrderLineItem::select($this->required_line_item_columns)
                ->leftJoin('icsp', function (JoinClause $join) {
                    $join->on('oeel.shipprod', '=', 'icsp.prod')
                        ->where('icsp.cono', $this->customer->account->sx_company_number);
                })
                ->leftJoin('icsl', function (JoinClause $join) {
                    $join->on('oeel.vendno', '=', 'icsl.vendno')
                        ->where('icsl.cono', $this->customer->account->sx_company_number)
                        ->whereColumn('icsl.whse', '=', 'oeel.whse')
                        ->whereColumn('oeel.prodline', '=', 'icsl.prodline');
                })
                ->where('oeel.orderno', $order_no)->where('oeel.ordersuf', $order_suffix)
                ->where('oeel.cono', $this->customer->account->sx_company_number)
                ->orderBy('oeel.lineno', 'asc')
                ->get();

            if ($order_type == 'open-order') {
                $this->open_order_tab = true;
                $this->past_order_tab = false;
            } else {
                $this->open_order_tab = false;
                $this->past_order_tab = true;

            }

        }
    }

    public function closeModal()
    {
        $this->open_line_item_modal = false;
    }

    public function clipboardCopied()
    {
        $this->alert('success', 'Copied!');
    }

    private function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {

        // Declare an empty array
        $array = [];

        // Variable that store the date interval
        // of period 1 day
        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        // Use loop to store date into array
        foreach ($period as $date) {
            $array[] = $date->format($format);
        }

        // Return the array elements
        return $array;
    }
}
