<?php

namespace App\Models\Core;

use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    protected $fillable = [
        'name',
        'label',
        'guard_name',
        'group_name',
        'description',
        'master_type',
        'account_type',
    ];
}
