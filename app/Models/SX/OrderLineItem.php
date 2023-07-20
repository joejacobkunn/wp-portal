<?php

namespace App\Models\SX;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLineItem extends Model
{
    use HasFactory;

    protected $connection = 'sx';

    protected $table = 'oeel';

    private $types = [
        'l' => 'Lost',
        's' => 'Special',
        'n' => 'Non Stock',
    ];

    private $tied = [
        'p' => 'PO',
        't' => 'Warehouse Transfer',
    ];

    public function getSpecType()
    {
        if (empty($this->specnstype)) {
            return 'Stocked';
        }

        return $this->types[strtolower($this->specnstype)];
    }

    public function getTied()
    {
        if (empty($this->ordertype)) {
            return 'N/A';
        }

        return $this->tied[strtolower($this->ordertype)];
    }
}
