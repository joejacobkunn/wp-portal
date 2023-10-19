<?php

namespace App\Models\Report;

use App\Models\Core\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'reports';

    protected $fillable = [
        'name',
        'description',
        'query',
        'account_id',
        'group_by',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
