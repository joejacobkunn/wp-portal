<?php

namespace App\Models\Equipment\Warranty;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Report extends Model
{
    use \Sushi\Sushi;

    protected $schema = [
        'serial' => 'string',
    ];

    protected $fillable = ['registration_date', 'registered_by'];


    public function getRows()
    {

        return Storage::json('reports/warranty-report.json');
    }

    protected function sushiShouldCache()
    {
        return true;
    }

    protected function sushiCacheReferencePath()
    {
        return storage_path('app/public/reports/warranty-report.json');
    }
}
