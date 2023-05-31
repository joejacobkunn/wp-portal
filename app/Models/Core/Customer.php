<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

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
        'has_open_order',
    ];

    protected $casts = [
        'customer_since' => 'date',
        'last_sale_date' => 'date',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function getFullAddress()
    {
        $address = $this->address;

        if ($this->address2) {
        $address .= ', '.$this->address2;
        }

        $address .= ', '.$this->city;

        $address .= ', '.$this->state;

        $address .= ', '.$this->zip;

        return $address;

    }
}
