<?php

namespace App\Models\Equipment\Warranty\WarrantyImport;

use App\Models\Core\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyImports extends Model
{
    use HasFactory;
    protected $fillable = ['name','file_path','uploaded_by','failed_records','valid_records','processed_count','status','total_records'];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

}
