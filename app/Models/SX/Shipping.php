<?php

namespace App\Models\SX;

use App\Models\Scopes\WithnolockScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;

    public $connection = 'sx';

    protected $table = 'oeehp';

    protected static function booted(): void
    {
        parent::boot();
        static::addGlobalScope(new WithnolockScope);
    }


    private $carriers = [
        'upsg' => 'UPS-Ground',
        'upsa' => 'UPS-AirSaver',
        'ups3' => 'UPS-3rd Day',
        'u21' => 'UPS 3rd Day',
        'u11' => 'UPS Ground',
        'ups' => 'UPS Ground',
        'upsn' => 'UPS Next Day',
        'upss' => 'UPS Saturday',
        'wein' => 'Weingartz Truck',
        'fedx' => 'Fedex Ground', 
        'frt' => 'Freight Truck',
        'ped' => 'PED Truck'
    ];

    public function getCarrier()
    {
        if(array_key_exists(strtolower($this->shipviaty),$this->carriers))
        {
            return $this->carriers[strtolower($this->shipviaty)];
        }

        else return strtoupper($this->shipviaty);
    }


}
