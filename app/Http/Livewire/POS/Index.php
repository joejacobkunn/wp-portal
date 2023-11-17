<?php

namespace App\Http\Livewire\POS;

use App\Classes\SX;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $pricing_request = [
            'request' => [
                "companyNumber" => 10,
                "operatorInit" => "wpa",
                "operatorPassword" =>  "",
                "productCode" => "STBGA57",
                "useCrossReferenceFlag" => false,
                "customerNumber" => 1,
                "shipTo" => "",
                "unitOfMeasure" => "EA",
                "warehouse" => "utic",
                "extraData" => "",
                "offerID" => "",
                "quantity" => 0
            ]
        ];

        $info_request = [
            'request' => [
                "companyNumber" => 10,
                "operatorInit" => "wpa",
                "operatorPassword" =>  "",
                "productCode" => "STBGA57",
                "useCrossReferenceFlag" => false,
                "customerNumber" =>  1,
                "shipTo" => "",
                "unitOfMeasure" => "EA",
            ]
        ];


        $sx = new SX();
        dd($sx->get_product_pricing_and_availability($pricing_request));
        $sx->get_product_info($info_request);
        return view('livewire.pos.index');
    }
}
