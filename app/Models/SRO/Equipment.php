<?php

namespace App\Models\SRO;

use App\Models\Core\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $connection = 'sro';

    protected $table = 'equipment';

    protected $casts = ['purchase_date' => 'date'];

    public function sxCustomer()
    {
        return $this->setConnection('mysql')->belongsTo(Customer::class, 'sx_customer_id', 'sx_customer_number');
    }

}
