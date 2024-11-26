<?php

namespace App\Models\SalesRepOverride;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesRepOverride extends Model
{
    use HasFactory;
    protected $fillable = ['customer_number', 'ship_to', 'sales_rep', 'prod_line'];
}
