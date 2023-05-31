<?php

namespace App\Imports;

use App\Models\Core\Account;
use App\Models\Core\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class CustomersImport implements ToModel, WithChunkReading
{
    private Account $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Customer([
            'account_id' => $this->account->id,
            'sx_customer_number' => $row[3],
            'first_name' => $row[4],
            'last_name' => $row[5],
            'customer_type' => $row[6],
            'phone' => $row[7],
            'email' => $row[8],
            'address' => $row[9],
            'address2' => $row[10],
            'city' => $row[11],
            'state' => $row[12],
            'zip' => $row[13],
            'customer_since' => date('Y-m-d', strtotime($row[14])),
        ]);
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
