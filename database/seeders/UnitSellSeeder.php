<?php

namespace Database\Seeders;

use App\Models\Product\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSellSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //only update custom units
        $units = DB::connection('sx')->select("select prod,units 
                    FROM pub.icseu u
                    where u.cono = 10
                    with(nolock)");

        foreach($units as $unit)
        {
            $product = Product::where('prod', $unit->prod)->first();
            
            if(!is_null($product))
            {
                $existing_units = $product->unit_sell;
                array_push($existing_units, $unit->units);
                $product->update(['unit_sell' => array_unique($existing_units)]);
            }
        }
    }
}
