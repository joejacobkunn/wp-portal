<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'name',
        'subdomain',
        'address', 
        'is_active',
    ];

    protected $attributes = [
        'is_active' => 1
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

}
