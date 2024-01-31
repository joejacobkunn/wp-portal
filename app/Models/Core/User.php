<?php

namespace App\Models\Core;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Support\Str;
use App\Traits\LogsActivity;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\User\UserStatusEnum;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;

class User extends Authenticatable
{
    use HasApiTokens,
        HasFactory,
        Notifiable,
        HasRoles,
        SoftDeletes,
        AuthenticationLoggable,
        LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'abbreviation',
        'sx_operator_id',
        'title',
        'office_location',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => UserStatusEnum::class,
    ];

    private $activeRole;

    const ACTIVE = 1;

    const INACTIVE = 1;

    const LOG_FIELD_MAPS = [
        'name' => [
            'field_label' => 'Name',
        ],
        'email' => [
            'field_label' => 'Email',
        ],
        'abbreviation' => [
            'field_label' => 'Abbreviation',
        ],
        'title' => [
            'field_label' => 'Title',
        ],
        'office_location' => [
            'field_label' => 'Office Location',
        ],
        'is_active' => [
            'field_label' => 'Status',
            'resolve' => 'resolveStatus'
        ],
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeBasicSelect($query)
    {
        return $query->select('id', 'name', 'email');
    }

    public function metadata()
    {
        return $this->hasOne(UserMetadata::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function role()
    {
        return $this->hasOne(Role::class, 'model_has_roles', 'model_id', 'role_id');
    }

    public function getCurrentRoleAttribute()
    {
        if (! $this->activeRole) {
            $this->activeRole = $this->roles()->first();
        }

        return $this->activeRole;
    }

    public function location()
    {
        return Location::where('account_id', $this->account_id)->where('location', $this->office_location)->first();
    }

    public function isMasterAdmin()
    {
        return $this->hasRole(Role::MASTER_ROLE);
    }

    public function setResetToken($reset = true)
    {
        $this->metadata()->updateOrCreate([], []);

        $this->metadata->user_token = $reset ? bin2hex(random_bytes(25)) : null;
        $this->metadata->save();
    }

    public function getAbbreviation()
    {
        $nameString = $this->name && $this->name !="" ? $this->name : $this->email;

        return abbreviation($nameString);
    }

    public function resolveStatus($value, $cache = true)
    {
        return $value ? 'Active' : 'Inactive';
    }
}
