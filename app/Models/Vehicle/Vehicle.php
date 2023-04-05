<?php

namespace App\Models\Vehicle;

use App\Models\Core\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'vehicles';

    protected $fillable = [
        'account_id',
        'name',
        'vin',
        'license_plate_number',
        'make',
        'model',
        'year',
        'type',
        'retired_at'
    ];

    protected $casts = [
        'retired_at' => 'datetime'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

}
