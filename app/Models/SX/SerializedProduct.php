<?php

namespace App\Models\SX;

use App\Models\Scopes\WithnolockScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SerializedProduct extends Model
{
    use HasFactory;

    public $connection = 'sx';

    protected $table = 'icses';

    protected $fillable = ['user9', 'user4'];


    protected static function booted(): void
    {
        parent::boot();
        static::addGlobalScope(new WithnolockScope);
    }

}
