<?php

namespace App\Models\SX;

use App\Models\Scopes\WithnolockScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $table = 'icsd';

    protected $connection = 'sx';

    protected static function booted(): void
    {
        parent::boot();
        static::addGlobalScope(new WithnolockScope);
    }

}
