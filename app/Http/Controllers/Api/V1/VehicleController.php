<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Transformers\ApiPaginationResponse;
use App\Models\Vehicle\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = Vehicle::query()
            ->where('account_id', app('domain')->getClientId())
            ->when($request->name, function ($query) use ($request) {
                $query->where('name', 'like', $request->name.'%');
            })
            ->paginate($request->limit ?? 10, ['*'], 'page', $request->page ?? 1);

        return response(new ApiPaginationResponse($vehicles));
    }

    public function show($id)
    {
        $vehicle = Vehicle::where('account_id', app('domain')->getClientId())->findOrFail($id);

        return response($vehicle);
    }
}
