<?php

namespace App\Models\Marketing;

use App\Models\Core\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SMSMarketing extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'smsmarketing';

    protected $fillable = ['name','file', 'failed_file', 'processed', 'processed_count', 'total_count', 'created_by', 'status','created_at'];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
