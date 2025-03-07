<?php

namespace Database\Seeders;

use App\Models\Core\User;
use App\Models\Scheduler\NotificationTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class SchedulerEmailTemplateSeeder extends Seeder
{
    public $data = [
        0 => [
            'name' => 'AHM Scheduled',
            'slug' => 'ahm-scheduled',
            'description' => 'Email to customer when AHM is scheduled'
        ],
        1 => [
            'name' => 'AHM 48 Hours Reminder',
            'slug' => 'ahm-48-hours',
            'description' => 'Reminder email/sms for customer 48 hours before AHM'
        ],
        2 => [
            'name' => 'AHM Technician Dispatched',
            'slug' => 'ahm-dispatched',
            'description' => 'Email/SMS customer when technician is on its way'
        ],
        3 => [
            'name' => 'AHM Complete',
            'slug' => 'ahm-complete',
            'description' => 'Email/SMS customer when technician has completed maintenance'
        ],
        4 => [
            'name' => 'AHM Rescheduled',
            'slug' => 'ahm-rescheduled',
            'description' => 'Email/SMS customer when rescheduled'
        ],
        5 => [
            'name' => 'AHM Cancelled',
            'slug' => 'ahm-cancelled',
            'description' => 'Email/SMS customer when schedule cancelled'
        ],
        6 => [
            'name' => 'AHM 3 Week Reminder',
            'slug' => 'ahm-three-week-reminder',
            'description' => 'Email/SMS customer three weeks out for one year advance AHM schedules'
        ],

    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->data as $item) {
            NotificationTemplate::updateOrCreate([
                'slug' => $item['slug']
            ], [
                'name' => $item['name'],
                'description' => $item['description'],
            ]);
        }
    }
}
