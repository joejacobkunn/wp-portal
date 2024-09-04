<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class PeopleVox
{
    protected $payload;

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
        }

        $parsed['line_items'] = $this->transformLineItems($data);

        $payload = $this->createSXApiPayload($parsed);

        Mail::send([], [],function (Message $message) use($payload) {
            $message->to('jkrefman@wandpmanagement.com')->cc(['jkunnummyalil@wandpmanagement.com'])->subject('Webhook Response PeopleVox Receipt');
            $message->html('<span>Received Response : <br><br>'.json_encode($this->payload).'<br><br>Parsed Data<br><br>'.json_encode($payload).'</span>');
        });
    }

    private function transformLineItems($data)
    {
        //get array key of start of line item
        $keys = [];
        foreach($data as $key => $row)
        {
            if(str_contains($row,'Line=')) $keys['line'] = $key;
            if(str_contains($row,'LineQuantity=')) $keys['quantity'] = $key;
            if(str_contains($row,'Sequence=')) $keys['sequence'] = $key;
            if(str_contains($row,'CostPrice=')) $keys['cost'] = $key;
            if(str_contains($row,'LineRequestedDeliveryDate=')) $keys['delivery'] = $key;
        }

        $line_items = [];
        $j=0;

        for($i=$keys['line']; $i<$keys['sequence'];$i++)
        {
            $line_items[] = [
                'line_no' => (int)filter_var($data[$i] ,FILTER_SANITIZE_NUMBER_INT),
                'quantity' => (int)filter_var($data[$keys['quantity'] + ($j)] ,FILTER_SANITIZE_NUMBER_INT),
                'amount' => str_replace('CostPrice=', '', $data[$keys['cost'] + ($j)]),
            ];

            $j++;
        }

        return $line_items;

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
                'companyNumber' => 80,
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

    private function clean($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
     }


}
