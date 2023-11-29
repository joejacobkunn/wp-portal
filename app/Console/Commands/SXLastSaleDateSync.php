<?php

namespace App\Console\Commands;

use App\Models\Core\Account;
use App\Models\Core\Customer;
use App\Models\SX\Customer as SXCustomer;
use App\Models\SX\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SXLastSaleDateSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sx:last-sale-date-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to sync mysql last sale date with sx database. Runs nightly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cust_numbers = [];

        $invoiced_orders = SXCustomer::select('custno', 'lastsaledt')
            ->where('cono', 10)
            ->whereIn('lastsaledt', [now()->format('Y-m-d'),now()->subDays(1)->format('Y-m-d')])
            ->get();

        $account = Account::where('subdomain', 'weingartz')->first();

        foreach ($invoiced_orders as $invoiced_order) {
            $cust_numbers[] = $invoiced_order->custno;
            $customer = Customer::where('sx_customer_number', $invoiced_order->custno)->where('account_id', $account->id)->first();
            $customer->update(['last_sale_date' => Carbon::parse($invoiced_order->lastsaledt)->format('Y-m-d')]);
        }

        echo 'Synced customers are SX# '.implode(', ', $cust_numbers);
    }
}
