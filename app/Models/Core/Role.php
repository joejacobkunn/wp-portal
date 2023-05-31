<?php

namespace App\Models\Core;

use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    protected $fillable = [
        'name',
        'label',
        'reporting_role',
        'level',
        'guard_name',
        'is_preset',
        'description',
    ];

    const MASTER_ROLE = 'master-admin';

    const SUPER_ADMIN_ROLE = 'super-admin';

    const USER_ROLE = 'user';

    public function scopeBasicSelect($query)
    {
        return $query->select('id', 'name', 'label');
    }

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function hasModels()
    {
        return $this->hasMany(ModelHasRole::class);
    }
}
