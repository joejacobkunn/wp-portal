<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class AccountAccessToken extends Model
{
    protected $fillable = [
        'account_id',
        'access_token',
        'expires_at',
        'is_revoked',
    ];

    protected function clientSecret(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value,
            set: fn (string $value) => bcrypt($value),
        );
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_revoked', 0)->where('expires_at', '>=', (string) now());
    }
}
