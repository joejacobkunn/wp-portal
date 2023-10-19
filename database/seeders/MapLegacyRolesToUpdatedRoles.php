<?php

namespace Database\Seeders;

use App\Models\Core\Account;
use App\Models\Core\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MapLegacyRolesToUpdatedRoles extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('model_has_roles')->truncate();

        //map master admin
        $masterUser = User::first();
        $masterUser->assignRole('master-admin');

        //superadmins
        $superAdmins = Account::with('admin')->get()->pluck('admin');

        foreach ($superAdmins as $superAdmin) {
            if ($superAdmin) {
                $superAdmin->assignRole('super-admin');
            }
        }

        //map default users
        $adminUsers = array_filter($superAdmins->pluck('id')->toArray());
        $adminUsers[] = $masterUser->id;
        $defaultUsers = User::whereNotIn('id', $adminUsers)->get();
        
        foreach ($defaultUsers as $user) {
            $user->assignRole('default-user');
        }
    }
}
