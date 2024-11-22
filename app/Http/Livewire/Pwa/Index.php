<?php

namespace App\Http\Livewire\Pwa;


use App\Classes\Fortis;
use Illuminate\Http\Request;
use App\Models\Core\Location;
use Illuminate\Support\Facades\DB;
use App\Http\Livewire\Component\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use AuthorizesRequests, LivewireAlert;

    public $terminals = [];

    public $selectedTerminal;

    public $pageLoaded = false;

    public $selectedOrder;

    public $orderInProcess = false;

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
            $fortis = new Fortis();

            $this->terminals = $fortis->fetchTerminals($location->fortis_location_id);
        }

        $this->pageLoaded = true;
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
                    'custno' => $faker->randomNumber(6, true), //non editable
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
            'transaction_amount' => $this->selectedOrder['totinvamt'] * 100,
            'customer_id' => $this->selectedOrder['custno'],
            'terminal_id' => $this->selectedTerminal
        ];

        $fortis = app()->make(Fortis::class);
        $transaction = $fortis->terminalSale($orderData);
        $orderData = $fortis->transactionStatus($transaction['data']['async']['code']);

        if ($orderData['data']['id'] && $orderData['data']['progress'] == 100) {
            $this->alert('success', 'Order successfully placed!');

            $this->reset(
                'selectedOrder',
                'orderInProcess',
            );
        }
    }
}
