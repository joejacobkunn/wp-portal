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
        'master_type',
        'account_type'
    ];

    const MASTER_ROLE = 'master-admin';

    const SUPER_ADMIN_ROLE = 'super-admin';

    const USER_ROLE = 'user';

    const DEFAULT_USER_ROLE = 'default-user';

    public function scopeBasicSelect($query)
    {
        return $query->select('id', 'name', 'label');
    }

    public function scopeWithRoleType($query, $user)
    {
        return $query->when($user->isMasterAdmin(), function ($query) {
            $query->where('master_type', true);
        })->when(!$user->isMasterAdmin(), function ($query) {
            $query->where('account_type', true);
        });
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

    public static function getSuperAdminRole()
    {
        return self::where('name', 'super-admin')->firstOrFail();
    }

    public static function getDefaultRole()
    {
        return self::where('name', 'default-user')->firstOrFail();
    }

    public static function getRoleTypes()
    {
        return [
            ['name' => 'master_type', 'label' => 'Master Type'],
            ['name' => 'account_type', 'label' => 'Account Type'],
        ];
    }

    public function roleType()
    {
        return $this->master_type && $this->account_type ? 'Master and Account Type' :
            ($this->master_type ? 'Master Type'
                : ($this->account_type ? 'Account Type' : 'None')
            );
    }
}
