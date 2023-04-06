<?php

namespace App\Models\SX;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerInfo extends Model
{
    use HasFactory;

    protected $connection = 'sx';

    protected $table = 'zzarscs';

    public function company()
    {
        return $this->belongsTo(Company::class, 'cono');
    }

}
