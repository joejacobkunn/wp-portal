<?php

namespace App\Console\Commands;

use App\Models\Core\PurchaseOrderReceipt;
use Illuminate\Console\Command;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Classes\SX;

class ProcessPurchaseOrderReceipts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    private $cono = 80;

    protected $signature = 'app:process-purchase-order-receipts {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to process PeopleVox purchase order receipts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $receipt_date = $this->argument('date') ?? now()->format('Y-m-d');

        $purchase_orders_numbers = PurchaseOrderReceipt::where('is_processed',0)->where('po_number', '>', 143113)->pluck('po_number')->unique();

        foreach($purchase_orders_numbers as $purchase_order_number)
        {
            $purchase_orders = PurchaseOrderReceipt::where('is_processed',0)->where('po_number', $purchase_order_number)->get();

            $sx_po_line_data = $this->getSxPoData($purchase_order_number);

            if(!empty($sx_po_line_data))
            {
                $line_items = [];
                $products = [];
                $purchase_order_ids = [];
                $ineligible_products = [];
    
                foreach($purchase_orders as $purchase_order)
                {
                    foreach($purchase_order->line_items['products'] as $product)
                    {
                        $products[] = $product;
                    }

                    $ineligible_products = $this->checkForIneligibleProducts($products,$sx_po_line_data);

                    if(!empty($ineligible_products))
                    {
                        Mail::send([], [],function (Message $message) use($ineligible_products,$purchase_order) {
                            $message->to(['miranda.beaudett@bwretail.com', 'jay.leblanc@bwretail.com', 'karl.hessell@bwretail.com'])->cc(['jkunnummyalil@wandpmanagement.com', 'jkrefman@wandpmanagement.com'])->subject('Ineligible products for PO# '.$purchase_order->po_number);
                            $message->html('<span><strong>Ineligible products found for PO#'.$purchase_order->po_number.'  :</strong> <br><br>'.implode(" ,",$ineligible_products));
                        });
        
                    }
    
                    foreach($purchase_order->line_items['line_items'] as $line_item)
                    {
                        if($this->lineEligibleForReceipt($line_item['line_no'], $products, $sx_po_line_data))
                        {
                            $line_items[$line_item['line_no']] = $line_item;
                        }
                    }
    
                    $purchase_order_ids[] = $purchase_order->id;
                }
    
                $po_suffix = $sx_po_line_data[0]->posuf;
    
                $stage_code = $sx_po_line_data[0]->stagecd;
    
                if(in_array($stage_code,[0,1,2,3,4]))
                {
                    $sx_payload = $this->createSXApiPayload($purchase_order_number,$po_suffix,$line_items);
    
                    $sx_client = new SX();
        
                    $sx_client->receive_po($sx_payload);
                }
    
                PurchaseOrderReceipt::whereIn('id', $purchase_order_ids)->update(['is_processed' => 1]);
    
                Mail::send([], [],function (Message $message) use($sx_payload,$purchase_order_number) {
                    $message->to('jkrefman@wandpmanagement.com')->cc(['jkunnummyalil@wandpmanagement.com'])->subject('SX Payload for PO# '.$purchase_order_number.' on '.date('Y-m-d'));
                    $message->html('<span><strong>Aggregated daily SX payload  :</strong> <br><br>'.json_encode($sx_payload));
                });
    
            }
            else{
                PurchaseOrderReceipt::where('po_number', $purchase_order_number)->update(['is_processed', 1]);
            }
        }
    }

    private function getSxPoData($po_number)
    {
        return DB::connection('sx')->select("SELECT TOP 1 poeh.posuf, poel.lineno, poel.shipprod, poel.qtyord, poeh.stagecd
                                                FROM pub.poeh
                                                LEFT JOIN pub.poel ON poel.cono = poeh.cono AND poel.pono = poeh.pono AND poel.posuf = poeh.posuf
                                                WHERE poeh.cono = ?
                                                AND poeh.pono = ?
                                                ORDER BY poeh.posuf DESC
                                                WITH(NOLOCK)", [$this->cono,$po_number]);

    }

    private function lineEligibleForReceipt($line_no,$receipted_products,$sx_po_line_data)
    {
        foreach($sx_po_line_data as $sx_po_line)
        {
            if(in_array($sx_po_line->shipprod,$receipted_products))
            {
                if($sx_po_line->lineno == $line_no)
                {
                    return true;
                }
            }
        }

        return false;
    }

    private function checkForIneligibleProducts($products,$sx_po_line_data)
    {
        $sx_products = [];
        $in_eligible_products = [];

        foreach($sx_po_line_data as $sx_po_line)
        {
            $sx_products[] = $sx_po_line->shipprod;
        }

        foreach($products as $product)
        {
            if(!in_array($product,$sx_products))
            {
                $in_eligible_products[] = $product;
            }
        }

        return $in_eligible_products;
    }

    private function createSXApiPayload($po_number,$po_suffix,$parsed_line_items)
    {

        $parsed_line_items = collect($parsed_line_items)->sortBy('line_no')->toArray();

        $line_items = [];

        foreach($parsed_line_items as $line_item)
        {
            $line_items[] = [
                'lineno' => $line_item['line_no'],
                'qtyrcv' => $line_item['quantity'],
                'price' => $line_item['amount'],
                'cancelfl' => false,
                'unavail' => false,
                'reasunavty' => '',
                'user1' => '',
                'user2' => '',
                'user3' => '',
                'user4' => '',
                'user5' => '',
                'user6' => 0,
                'user7' => 0,
                'user8' => '',
                'user9' => ''
            ];
        }

        return [
            'request' => [
                'companyNumber' => $this->cono,
                'operatorInit' => "wpa",
                'operatorPassword' => "",
                "purchaseOrderNumber" => $po_number,
                'purchaseOrderSuffix' => $po_suffix,
                'reference' => "",
                'ttRcvline' => [
                    'tt-rcvline' => $line_items
                ],
                'tInfieldvalue' => [
                    't-infieldvalue' => [

                    ]
                ]

            ]
        ];
    }


}