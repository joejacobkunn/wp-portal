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
            'name' => 'AHM Technician 1HR Away',
            'slug' => 'ahm-1hr',
            'description' => 'Email/SMS customer when technician is one hour away'
        ],
        4 => [
            'name' => 'AHM Technician Arrived',
            'slug' => 'ahm-arrived',
            'description' => 'Email/SMS customer when technician arrived at destination'
        ],
        5 => [
            'name' => 'AHM Complete',
            'slug' => 'ahm-complete',
            'description' => 'Email/SMS customer when technician has completed maintenance'
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
