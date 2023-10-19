<?php

use App\Models\Core\Permission;
use App\Models\Core\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $superAdmin = Role::getSuperAdminRole();
        $permissionIds = Permission::where('group_name', 'Access Control')->get()?->pluck('id');
        if($superAdmin) {
            DB::table('role_has_permissions')
                ->where('role_id', $superAdmin->id)
                ->whereIn('permission_id', $permissionIds)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Do nothing not relevent.
    }
};
