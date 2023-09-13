<?php

namespace App\Models\SX;

use App\Models\Scopes\WithnolockScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $connection = 'sx';

    protected $table = 'arsc';

    protected static function booted(): void
    {
        parent::boot();
        static::addGlobalScope(new WithnolockScope);
    }

}
