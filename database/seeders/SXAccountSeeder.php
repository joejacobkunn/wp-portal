<?php

namespace Database\Seeders;

use App\Models\Core\SXAccount;
use App\Models\SX\Company;
use Illuminate\Database\Seeder;

class SXAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {

            SXAccount::updateOrCreate(['cono' => $company->cono], [
                'name' => $company->conm,
                'address' => $company->addr ?? '',
                'city' => $company->city,
                'state' => $company->state,
                'zip' => $company->zipcd,
                'phoneno' => $company->phoneno,
            ]);
        }
    }
}
