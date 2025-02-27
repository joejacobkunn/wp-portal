<?php

namespace App\Console\Commands;

use App\Models\Core\PurchaseOrderReceipt;
use Illuminate\Console\Command;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Classes\SX;
use Illuminate\Support\Facades\Log;

class ProcessPurchaseOrderReceipts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    private $cono = 80;

    protected $signature = 'app:process-purchase-order-receipts {--mode=test} {--po=} {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to process PeopleVox purchase order receipts';

    public $line_item_slots = [];

    public $sx_po_line_data = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mode = $this->option('mode');

        $po_number = $this->option('po') ?? null;

        $date = $this->option('date') ?? null;

        $this->info('Starting process in mode : '.$mode.'<br>');

        if($po_number)
        {
            $this->newLine();
            $this->info(' for PO Number : '.$po_number.'<br>');
        }

        if($date)
        {
            $this->newLine();
            $this->info(' for Date : '.$date);
        }


        $query = PurchaseOrderReceipt::query();

        $query->when(!is_null($po_number), function ($q) use($po_number) {
            return $q->where('po_number', $po_number);
        });

        $query->when(!is_null($date), function ($q) use($date) {
            return $q->where('receipt_date', $date);
        });

        $query->when($mode == 'process', function ($q) use($date) {
            return $q->where('is_processed', 0);
        });

        $purchase_orders_numbers = $query->pluck('po_number')->unique();

        foreach($purchase_orders_numbers as $purchase_order_number)
        {
            $this->info('Processing PO Number '.$purchase_order_number.'<br>');

            if($mode == 'test')
            {
                $purchase_orders = PurchaseOrderReceipt::where('po_number', $purchase_order_number)->get();
            }else{
                $purchase_orders = PurchaseOrderReceipt::where('is_processed',0)->where('po_number', $purchase_order_number)->get();
            }


            $this->sx_po_line_data = $this->getSxPoData($purchase_order_number);

            if(!empty($this->sx_po_line_data))
            {
                $line_items = [];
                $this->line_item_slots = [];
                $this->line_item_totals = [];
                $products = [];
                $purchase_order_ids = [];
                $ineligible_products = [];

                foreach($purchase_orders as $purchase_order)
                {
                    if($purchase_order->is_processed) $this->info('Peoplevox Ref '.$purchase_order->people_vox_reference_no .'receipt has already been processed<br>');

                    foreach($purchase_order->line_items['line_items'] as $item => $item_count)
                    {
                        //$array_key = $this->checkForItem($item, $products);
                        $this->line_item_slots[strtoupper($item)][] = ['qty' => $item_count, 'po_number' => $purchase_order_number];
                        $products[] = ['prod' => $item, 'qty' => $item_count, 'po_number' => $purchase_order_number];                        
                    }

                    foreach($this->line_item_slots as $item => $slots)
                    {
                        $this->line_item_totals[strtoupper($item)] = 0;
                        foreach($slots as $slot){
                            $this->line_item_totals[strtoupper($item)] += $slot['qty'];
                        }
                    }
                }

                $ineligible_products = $this->checkForIneligibleProducts($products,$this->sx_po_line_data);

                if(!empty($ineligible_products))
                {
                    $to = ['jkrefman@wandpmanagement.com'];

                    if($mode == 'process') 
                    {
                        $to = ['miranda.beaudett@bwretail.com', 'jay.leblanc@bwretail.com', 'karl.hessell@bwretail.com', 'jkrefman@wandpmanagement.com'];
                        //PurchaseOrderReceipt::whereIn('id', $purchase_order_ids)->update(['is_processed' => 1]);
                    }

                    // Mail::send([], [],function (Message $message) use($ineligible_products,$purchase_order,$po_suffix,$to) {
                    //     $message->to($to)->cc(['jkunnummyalil@wandpmanagement.com', 'jkrefman@wandpmanagement.com'])->subject('Ineligible products for PO# '.$purchase_order->po_number.'-'.$po_suffix);
                    //     $message->html('The following items were received in Peoplevox today but could not be found on PO# '.$purchase_order->po_number.'-'.$po_suffix.', therefore they were not received in SX.<br><br>PeopleVox Ref Number : '.$purchase_order->people_vox_reference_no.'<br>Receipt Date : '.date('Y-m-d', strtotime($purchase_order->receipt_date)).'<br><br>Ineligible Items  : '.implode(" ,",$ineligible_products).'<br><br>Note: Reasons could include supersedes, substitutes, or overages (if the line item was already completed in SX)');
                    // });

                    $this->info('Ineligible items : '.implode(" ,",$ineligible_products).'<br>');
    
                }


                $this->info('PeopleVox Data => '.json_encode($products).'<br><br>');
                $this->info('PeopleVox Calculated Line Item Totals => '.json_encode($this->line_item_totals).'<br><br>');
    
                foreach($purchase_orders as $purchase_order)
                {

                    $po_suffix = $this->sx_po_line_data[0]->posuf;
    
                    $stage_code = $this->sx_po_line_data[0]->stagecd;


                    foreach($this->sx_po_line_data as $sx_po_line)
                    {
                        if($this->itemEligibleForReceipt($sx_po_line->shipprod,$products) && !in_array(strtoupper($sx_po_line->shipprod),$ineligible_products))
                        {
                            $po_meta_data = $this->getPOMetaDataForItem(strtoupper($sx_po_line->shipprod), $sx_po_line->lineno,$sx_po_line->qtyord,$purchase_order_number,$po_suffix);
                            
                            if(!empty($po_meta_data))
                            {
                                $line_items[$sx_po_line->lineno] = ['line_no' => $sx_po_line->lineno, 'quantity' => $po_meta_data['qty'], 'amount' => $sx_po_line->price];
                            }
                        }
                        
                    }    


                    $purchase_order_ids[] = $purchase_order->id;
                }    
    
                if(in_array($stage_code,[0,1,2,3,4]))
                {
                    $sx_payload = $this->createSXApiPayload($purchase_order_number,$po_suffix,$line_items);
    
                    $sx_client = new SX();

                    if($mode == 'process')
                    {
                        $sx_client->receive_po($sx_payload);
                        $this->flag_po_in_sx($purchase_order_number,$po_suffix,$purchase_order->people_vox_reference_no);
                    }
        
                    Mail::send([], [],function (Message $message) use($sx_payload,$purchase_order_number,$mode) {
                        $message->to('jkrefman@wandpmanagement.com')->cc(['jkunnummyalil@wandpmanagement.com'])->subject('SX Payload for PO# '.$purchase_order_number.' on '.date('Y-m-d'));
                        $message->html('<strong>Mode : </strong>'.$mode);
                        $message->html('<span><strong>Aggregated daily SX payload  :</strong> <br><br>'.json_encode($sx_payload));
                    });
        
    
                }
    
                if($mode == 'process') PurchaseOrderReceipt::whereIn('id', $purchase_order_ids)->update(['is_processed' => 1]);
    
            }
            else{
                $this->info('No line items found for this PO');
                if($mode == 'process') PurchaseOrderReceipt::where('po_number', $purchase_order_number)->update(['is_processed', 1]);
            }
        }

        $this->info('<br>The job is complete');
    }

    private function getSxPoData($po_number)
    {
        $last_suffix = DB::connection('sx')->select("SELECT top 1 poeh.posuf, poel.lineno, poel.shipprod, poel.qtyord, poeh.stagecd
                                                        FROM pub.poeh
                                                        LEFT JOIN pub.poel ON poel.cono = poeh.cono AND poel.pono = poeh.pono AND poel.posuf = poeh.posuf
                                                        WHERE poeh.cono = ?
                                                        AND poeh.pono = ?
                                                        ORDER BY poeh.posuf DESC
                                                        WITH(NOLOCK)", [$this->cono,$po_number]);

        $this->info('SX Data => Last Suffix => '.$last_suffix[0]->posuf.'<br>');

        $po_data = DB::connection('sx')->select("SELECT poeh.posuf, poel.lineno, poel.shipprod, poel.qtyord, poel.price, poeh.stagecd
                                                FROM pub.poeh
                                                LEFT JOIN pub.poel ON poel.cono = poeh.cono AND poel.pono = poeh.pono AND poel.posuf = poeh.posuf
                                                WHERE poeh.cono = ?
                                                AND poeh.pono = ?
                                                AND poeh.posuf = ?
                                                WITH(NOLOCK)", [$this->cono,$po_number,$last_suffix[0]->posuf]);

        $this->info('SX Data => PO Data => '.json_encode($po_data).'<br><br>');

        return $po_data;

    }

    private function getPOMetaDataForItem($item,$line,$qty,$po_number,$po_suffix)
    {
        $this->info('Starting item => '.$item);

        if($this->line_item_totals[$item] < 1) 
        {
            //$this->info('Skipping as receipt line item count is '.$this->line_item_totals[$item]);
            return [];
        }

        //looping thru each line item quantity from the receipt
        foreach($this->line_item_slots[$item] as $slot)
        {

            if($this->line_item_totals[$item] == $qty)
            {
                $this->info($item.' => Receipt line item count is same as SX quantity.<br>');


                if($this->check_if_last_multiple_item($item,$line))
                {
                    return ['lineno' => $line, 'qty' => $this->line_item_totals[$item]];
                }else{

                    $this->line_item_totals[$item] -= $qty;

                    return ['lineno' => $line, 'qty' => $qty];
                }

            }
        }
        
        foreach($this->line_item_slots[$item] as $slot)
        {
            $this->info($item.' => Receipt line item count ('.$this->line_item_totals[$item].') is less than SX quantity.<br>');

            if($this->line_item_totals[$item] < $qty)
            {
                
                if($this->check_if_last_multiple_item($item,$line))
                {
                    return ['lineno' => $line, 'qty' => $this->calculate_multiple_item_qty($item,$line)];
                }else{
                    if($this->check_if_multiple_to_one_in_sx($item)) return ['lineno' => $line, 'qty' => $this->calculate_sx_to_one($item)];

                    $this->line_item_totals[$item] -= $qty;

                    return ['lineno' => $line, 'qty' => number_format($this->calculate_regular_line_item_qty($item,$line,$slot['qty']), 2, '.', '')];
                }

            }

        }

        foreach($this->line_item_slots[$item] as $slot)
        {

            if($this->line_item_totals[$item] > $qty){

                $this->info($item.' => Receipt line item count ('.$this->line_item_totals[$item].') is greater than SX quantity ('.$qty.')<br>');
                
                if($this->check_if_last_multiple_item($item,$line))
                {
                    return ['lineno' => $line, 'qty' => $this->calculate_multiple_item_qty($item,$line)];
                }else{

                    if($this->check_if_multiple_to_one_in_sx($item)) return ['lineno' => $line, 'qty' => $this->calculate_sx_to_one($item)];

                    $this->line_item_totals[$item] -= $qty;

                    return ['lineno' => $line, 'qty' => $qty];
                }


            }
        }
    
    }

    private function itemEligibleForReceipt($item,$receipted_products)
    {
        foreach($receipted_products as $product)
        {
            if(strtolower($item) == strtolower($product['prod']))
            {
                return true;
            }
    
        }

        return false;
    }

    private function flag_po_in_sx($purchase_order_number,$po_suffix,$peoplevox_ref_no)
    {
        DB::connection('sx')->statement("UPDATE pub.poeh SET user11 = 'PeopleVoxRef:".$peoplevox_ref_no."' where cono = ".$this->cono." and pono = '".$purchase_order_number."' and posuf = '".$po_suffix."'");
    }

    private function checkForIneligibleProducts($products,$sx_po_line_data)
    {
        $sx_products = [];
        $in_eligible_products = [];

        foreach($this->sx_po_line_data as $sx_po_line)
        {
            $sx_products[] = strtolower($sx_po_line->shipprod);
        }


        foreach($products as $product)
        {
            if(!in_array(strtolower($product['prod']),$sx_products))
            {
                $in_eligible_products[] = strtoupper($product['prod']);
            }
        }

        return $in_eligible_products;
    }

    private function checkForItem($item, $products)
    {
        foreach($products as $key => $product)
        {
            if($product['item'] == $item) return $key;
        }

        return false;
    }

    private function check_if_last_multiple_item($item,$line_no)
    {
        if(collect($this->sx_po_line_data)->where('shipprod',$item)->count() > 1 && collect($this->sx_po_line_data)->where('shipprod',$item)->max('lineno') == $line_no) return true;
        return false;
    }

    private function check_if_multiple_to_one_in_sx($item)
    {
        if(collect($this->sx_po_line_data)->where('shipprod',$item)->count() == 1 && count($this->line_item_slots[$item]) > 1)
        {
            return true;
        } 
        return false;
    }

    private function calculate_sx_to_one($item)
    {
        $sum = 0;

        foreach ($this->line_item_slots[$item] as $key => $slot) {
            $sum += $slot['qty'];
        }

        return number_format($sum, 2, '.', '');
    }

    private function calculate_multiple_item_qty($item,$line)
    {
        $total = 0;

        foreach($this->line_item_slots[$item] as $slot)
        {
            $total += $slot['qty'];
        }

        $exhausted_slots = collect($this->sx_po_line_data)->where('shipprod',$item)->where('lineno','!=',$line); 
        
        $sum = 0;

        foreach($exhausted_slots as $slot)
        {
            $sum += $slot->qtyord;
        }

        return number_format($total - $sum, 2, '.', '');

    }

    private function calculate_regular_line_item_qty($item,$line,$count)
    {
        if(collect($this->sx_po_line_data)->where('shipprod',$item)->count() > 1 && collect($this->sx_po_line_data)->where('shipprod',$item)->max('lineno') != $line)
        {
            $qtyord = collect($this->sx_po_line_data)->where('shipprod',$item)->where('lineno',$line)->first()->qtyord;

            if($count > $qtyord) return $qtyord;

            else return $count;

        }else{
            return $count;
        }
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
