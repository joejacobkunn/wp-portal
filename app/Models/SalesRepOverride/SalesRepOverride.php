<?php

namespace App\Models\SalesRepOverride;

use App\Models\Core\Customer;
use App\Models\Core\Operator;
use App\Models\Core\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesRepOverride extends Model
{
    use HasFactory;
    protected $fillable = ['customer_number', 'ship_to', 'sales_rep', 'prod_line', 'last_updated_by'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_number', 'sx_customer_number');
    }

    public function salesRep()
    {
        return $this->belongsTo(Operator::class, 'sales_rep', 'operator');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'last_updated_by', 'id');
    }
}
