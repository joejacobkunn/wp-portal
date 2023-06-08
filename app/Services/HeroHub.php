<?php

namespace App\Services;

use App\Models\Core\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class HeroHub {

    public Account $account;

    public function __construct($account)
    {
        $this->account = $account;
    }

    private function get_token()
    {
        $response = Http::post(config('herohub.token_endpoint'), [
            'clientId' => $this->account->herohubConfig->client_id,
            'clientKey' => $this->account->herohubConfig->client_key,
            'organizationGuid' => $this->account->herohubConfig->organization_guid,
        ]);

        return $response->json('result')['value'];
    }

    public function send_shipped_notification($data)
    {
        $line_items = $this->get_line_items_for_order($data['cono'], $data['order_no'], $data['order_suffix']);


        foreach($line_items as $line_item){

            $response = Http::acceptJson()
                ->withToken($this->get_token())
                ->withBody($this->generate_shipped_product_payload_for_line_item($line_item),'application/json')
                ->post(config('herohub.hero_hub_cart_url').'/shipments/outbound');
                return $response;
    
        }
       
    }

    private function get_line_items_for_order($cono, $order_no, $order_suffix)
    {
        return DB::connection('sx')->select("SELECT l.lineno,
                                    l.shipprod,	
                                    l.qtyship,
                                    l.qtyord,
                                    p.trackerno,
                                    p.shipviaty,
                                    h.custno AS hero_hub_orderno
                                FROM
                                    PUB.oeel l
                                    LEFT JOIN PUB.oeehp p ON p.orderno = l.orderno AND p.ordersuf = l.ordersuf AND p.cono = ".$cono."
                                    LEFT JOIN PUB.oeeh h ON h.orderno = l.orderno AND h.ordersuf = l.ordersuf AND h.cono = ".$cono."
                                WHERE
                                    l.cono = ".$cono."
                                    AND l.orderno = " . $order_no . "
                            AND l.ordersuf = " . $order_suffix . " WITH(nolock)");

    }

    private function generate_shipped_product_payload_for_line_item($line_item)
    {
        $line_item = (array) $line_item;
        $hub_hero_order_id = $line_item['HERO_HUB_ORDERNO'];
        $order_line = [];
        $deliveries = [];

        if ($line_item['qtyship'] > 0) {
            $order_line = [
                'orderLineNumber' => intval($line_item['lineno']),
                'material' => preg_replace('/^EX/', '', $line_item['shipprod']),
                'qty' => number_format($line_item['qtyship'], 2),
                'deliveries' => [
                    'orderLineId' => intval($line_item['lineno']),
                    'delivery' => 1,
                    'deliveryLineNumber' => intval($line_item['lineno']),
                    'qty' => number_format($line_item['qtyship'], 1)
                ]
            ];

            $deliveries = [
                'deliveryNumber' => intval($line_item['lineno']),
                'trackings' => [
                    'trackingNumber' => $line_item['trackerno']
                ]
            ];
        }

        return json_encode([
            'HeroHubId' => $hub_hero_order_id,
            'orderLines' => $order_line,
            'deliveries' => $deliveries
        ]);
    }

}