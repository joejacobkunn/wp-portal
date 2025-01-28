<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\SX;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\Transformers\OrderSXTransfomer;
use App\Models\Core\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function create(OrderRequest $request)
    {
        $sx_client = new SX();

        $sx_response = $sx_client->create_order(new OrderSXTransfomer($request));

        if ($sx_response['status'] == 'success') {
            return response()->json(['status' => $sx_response['status'], 'order_id' => $sx_response['order_id']], 201);
        } else {
            return response()->json(['status' => 'error', 'message' => $sx_response['message']], 400);
        }

    }

    public function pendingPayment(Request $request)
    {
        $request->validate([
            'operator' => 'required',
            'whse' => ['required', Rule::in(Warehouse::where('cono',10)->pluck('short')->toArray())]
        ]);

        if(config('sx.mock')) return $this->mock(__FUNCTION__, $request);


        $order = DB::connection('sx')->select("SELECT TOP 1 arsc.name, arsc.phoneno, arsc.email, arsc.custtype, arsc.custno, h.orderno , h.ordersuf, h.stagecd , h.totinvamt , h.tendamt , h.tottendamt, h.shiptonm, h.shiptost, h.shiptozip , h.shiptocity, oeeh.shiptoaddr[1] AS 'address', oeeh.shiptoaddr[2] AS 'address2'
                                        FROM pub.oeeh h
                                        LEFT JOIN pub.arsc
                                        ON arsc.cono = h.cono
                                        AND arsc.custno = h.custno
                                        WHERE h.cono = 10
                                        AND h.whse = '".$request->whse."'
                                        AND h.stagecd IN (1,3)
                                        AND h.openinit = '".$request->operator."'
                                        AND h.totinvamt - h.tendamt > 0
                                        WITH(nolock)");

        return response()->json(['status' => 'success', 'data' => $order[0] ?? ''], 200);

    }

    public function mock($function, $request)
    {
        $faker = \Faker\Factory::create();
        sleep(1.5);
        
        if($function == 'pendingPayment')
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

             //initiate transacion

             return response()->json(['status' => 'success', 'data' => $response], 200);

        }

    }
}
