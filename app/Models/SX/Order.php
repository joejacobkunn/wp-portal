<?php

namespace App\Models\SX;

use App\Models\Scopes\WithnolockScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    public $connection = 'sx';

    protected $table = 'oeeh';

    protected $primaryKey = 'orderno';

    protected static function booted(): void
    {
        parent::boot();
        static::addGlobalScope(new WithnolockScope);
    }


    private $stage_codes = [
        0 => 'Quoted',
        1 => 'Ordered',
        2 => 'Picked',
        3 => 'Shipped',
        4 => 'Invoiced',
        5 => 'Paid',
        9 => 'Cancelled',
    ];

    private $shipping_stages = [
        0 => 'Quoted',
        1 => 'Reserved',
        2 => 'Committed',
        3 => 'Shipped',
    ];

    protected $casts = [
        'enterdt' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function getLineItems()
    {
        $required_line_item_columns = [
            'oeel.orderno',
            'oeel.ordersuf',
            'oeel.shipto',
            'oeel.lineno',
            'oeel.qtyord',
            'oeel.proddesc',
            'oeel.price',
            'oeel.shipprod',
            'oeel.statustype',
            'oeel.prodcat',
            'oeel.prodline',
            'oeel.specnstype',
            'oeel.qtyship',
            'oeel.ordertype',
            'oeel.netamt',
            'oeel.orderaltno',
            'oeel.user8',
            'oeel.vendno',
            'oeel.whse',
            'oeel.stkqtyord',
            'oeel.stkqtyship',
            'oeel.returnfl',
            'icsp.descrip',
            'icsl.user3',
            'icsl.whse',
            'icsl.prodline',
            'oeel.cono',
        ];
    
        return OrderLineItem::select($required_line_item_columns)
        ->leftJoin('icsp', function (JoinClause $join) {
            $join->on('oeel.cono','=','icsp.cono')
            ->on('oeel.shipprod', '=', 'icsp.prod');
                //->where('icsp.cono', $this->customer->account->sx_company_number);
        })
        ->leftJoin('icsl', function (JoinClause $join) {
            $join->on('oeel.cono','=','icsl.cono')
                ->on('oeel.whse', '=', 'icsl.whse')
                ->on('oeel.vendno', '=', 'icsl.vendno')
                ->on('oeel.prodline', '=', 'icsl.prodline');
        })
        ->where('oeel.orderno', $this->orderno)->where('oeel.ordersuf', $this->ordersuf)
        ->where('oeel.cono', $this->cono)
        ->orderBy('oeel.lineno', 'asc')
        ->get();
    }

    public function shipping()
    {

    }

    public function scopeOpenOrders(Builder $query)
    {
        $query->whereIn('stagecd', [1, 2]);
    }

    public function scopeNonOpenOrders(Builder $query)
    {
        $query->whereNotIn('stagecd', [1,2]);
    }


    public function getStageCode()
    {
        return $this->stage_codes[$this->stagecd];
    }

    public function getShippingStage($stage_code)
    {
        if ($stage_code > 3) {
            return 'Delivered';
        }

        return $this->shipping_stages[$stage_code];
    }

    public function isBackOrder()
    {
        return ($this->totqtyord > $this->totqtyshp) ? true : false;
    }

    public function hasGolfParts($line_items)
    {
        $golf_parts = [];

        foreach($line_items as $line_item)
        {
                $wprod = DB::connection('sx')->select("SELECT top 1 arpvendno FROM pub.icsw 
                                            WHERE cono = ? AND whse = ? AND prod = ? with(nolock) ", [$line_item->cono,$line_item->whse, $line_item->shipprod]);

                if(!empty($wprod))
                {
                    if($wprod[0]->arpvendno == '68878') $golf_parts[] = $line_item->shipprod;
                }
        }

        return (!empty($golf_parts)) ? $golf_parts : null;
    }

    public function hasNonStockItems($line_items)
    {
        $non_stock_items = [];

        foreach($line_items as $line_item)
        {
            if($line_item->specnstype == 'n') $non_stock_items[] = $line_item->shipprod;
        }

        return (!empty($non_stock_items)) ? $non_stock_items : null;
    }
}
