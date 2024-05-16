<?php

namespace App\Console\Commands;

use App\Models\Core\User;
use App\Models\Equipment\UnavailableReport;
use Illuminate\Console\Command;

class CreateUnavailableEquipmentReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-unavailable-equipment-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to generate unavailable equipment report for TMs bi-monthly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //fetch users that have unavailable ids in users table
        $users = User::where('unavailable_equipments_id', '<>', null)->orWhere('unavailable_equipments_id', '<>', '')->get();

        foreach($users as $user)
        {
            UnavailableReport::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'report_date' => now()->format('Y-m-d')
                ],
                [
                    'cono' => $user->account->sx_company_number,
                    'status' => 'Pending Review'
                ]
            );
        }


    }
}
