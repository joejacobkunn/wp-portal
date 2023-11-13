<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'locations';

    protected $fillable = [
        'name',
        'account_id',
        'phone',
        'address',
        'is_active',
        'fortis_location_id',
        'location'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
