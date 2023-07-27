<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountApiKey extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'label',
        'account_id',
        'client_key',
        'client_secret',
        'client_secret_last4',
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

    public function scopeBasicSelect($query)
    {
        return $query->select('id', 'client_key', 'client_secret', 'client_secret_last4');
    }

    public function scopeIsActive($query)
    {
        return $query->where('is_revoked', 0);
    }
}
