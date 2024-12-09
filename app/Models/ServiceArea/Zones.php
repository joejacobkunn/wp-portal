<?php

namespace App\Models\ServiceArea;

use App\Models\Core\Comment;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zones extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'whse_id',
        'name',
        'description',
        'schedule_days',
    ];

    protected $casts = [
        'schedule_days' => 'array',
    ];

    const LOG_FIELD_MAPS = [

        'name' => [
            'field_label' => 'Zone Name',
        ],
        'description' => [
            'field_label' => 'Description',
        ],
        'schedule_days' => [
            'field_label' => 'Scheduled Days',
            'resolve' => 'resolveScheduleDay'

        ]
    ];
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }


    protected function resolveScheduleDay($value)
    {
        if (empty($value)) {
            return 'No schedule days set';
        }

        $changes = [];
        $days = [
            'monday', 'tuesday', 'wednesday', 'thursday',
            'friday', 'saturday', 'sunday'
        ];

        foreach ($days as $day) {
            if (isset($value[$day])) {
                $dayData = $value[$day];

                // Only include days that are enabled or have data
                if ($dayData['enabled'] ||
                    !empty($dayData['schedule']) ||
                    !empty($dayData['ahm_slot']) ||
                    !empty($dayData['pickup_delivery_slot'])) {

                    $dayChanges = [];

                    if ($dayData['enabled']) {
                        $dayChanges[] = 'Enabled';
                    }
                    if (!empty($dayData['schedule'])) {
                        $dayChanges[] = 'Schedule: ' . $dayData['schedule'];
                    }
                    if (!empty($dayData['ahm_slot'])) {
                        $dayChanges[] = 'AHM Slot: ' . $dayData['ahm_slot'];
                    }
                    if (!empty($dayData['pickup_delivery_slot'])) {
                        $dayChanges[] = 'Pickup/Delivery Slot: ' . $dayData['pickup_delivery_slot'];
                    }

                    if (!empty($dayChanges)) {
                        $changes[] = ucfirst($day) . ': ' . implode(', ', $dayChanges);
                    }
                }
            }
        }

        return empty($changes) ? 'No active schedule days' : implode(' | ', $changes);
    }


}
