<?php

namespace App\Models\Equipment;

use App\Models\Core\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnavailableReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'unavailable_reports';

    protected $fillable = ['cono', 'user_id', 'report_date', 'data', 'submitted_at', 'status', 'note'];

    protected $casts = ['report_date' => 'date', 'submitted_at' => 'date'];

    public function assignee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
