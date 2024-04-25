<?php

namespace App\Models\SX;

use App\Models\Scopes\WithnolockScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderLineItem extends Model
{
    use HasFactory;

    protected $connection = 'sx';

    protected $table = 'oeel';

    protected $fillable = ['orderaltno', 'linealtno', 'ordertype'];

    protected static function booted(): void
    {
        parent::boot();
        static::addGlobalScope(new WithnolockScope);
    }


    private $types = [
        'l' => 'Lost',
        's' => 'Special',
        'n' => 'Non Stock',
    ];

    private $tied = [
        'p' => 'PO',
        't' => 'Warehouse Transfer',
    ];

    public function getSpecType()
    {
        if (empty($this->specnstype)) {
            return 'Stocked';
        }

        return $this->types[strtolower($this->specnstype)];
    }

    public function getTied()
    {
        if (empty($this->ordertype)) {
            return 'N/A';
        }

        return $this->tied[strtolower($this->ordertype)];
    }

    public function cleanDescription()
    {
        $description = str_replace(';', '', $this->descrip);
        $description = str_replace('>>NO LONGER AVAILABLE<<', '', $description);
        return trim($description);
    }

    public function isBackOrder()
    {
        return (intval($this->stkqtyord) - intval($this->stkqtyship)) > 0 ? 1 : 0;
    }

    public function checkInventoryLevelsInWarehouses($warehouses)
    {
        return DB::connection('sx')->select("SELECT prod,whse,qtyonhand, qtycommit, qtyreservd FROM pub.icsw 
                                            WHERE cono = ? AND whse IN ('".implode("','",$warehouses)."') AND prod = ? with(nolock) ", [$this->cono, $this->shipprod]);
    }
}
