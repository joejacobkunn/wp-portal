<?php

namespace App\Models\Report;

use App\Models\Core\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dashboard extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'reporting_dashboards';

    protected $fillable = ['name', 'reports', 'is_active', 'account_id'];

    protected $casts = ['reports' => 'array'];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
