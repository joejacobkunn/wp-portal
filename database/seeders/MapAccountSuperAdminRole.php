<?php

namespace Database\Seeders;

use App\Models\Core\Role;
use App\Models\Core\Account;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MapAccountSuperAdminRole extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = Account::select('id', 'name')->with('admin')->get();

        foreach ($accounts as $account) {
            $superAdminName = Role::SUPER_ADMIN_ROLE . '-account-' . $account->id;
            $role = Role::where('name', $superAdminName)->where('account_id', $account->id)->first();
            
            if (! $role) {
                //create superadmin role
                $role = Role::create([
                    'account_id' => $account->id,
                    'name' => $superAdminName,
                    'label' => 'Super Admin',
                    'is_preset' => 1,
                ]);
    
                $superAdminRole = Role::where('name', Role::SUPER_ADMIN_ROLE)->first();
                $role->syncPermissions($superAdminRole->getPermissionNames());

                if ($account->admin) {
                    $account->admin->roles()->detach();
                    $account->admin->assignRole($superAdminName);
                }
    
                //create user role
                Role::create([
                    'account_id' => $account->id,
                    'name' => Role::USER_ROLE . '-account-' . $account->id,
                    'label' => 'User',
                    'is_preset' => 1,
                ]);
            }
        }
    }
}
