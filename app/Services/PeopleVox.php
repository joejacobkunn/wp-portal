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

            $parsed['line_items'] = $this->transformLineItems($data);

            PurchaseOrderReceipt::create([
                'po_number' => $po_number,
                'people_vox_reference_no' => $parsed['people_vox_reference_number'],
                'receipt_date' => \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $parsed['delivery_date'])->format('Y-m-d'),
                'line_items' => $parsed['line_items']
            ]);

            Mail::send([], [],function (Message $message) {
                $message->to('jkrefman@wandpmanagement.com')->subject('Webhook Response PeopleVox Receipt');
                $message->html('<span><strong>Received Response :</strong> <br><br>'.json_encode($this->payload));
            });

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

        $parsed_data = [];
        $j=0;

        for($i=$keys['item_qty']; $i<$keys['item_codes'];$i++)
        {
            $parsed_data['line_items'][trim(str_replace('ItemCode=', '', $data[$keys['item_codes'] + ($j)]))] = (int)filter_var($data[$keys['item_qty'] + $j] ,FILTER_SANITIZE_NUMBER_INT);
            $j++;
        }


        return $parsed_data;

    }


    private function clean($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
     }

}
