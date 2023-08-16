<?php

namespace App\Models\SRO;

use App\Models\Core\Customer as CoreCustomer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $connection = 'sro';

    protected $table = 'customers';

    public function sxCustomer()
    {
        return $this->setConnection('mysql')->belongsTo(CoreCustomer::class, 'sx_customer_id', 'sx_customer_number');
    }
}
