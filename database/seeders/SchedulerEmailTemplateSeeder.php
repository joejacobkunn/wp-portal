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
            'name' => 'Scheduled',
            'type' => 'Shipment Follow Up',
        ],
        1 => [
            'name' => 'Delivered',
            'type' => 'Shipment Follow Up',
        ],
        2 => [
            'name' => 'Default 200',
            'type' => 'Shipment Follow Up',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::latest()->first();
        foreach ($this->data as $item) {
            NotificationTemplate::updateOrCreate([
                'name' => $item['name']
            ], [
                'name' => $item['name'],
                'account_id' => $user->account_id,
                'created_by' => $user->id,
                'type' => $item['type'],
            ]);
        }
    }
}
