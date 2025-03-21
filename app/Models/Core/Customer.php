<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'account_id',
        'sx_customer_number',
        'name',
        'customer_type',
        'phone',
        'email',
        'address',
        'address2',
        'city',
        'state',
        'zip',
        'customer_since',
        'look_up_name',
        'sales_territory',
        'last_sale_date',
        'sales_rep_in',
        'sales_rep_out',
        'is_active',
        'open_order_count',
        'preferred_contact_data',
    ];

    protected $casts = [
        'customer_since' => 'date',
        'last_sale_date' => 'date',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function takenBys()
    {
        return $this->belongsToMany(Operator::class, 'customer-taken_by', 'customer_id', 'sx_operator_id');
    }

    public function getFullAddress()
    {
        $address = ucwords(strtolower($this->address));

        if ($this->address2) {
            $address .= ', '.ucwords(strtolower($this->address2));
        }

        $address .= ', '.ucwords(strtolower($this->city));

        $address .= ', '.$this->state;

        $address .= ', '.$this->zip;

        return $address;

    }
}
