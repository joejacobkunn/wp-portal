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

    protected $signature = 'app:process-purchase-order-receipts {--mode=test} {--po=}';

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

        $this->info('Starting process in mode : '.$mode);

        if($po_number)
        {
            $this->newLine();
            $this->info(' for PO Number : '.$po_number);
        }

        $query = PurchaseOrderReceipt::query();

        $query->when(!is_null($po_number), function ($q) use($po_number) {
            return $q->where('po_number', $po_number);
        });

        $query->when(is_null($po_number), function ($q) {
            return $q->where('po_number', '>', 143113);
        });

        $purchase_orders_numbers = $query->where('is_processed',0)->pluck('po_number')->unique();

        foreach($purchase_orders_numbers as $purchase_order_number)
        {
            $purchase_orders = PurchaseOrderReceipt::where('is_processed',0)->where('po_number', $purchase_order_number)->get();

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
                    foreach($purchase_order->line_items['line_items'] as $item => $item_count)
                    {
                        //$array_key = $this->checkForItem($item, $products);
                        $this->line_item_slots[$item][] = ['qty' => $item_count, 'po_number' => $purchase_order_number];
                        $products[] = ['prod' => $item, 'qty' => $item_count, 'po_number' => $purchase_order_number];                        
                    }

                    foreach($this->line_item_slots as $item => $slots)
                    {
                        $this->line_item_totals[$item] = 0;
                        foreach($slots as $slot){
                            $this->line_item_totals[$item] += $slot['qty'];
                        }
                    }
                }
    
                foreach($purchase_orders as $purchase_order)
                {

                    $ineligible_products = $this->checkForIneligibleProducts($products,$this->sx_po_line_data);

                    $po_suffix = $this->sx_po_line_data[0]->posuf;
    
                    $stage_code = $this->sx_po_line_data[0]->stagecd;


                    if(!empty($ineligible_products))
                    {
                        $to = ['jkunnummyalil@wandpmanagement.com', 'jkrefman@wandpmanagement.com'];

                        if($mode == 'process') 
                        {
                            $to = ['miranda.beaudett@bwretail.com', 'jay.leblanc@bwretail.com', 'karl.hessell@bwretail.com','jkunnummyalil@wandpmanagement.com', 'jkrefman@wandpmanagement.com'];
                            PurchaseOrderReceipt::whereIn('id', $purchase_order_ids)->update(['is_processed' => 1]);
                        }

                        Mail::send([], [],function (Message $message) use($ineligible_products,$purchase_order,$po_suffix,$to) {
                            $message->to('jkunnummyalil@wandpmanagement.com', 'jkrefman@wandpmanagement.com')->cc(['jkunnummyalil@wandpmanagement.com', 'jkrefman@wandpmanagement.com'])->subject('Ineligible products for PO# '.$purchase_order->po_number.'-'.$po_suffix);
                            $message->html('The following items were received in Peoplevox today but could not be found on PO# '.$purchase_order->po_number.'-'.$po_suffix.', therefore they were not received in SX.<br><br>PeopleVox Ref Number : '.$purchase_order->people_vox_reference_no.'<br>Receipt Date : '.date('Y-m-d', strtotime($purchase_order->receipt_date)).'<br><br>Ineligible Items  : '.implode(" ,",$ineligible_products).'<br><br>Note: Reasons could include supersedes, substitutes, or overages (if the line item was already completed in SX)');
                        });

                        $this->info('Failed to process due to ineligble items : '.implode(" ,",$ineligible_products));

                        exit;
        
                    }

    
                    foreach($this->sx_po_line_data as $sx_po_line)
                    {
                        if($this->itemEligibleForReceipt($sx_po_line->shipprod,$products))
                        {
                            $po_meta_data = $this->getPOMetaDataForItem($sx_po_line->shipprod, $sx_po_line->lineno,$sx_po_line->qtyord,$purchase_order_number,$po_suffix);
                            
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
                if($mode == 'process') PurchaseOrderReceipt::where('po_number', $purchase_order_number)->update(['is_processed', 1]);
            }
        }

        $this->info('The job is complete');
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

        return DB::connection('sx')->select("SELECT poeh.posuf, poel.lineno, poel.shipprod, poel.qtyord, poel.price, poeh.stagecd
                                                FROM pub.poeh
                                                LEFT JOIN pub.poel ON poel.cono = poeh.cono AND poel.pono = poeh.pono AND poel.posuf = poeh.posuf
                                                WHERE poeh.cono = ?
                                                AND poeh.pono = ?
                                                AND poeh.posuf = ?
                                                WITH(NOLOCK)", [$this->cono,$po_number,$last_suffix[0]->posuf]);

    }

    private function getPOMetaDataForItem($item,$line,$qty,$po_number,$po_suffix)
    {
        Log::info($item.' '.$qty.' total '.$this->line_item_totals[$item]);

        if($this->line_item_totals[$item] < 1) return [];

        //looping thru each line item quantity from the receipt
        foreach($this->line_item_slots[$item] as $slot)
        {

            if($this->line_item_totals[$item] == $qty)
            {

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
            //peoplevox qty less than sx quantity
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
            if($item == $product['prod'])
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
            $sx_products[] = $sx_po_line->shipprod;
        }

        foreach($products as $product)
        {
            if(!in_array($product['prod'],$sx_products))
            {
                $in_eligible_products[] = $product['prod'];
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
        //Log::info($item.' line no '.$line_no.' max '.collect($this->sx_po_line_data)->where('shipprod',$item)->max('lineno'));
        if(collect($this->sx_po_line_data)->where('shipprod',$item)->count() > 1 && collect($this->sx_po_line_data)->where('shipprod',$item)->max('lineno') == $line_no) return true;
        return false;
    }

    private function check_if_multiple_to_one_in_sx($item)
    {
        if(collect($this->sx_po_line_data)->where('shipprod',$item)->count() == 1 && count($this->line_item_slots[$item]) > 1)
        {
            Log::info($item.' '.collect($this->sx_po_line_data)->where('shipprod',$item)->count().' '.count($this->line_item_slots[$item]));
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
