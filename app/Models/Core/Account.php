<?php

namespace App\Models\Core;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'name',
        'subdomain',
        'address', 
        'is_active',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user');
    }

    public function metadata()
    {
        return $this->hasOne(AccountMetadata::class);
    }

    public function scopeBasicSelect($query)
    {
        return $query->select('id', 'name', 'label');
    }

    public function apiKeys()
    {
        return $this->hasMany(AccountApiKey::class);
    }

    public function createKey($label)
    {
        $secretKey = Str::random(40);

        $key = $this->apiKeys()->create([
            'label' => $label,
            'client_key' => bin2hex(random_bytes(15)),
            'client_secret' => $secretKey,
            'client_secret_last4' => substr($secretKey, -4)
        ]);

        return [
            'client_key' => $key->client_key,
            'client_secret' => $secretKey,
        ];
    }

    public function revokeKey($key)
    {
        $key = $this->apiKeys()->where('client_key', $key)->firstOrFail();
        $key->is_revoked =  1;
        $key->save();
    }
}
