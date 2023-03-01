<?php

namespace App\Models\Core;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    private $activeRole;

    const ACTIVE = 1;

    const INACTIVE = 1;

    protected function password(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value,
            set: fn (string $value) => bcrypt($value),
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_inactive', 0);
    }

    public function scopeBasicSelect($query)
    {
        return $query->select('id',  'name', 'email');
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
        if (!$this->activeRole) {
            $this->activeRole = $this->roles()->first(); 
        }

        return $this->activeRole;
    }

    public function isMasterAdmin()
    {
        return $this->hasRole(Role::MASTER_ROLE);
    }

    public function setResetToken($reset = true)
    {
        $this->metadata()->updateOrCreate([], []);
        
        $this->metadata->user_token =  $reset ? bin2hex(random_bytes(25)) : null;
        $this->metadata->save();
    }
}
