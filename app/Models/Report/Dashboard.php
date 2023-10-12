<?php

namespace App\Models\Report;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dashboard extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'reporting_dashboards';

    protected $fillable = ['name', 'reports', 'is_active'];

    protected $casts = ['reports' => 'array'];
}
