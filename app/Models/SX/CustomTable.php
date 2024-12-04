<?php

namespace App\Models\SX;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\WithnolockScope;

class CustomTable extends Model
{
    use HasFactory;

    protected $connection = 'sx';

    protected $table = 'sastaz';

    protected $fillable = ['cono', 'codeiden', 'primarykey', 'secondarykey', 'codeval', 'operinit'];

    protected static function booted(): void
    {
        parent::boot();
        static::addGlobalScope(new WithnolockScope);
    }
}
