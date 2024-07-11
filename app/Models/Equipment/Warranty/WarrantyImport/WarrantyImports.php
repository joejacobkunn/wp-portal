<?php

namespace App\Models\Equipment\Warranty\WarrantyImport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyImports extends Model
{
    use HasFactory;
    protected $fillable = ['file_path','uploaded_by','failed_records','valid_records','processed_count','status'];
}
