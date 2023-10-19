<?php

namespace App\Console\Commands;

use App\Models\Core\Account;
use App\Models\Core\Customer;
use App\Models\SX\Customer as SXCustomer;
use App\Models\SX\Order;
use App\Models\SX\Warehouse;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ImportSX extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:sx {name} {subdomain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to import excel data';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $name = $this->argument('name');

        $subdomain = $this->argument('subdomain');

        $account = Account::where('subdomain', $subdomain)->first();

        if ($name == 'customers') {

            //fetch from sx customer
            $required_fields = ['custno', 'name', 'addr', 'city', 'state', 'zipcd', 'phoneno', 'email', 'custtype', 'enterdt', 'lookupnm', 'salesterr', 'lastsaledt', 'slsrepin', 'slsrepout', 'statustype'];

            SXCustomer::select($required_fields)
                ->where('cono', $account->sx_company_number)
                ->orderBy('custno', 'asc')
                ->chunk(1000, function (Collection $sx_customers) use ($account) {
                    foreach ($sx_customers as $sx_customer) {

                        $address = $this->split_address($sx_customer->addr);

                        $customer = Customer::updateOrCreate(
                            [
                                'account_id' => $account->id,
                                'sx_customer_number' => $sx_customer->custno,
                
                            ],
                            [
                                'sx_customer_number' => $sx_customer->custno,
                                'name' => $sx_customer->name,
                                'customer_type' => strtoupper($sx_customer->custtype),
                                'phone' => $sx_customer->phoneno,
                                'email' => $sx_customer->email,
                                'address' => $address[0],
                                'address2' => $address[1] ?? '',
                                'city' => $sx_customer->city,
                                'state' => $sx_customer->state,
                                'zip' => $sx_customer->zipcd,
                                'customer_since' => date('Y-m-d', strtotime($sx_customer->enterdt)),
                                'look_up_name' => $sx_customer->lookupnm,
                                'sales_territory' => $sx_customer->salesterr,
                                'last_sale_date' => $sx_customer->lastsaledt,
                                'sales_rep_in' => $sx_customer->slsrepin,
                                'sales_rep_out' => $sx_customer->slsrepout,
                                'is_active' => $sx_customer->statustype ?? 1,
                            ]
                        );
                    }
                });
        }

        if ($name == 'customer-order-status-sync') {

            $start_time = microtime(true);
            
            $warehouses = ['ann','ceda','farm','livo','utic','wate'];
            $open_order_customers = [];

            //step 1 - set all customer flag to false
            Customer::where('account_id', $account->id)
                ->whereNot('open_order_count',0)
                ->chunkById(1000, function($customers) {
                    foreach($customers as $customer) {
                        $customer->open_order_count = 0;
                        $customer->save();
                    }
            });

            //step 2 - update open order flags
            Order::without('customer')
                ->select('custno',DB::raw('count(*) as open_order_count'))
                ->where('cono', $account->sx_company_number)
                ->where('custno', '!=', 1)
                ->whereIn('whse', $warehouses)
                ->openOrders()
                ->groupBy('custno')
                ->orderBy('custno', 'asc')
                ->chunk(1000, function (Collection $open_orders) use($open_order_customers) {
                    foreach ($open_orders as $open_order) {
                        Customer::where('sx_customer_number', $open_order->custno)->update(['open_order_count' => $open_order->OPEN_ORDER_COUNT]);
                        $open_order_customers[] = $open_order->custno;
                    }
                });

            //step 3 - delete any duplicate customers
            $deleted = DB::delete('DELETE t1 FROM customers t1 INNER JOIN customers t2  WHERE t1.id < t2.id AND t1.sx_customer_number = t2.sx_customer_number');

            $end_time = microtime(true);

            $execution_time = $end_time - $start_time;

            echo " Execution time of script = " . $execution_time . " sec to update ".count($open_order_customers);

        }
    }

    private function split_address($address)
    {
        return explode(';', $address);
    }
}
