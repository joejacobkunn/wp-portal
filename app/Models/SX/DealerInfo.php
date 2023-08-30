<?php

namespace App\Models\SX;

use App\Models\Scopes\WithnolockScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerInfo extends Model
{
    use HasFactory;

    protected $connection = 'sx';

    protected $table = 'zzarscs';

    protected static function booted(): void
    {
        parent::boot();
        static::addGlobalScope(new WithnolockScope);
    }


    public function company()
    {
        return $this->belongsTo(Company::class, 'cono');
    }
}
