<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\Fortis;
use App\Classes\SX;
use App\Http\Controllers\Controller;
use App\Models\Core\Location;
use App\Models\Core\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FortisController extends Controller
{

    public function listTerminals(Request $request)
    {
        $request->validate([
            'location' => ['required', Rule::in(Warehouse::where('cono',10)->pluck('title')->toArray())]
        ]);

        $location = Location::where('location', $request->location)->first();

        if(!empty($location->fortis_location_id)){
            
            $fortis = new Fortis();

            return response()->json(['status' => 'success', 'data' => $fortis->fetchTerminals($request->fortis_location_id)], 200);
        }

        return response()->json(['status' => 'success', 'data' => ''], 200);

    }

    public function getUser(Request $request)
    {
        $request->validate([
            'fortis_user_id' => 'required'
        ]);

        $fortis = new Fortis();

        return response()->json(['status' => 'success', 'data' => $fortis->getUser($request->fortis_user_id)], 200);
    }

    public function createContact(Request $request)
    {
        $fortis = new Fortis();

        return response()->json(['status' => 'success', 'data' => $fortis->createContact($request)], 200);

    }

    public function mock($function, $request)
    {
        
    }
}
