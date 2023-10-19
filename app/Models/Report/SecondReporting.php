<?php

namespace App\Models\Report;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SecondReporting extends Model
{
    use \Sushi\Sushi;

    protected static $query;


    public function getRows()
    {
        $rows = [];
        $data = DB::connection('sx')->select(self::$query);

        foreach($data as $row){
            $rows[] = (array)$row;
        }

        return $rows;
    }

    public static function setQuery($query)
    {
        self::$query = $query;
		return self::query();
    }

    protected function sushiShouldCache()
    {
        return false;
    }
}
