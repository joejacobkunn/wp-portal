<?php

namespace App\Listeners\Floormodel;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class InventoryUpdatedListener
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
    public function handle(object $event): void
    {
        if(!config('sx.mock'))
        {
            DB::connection('sx')->update('UPDATE pub.sastaz SET codeval = ? where cono = ? and primarykey = ? and secondarykey = ?', [
                $event->inventory->qty,
                10,
                strtoupper($event->inventory->warehouse->short),
                strtoupper($event->inventory->product)
            ]);
    
        }
    }
}
