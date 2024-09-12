<?php

namespace App\Services;

use App\Models\Core\PurchaseOrderReceipt;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PeopleVox
{
    protected $payload;

    private $cono = 80;

    public function __construct()
    {
    }

    public function sync($webhook)
    {
        $this->payload = $webhook->payload;
        $parsed = [];
        $data = str_getcsv($this->payload['GoodsInId']);


        foreach($data as $row)
        {
            if(str_contains($this->clean($row),'Reference') && ctype_alnum($this->clean($row)) && empty($parsed['people_vox_reference_number'])) $parsed['people_vox_reference_number'] = (int)filter_var($row ,FILTER_SANITIZE_NUMBER_INT);
            if(str_contains($this->clean($row),'PONumber')) $parsed['po_number'] = str_replace('PONumber', '', $this->clean($row));
            if(str_contains($this->clean($row),'Status')) $parsed['status'] = str_replace('Status', '', $this->clean($row));
            if(str_contains($row,'DeliveryDateTime')) $parsed['delivery_date'] = str_replace('DeliveryDateTime=', '', $row);
        }

        $po_number = $parsed['po_number'];

        if(is_numeric($po_number))
        {
            // $sx_po_line_data = DB::connection('sx')->select("SELECT poel.lineno, poel.shipprod, poel.qtyord FROM pub.poel
            //                                                 WHERE poel.cono = ?
            //                                                 AND poel.pono = ?
            //                                                 AND poel.posuf = ?
            //                                                 AND poel.statustype = 'a'
            //                                 WITH(NOLOCK)", [$this->cono,$po_number_split[0],$po_number_split[1]]);

            $parsed['line_items'] = $this->transformLineItems($data);

            PurchaseOrderReceipt::create([
                'po_number' => $po_number,
                'people_vox_reference_no' => $parsed['people_vox_reference_number'],
                'receipt_date' => \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $parsed['delivery_date'])->format('Y-m-d'),
                'line_items' => $parsed['line_items']
            ]);

            // $payload = $this->createSXApiPayload($parsed);

            // Mail::send([], [],function (Message $message) use($payload) {
            //     $message->to('jkrefman@wandpmanagement.com')->cc(['jkunnummyalil@wandpmanagement.com'])->subject('Webhook Response PeopleVox Receipt');
            //     $message->html('<span><strong>Received Response :</strong> <br><br>'.json_encode($this->payload).'<br><br><strong>Parsed Data</strong><br><br>'.json_encode($payload).'<br><br><strong>SX PO Data</strong><br><br>'.json_encode($sx_po_line_data).'</span>');
            // });

        }

    }

    private function transformLineItems($data)
    {
        //get array key of start of line item
        $keys = [];
        foreach($data as $key => $row)
        {
            if(str_contains($row,'Line=')) $keys['line'] = $key;
            if(str_contains($row,'LineQuantity=')) $keys['quantity'] = $key;
            if(str_contains($row,'CostPrice=')) $keys['cost'] = $key;
            if(str_contains($row,'ItemQuantity=')) $keys['item_qty'] = $key;
            if(str_contains($row,'ItemCode=')) $keys['item_codes'] = $key;
        }

        $receipted_products = [];

        for($m = $keys['item_codes']; $m < $keys['cost']; $m++)
        {
            $receipted_products[] = str_replace('ItemCode=', '', $data[$m]);
        }

        $line_items = [];
        $j=0;

        for($i=$keys['line']; $i<$keys['quantity'];$i++)
        {
            $line_no = (int)filter_var($data[$i] ,FILTER_SANITIZE_NUMBER_INT);

            // if($this->lineEligibleForReceipt($line_no,$receipted_products,$sx_po_line_data))
            // {
    
            // }

            $line_items[] = [
                'line_no' => $line_no,
                'quantity' => (int)filter_var($data[$keys['quantity'] + ($j)] ,FILTER_SANITIZE_NUMBER_INT),
                'amount' => str_replace('CostPrice=', '', $data[$keys['cost'] + ($j)]),
            ];


            $j++;
        }

        return collect($line_items)->sortBy('line_no')->toArray();

    }

    private function createSXApiPayload($data)
    {
        $line_items = [];

        foreach($data['line_items'] as $line_item)
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

        $po_number_split = explode('-',$data['po_number']);

        return [
            'request' => [
                'companyNumber' => $this->cono,
                'operatorInit' => "wpa",
                'operatorPassword' => "",
                "purchaseOrderNumber" => $po_number_split[0],
                'purchaseOrderSuffix' => $po_number_split[1],
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

    private function clean($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
     }

}
