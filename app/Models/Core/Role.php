<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'label',
        'guard_name',
        'is_preset',
        'description',
        'account_id',
        'created_by',
        'is_visible'
    ];

    const MASTER_ROLE = 'master-admin';

    const SUPER_ADMIN_ROLE = 'super-admin';

    const USER_ROLE = 'user';

    public function scopeBasicSelect($query)
    {
        return $query->select('id', 'name', 'label')->where('is_visible', true);
    }

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function hasModels()
    {
        return $this->hasMany(ModelHasRole::class);
    }

    public static function getMasterRole()
    {
        return self::where('name', 'master-admin')->firstOrFail();
    }

    public static function getDefaultRole()
    {
        return self::where('name', 'default')->firstOrFail();
    }
}
