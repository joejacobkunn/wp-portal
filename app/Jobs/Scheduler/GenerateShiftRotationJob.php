<?php

namespace App\Jobs\Scheduler;

use App\Models\Core\CalendarHoliday;
use App\Models\Scheduler\ShiftRotation;
use Illuminate\Bus\Queueable;
use App\Models\Scheduler\Truck;
use App\Models\Scheduler\Shifts;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateShiftRotationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $startDate;

    public $endDate;

    public $trucks;

    public $ahmShifts = [];

    public $deliveryShifts = [];

    public $holidays = [];

    /**
     * Create a new job instance.
     */
    public function __construct($startDate, $endDate, $truckIds = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->setHolidays();

        if ($truckIds) {
            $truckIds = is_array($truckIds) ? $truckIds : [$truckIds];
        }
        
        $this->trucks = Truck::with(['rotations' => function ($query) {
                    return $query->orderBy('sort_order', 'asc');
                }])
                ->when(!empty($truckIds), function ($subQuery) use ($truckIds) {
                    $subQuery->whereIn('id', $truckIds);
                })
                ->get();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->ahmShifts = Shifts::where('type', 'ahm')->pluck('shift','whse');
        $this->deliveryShifts = Shifts::where('type', 'delivery_pickup')->pluck('shift', 'whse');

        foreach ($this->trucks as $truck) {
            $this->generateShiftRotations($truck);
        }
    }

    public function setHolidays()
    {
        $holidays = [];
        $startDateObj = Carbon::parse($this->startDate);
        $endDateObj = Carbon::parse($this->endDate);
        $holidays = CalendarHoliday::listDates($startDateObj->format('Y'));
        if ($startDateObj->format('Y') != $endDateObj->format('Y')) {
            $holidays = array_merge($holidays, CalendarHoliday::listDates($endDateObj->format('Y')));
        }

        $this->holidays = $holidays;
    }

    public function generateShiftRotations($truck)
    {
        $dateObj = Carbon::parse($this->startDate);
        $shifts = $this->getShifts($truck);
        $zoneRotations = $truck->rotations->pluck('zone_id')->toArray();
        $rotationIndex = 0;

        while($dateObj->toDateString() <= $this->endDate) {
            $selectedDate = $dateObj->toDateString();

            //check if holiday
            if (! in_array($selectedDate, $this->holidays)) {
                $dayShifts = $shifts[strtolower($dateObj->format('F'))][strtolower($dateObj->format('l'))] ?? [];

                if (!empty($dayShifts)) {
                    foreach ($dayShifts as $dayShift) {
                        ShiftRotation::create([
                            'truck_id' => $truck->id,
                            'zone_id' => $zoneRotations[$rotationIndex],
                            'shift_id' => 0,
                            'scheduled_date' => $dateObj->toDateString(),
                        ]);

                        $rotationIndex++;

                        //reset rotation index to start if end of rotations
                        if (!isset($zoneRotations[$rotationIndex])) {
                            $rotationIndex = 0;
                        }
                    }
                }
            }

            $dateObj->addDay();
        }
    }

    public function getShifts($truck)
    {
        if ($truck->service_type == 'ahm') {
            return $this->ahmShifts[$truck->whse] ?? [];
        } else {
            return $this->deliveryShifts[$truck->whse] ?? [];
        }
    }
}
