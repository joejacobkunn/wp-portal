<?php

namespace App\Listeners\Floormodel;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class InventoryDeletedListener
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
            DB::connection('sx')->delete('DELETE from pub.sastaz where cono = ? and primarykey = ? and secondarykey = ?', [
                10,
                strtoupper($event->inventory->warehouse->short),
                strtoupper($event->inventory->product)
            ]);
    
        }

    }
}
