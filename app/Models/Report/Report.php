<?php

namespace App\Models\Report;

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
        'group_by',
        'tally_column'
    ];
}
