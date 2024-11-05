<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\SX;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\Transformers\OrderSXTransfomer;
use Illuminate\Support\Facades\DB;

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

    public function pendingPayment($request)
    {
        $request->validate([
            'operator' => 'required',
            'whse' => 'required'
        ]);

        $order = DB::connection('sx')->select("SELECT TOP 1 h.orderno , h.stagecd , h.totinvamt , h.tendamt , h.tottendamt, h.shiptonm, h.shiptost, h.shiptozip , h.shiptoaddr , h.shiptocity
                                        FROM pub.oeeh h
                                        WHERE h.cono = 10
                                        AND h.whse = '".$request->whse."'
                                        AND h.stagecd IN (1,3)
                                        AND h.openinit = '".$request->operator."'
                                        AND h.totinvamt - h.tendamt > 0
                                        WITH(nolock)");

        return response()->json(['status' => 'success', 'data' => $order[0]], 200);

    }
}
