<?php

namespace App\Http\Livewire\Pwa;


use App\Classes\Fortis;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Core\Location;
use Illuminate\Support\Facades\DB;
use App\Http\Livewire\Component\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{
    use AuthorizesRequests, LivewireAlert;

    public $terminals = [];

    public $selectedTerminal;

    public $pageLoaded = false;

    public $selectedOrder;

    public $orderInProcess = false;

    public $checkSum = '';

    public function mount()
    {

    }

    public function render()
    {
        return $this->renderView('livewire.pwa.order', [], 'layouts.pwa');
    }

    public function loadTerminals()
    {
        $location = Location::where('location', auth()->user()->office_location)->first();

        if(!empty($location->fortis_location_id)) {
            $fortis = app()->make(Fortis::class);
            $terminalData = json_decode($fortis->fetchTerminals(auth()->user()->location()->fortis_location_id), true);
            $this->terminals = [];
    
            foreach ($terminalData['list'] as $index => $terminal) {
                if ($terminal['active']) {
                    $this->terminals[$index]['id'] = $terminal['id'];
                    $this->terminals[$index]['title'] = $terminal['title'];
                    $this->terminals[$index]['location_id'] = $terminal['location_id'];
                    $this->terminals[$index]['active'] = $terminal['active'];
                    $this->terminals[$index]['is_provisioned'] = $terminal['is_provisioned'];
                    $this->terminals[$index]['available'] = $terminal['active'] && !$terminal['is_provisioned'];
                }
            }
        }

        $this->pageLoaded = true;
    }

    public function setTerminal($terminalId)
    {
        $this->selectedTerminal = $terminalId;
    }

    public function checkPendingPayment(Request $request)
    {
        $order = $this->fetchPendingPayment($request);

        $this->reset(
            'selectedOrder',
            'orderInProcess',
        );

        if (!empty($order)) {
            $this->selectedOrder = $order;
            $this->orderInProcess = true;
        } else {
            return $this->alert('info', 'No orders pending payment are found with '. auth()->user()->name .' at '. auth()->user()->office_location);
        }
    }

    protected function fetchPendingPayment($request)
    {
        $whse = 'utic';
        $operator = auth()->user()->operator_id;

        if(config('sx.mock')) return $this->mock(__FUNCTION__, $request);


        $order = DB::connection('sx')->select("SELECT TOP 1 arsc.name, arsc.phoneno, arsc.email, arsc.custtype, arsc.custno, h.orderno , h.ordersuf, h.stagecd , h.totinvamt , h.tendamt , h.tottendamt, h.shiptonm, h.shiptost, h.shiptozip , h.shiptocity, oeeh.shiptoaddr[1] AS 'address', oeeh.shiptoaddr[2] AS 'address2'
                                        FROM pub.oeeh h
                                        LEFT JOIN pub.arsc
                                        ON arsc.cono = h.cono
                                        AND arsc.custno = h.custno
                                        WHERE h.cono = 10
                                        AND h.whse = '".$whse."'
                                        AND h.stagecd IN (1,3)
                                        AND h.openinit = '".$operator."'
                                        AND h.totinvamt - h.tendamt > 0
                                        WITH(nolock)");

        return $order;
    }

    public function mock($function, $request)
    {
        $faker = \Faker\Factory::create();
       // sleep(1.5);
        
        if($function == 'fetchPendingPayment')
        {
            $response = '';
            if (rand(0, 1)) {
                $response= [
                    'name' => $faker->name(),
                    'phoneno' => $faker->e164PhoneNumber(),
                    'email' => $faker->email(),
                    'custtype' => 'mun', //hide
                    'custno' => $faker->randomNumber(8, true), //non editable
                    'orderno' => $faker->randomNumber(7, true), //non editable
                    'ordersuf' => 0, 
                    'stagecd' => 5, //hide
                    'totinvamt' => $faker->randomFloat(2),  //non editable
                    'tendamt' => '0.00',  //hide
                    'tottendamt' => '0.00',  //hide
                    'shiptonm' => $faker->name(),
                    'address' => $faker->streetAddress(),
                    'address2' => $faker->secondaryAddress() ,
                    'shiptocity' => $faker->city(),
                    'shiptost' => $faker->stateAbbr(),
                    'shiptozip' => $faker->postcode(),
                ];
             }

             return $response;

        }
    }

    public function processTransaction()
    {
        $orderData = [
            'checkin_date' => date('Y-m-d'),
            'checkout_date' => date('Y-m-d'),
            'clerk_number' => auth()->user()->sx_operator_id,
            'custom_data' => [
                'sx_order_id' => $this->selectedOrder['orderno'],
                'portal_user_id' => auth()->user()->id
            ],
            'location_id' => auth()->user()->location()->fortis_location_id,
            'transaction_amount' => (int) ($this->selectedOrder['totinvamt'] * 100),
            'customer_id' =>  (string) $this->selectedOrder['custno'],
            'terminal_id' => $this->selectedTerminal
        ];

        $fortis = app()->make(Fortis::class);
        $transaction = $fortis->terminalSale($orderData);

        if ($transaction['type'] == 'TransactionProcessing') {
            $this->checkSum = sha1(Str::random(25));

            return [
                'status' => 'success',
                'orderNo' => $this->selectedOrder['orderno'],
                'transactionCode' => $transaction['data']['async']['code'],
                'checkSum' => $this->checkSum,
            ];
        }

        $this->alert('error', 'Error Code: ' . $transaction['statusCode']. ' ' . $transaction['detail']);

        return [
            'status' => 'error',
            'statusCode' => $transaction['statusCode'],
            'message' => $transaction['detail'],
        ];
    }

    public function getTransactionStatus($checkSum, $orderNo, $transcationCode)
    {
        //skip if canceled order
        if (!$this->selectedOrder) {
            return;
        }

        if ($this->selectedOrder['orderno'] != $orderNo || $this->checkSum != $checkSum) {
            abort(403, 'Invalid request');
        }

        $fortis = app()->make(Fortis::class);
        $orderData = $fortis->transactionStatus($transcationCode);

        if (isset($orderData['data']['id']) && $orderData['data']['progress'] == 100) {
            $this->alert('success', 'Order successfully placed!');

            $this->reset(
                'selectedOrder',
                'orderInProcess',
                'checkSum',
            );

            return [
                'status' => 'success',
                'order_status' => $orderData['data']['progress'],
            ];
        }

        if (isset($orderData['type']) && strtolower($orderData['type']) == 'error') {
            $this->alert('error', 'Error Code: ' . $orderData['title']. ' ' . ($orderData['detail'] ?? ''));   

            return [
                'status' => 'success',
                'detail' =>'Error Code: ' . $orderData['title']. ' ' . ($orderData['detail'] ?? ''),
            ];
        }

    }

    public function cancelTransaction()
    {
        //@TODO
        //check if any terminal sale cancel required

        $this->reset(
            'selectedOrder',
            'orderInProcess',
            'checkSum',
        );


        $this->alert('info', 'Last transaction canceled.');   

        $this->dispatch('browser:transaction-cancelled');
    }
}
