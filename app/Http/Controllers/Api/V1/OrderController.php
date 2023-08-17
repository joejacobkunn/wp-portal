<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\SX;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\Transformers\OrderSXTransfomer;

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

    public function show($id)
    {
    }
}
