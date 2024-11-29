<?php

namespace App\Models\SalesRepOverride;

use App\Models\Core\Comment;
use App\Models\Core\Customer;
use App\Models\Core\Operator;
use App\Models\Core\User;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesRepOverride extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = ['customer_number', 'ship_to', 'sales_rep', 'prod_line', 'last_updated_by'];
    const LOG_FIELD_MAPS = [

        'customer_number' => [
            'field_label' => 'Customer Number',
        ],
        'ship_to' => [
            'field_label' => 'Ship To',
        ],
        'sales_rep' => [
            'field_label' => 'Sales Rep',
        ],
        'prod_line' => [
            'field_label' => 'Prod Line',
        ]
    ];
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

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
