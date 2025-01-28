<?php

namespace App\Models\Core;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarHoliday extends Model
{
    use HasFactory;

    protected $table = 'calendar_holidays';

    protected $fillable = [
        'label',
        'date_value',
        'custom',
    ];

    public static function listAll($year = null, $month = null)
    {
        $year = $year ? $year : date('Y');

        $holidayDefs = self::get();
        $holidayList = [];

        foreach ($holidayDefs as $def) {
            $dateObj = Carbon::parse(str_replace('{YYYY}', $year, $def->date_value));
            if ((!$month || $month == $dateObj->format('m')) && (! $def->custom || $dateObj->format('Y') == $year)) {
                $holidayList[] = [
                    'date' => $dateObj->toDateString(),
                    'label' => $def->label,
                ];
            }
        }

        return $holidayList;
    }

    public static function listDates($year = null, $month = null)
    {
        $holidays = self::listAll($year, $month);

        return collect($holidays)->pluck('date')->toArray();
    }
}
