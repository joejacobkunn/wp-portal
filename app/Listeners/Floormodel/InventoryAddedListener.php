<?php

namespace App\Listeners\Floormodel;

use App\Events\Floormodel\InventoryAdded;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class InventoryAddedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(InventoryAdded $event): void
    {
        if(!config('sx.mock'))
        {
            DB::connection('sx')->insert("INSERT INTO pub.sastaz (cono, codeiden, primarykey,secondarykey,codeval,operinit,transproc,transdt,transtm) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                10,
                '*Floor Model Inventory',
                strtoupper($event->inventory->warehouse->short),
                strtoupper($event->inventory->product),
                $event->inventory->qty,
                strtolower($event->inventory->operator->operator),
                'WP Portal',
                Carbon::parse($event->inventory->created_at)->format('Y-m-d'),
                date('Hi', strtotime($event->inventory->created_at))
            ]);
    
        }
    }
}
