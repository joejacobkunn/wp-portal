<?php

namespace App\Services;

use App\Models\Core\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class HeroHub
{
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

        $response = Http::acceptJson()
            ->withToken($this->get_token())
            ->post(config('herohub.hero_hub_cart_url').'/shipments/outbound', $this->generate_shipped_product_payload_for_line_items($line_items));

        return $response->body();

    }

    private function get_line_items_for_order($cono, $order_no, $order_suffix)
    {
        return DB::connection('sx')->select('SELECT l.lineno,
                                    l.shipprod,	
                                    l.qtyship,
                                    l.qtyord,
                                    p.trackerno,
                                    p.shipviaty,
                                    h.custpo AS hero_hub_orderno
                                FROM
                                    PUB.oeel l
                                    LEFT JOIN PUB.oeehp p ON p.orderno = l.orderno AND p.ordersuf = l.ordersuf AND p.orderty = "O" AND p.cono = '.$cono.'
                                    LEFT JOIN PUB.oeeh h ON h.orderno = l.orderno AND h.ordersuf = l.ordersuf AND h.cono = '.$cono.'
                                WHERE
                                    l.cono = '.$cono.'
                                    AND l.orderno = '.$order_no."
                                    AND l.shipprod <> 'EXSHOPLOCAL'
                            AND l.ordersuf = ".$order_suffix.' WITH(nolock)');

    }

    private function generate_shipped_product_payload_for_line_items($line_items)
    {

        $tracking_numbers = [];
        $unique_tracking_numbers = [];

        foreach ($line_items as $order_line_item) {
            $line_item = (array) $order_line_item;
            $hub_hero_order_id = $line_item['HERO_HUB_ORDERNO'];

            if ($line_item['qtyship'] > 0) {
                if (! in_array($line_item['trackerno'], $unique_tracking_numbers)) {
                    $tracking_numbers[] = ['trackingNumber' => $line_item['trackerno']];
                    $unique_tracking_numbers[] = $line_item['trackerno'];
                }
            }

        }

        return [
            'heroHubId' => preg_replace("/[^A-Za-z0-9 ]/", '', $hub_hero_order_id),
            'orderLines' => [],
            'deliveries' => [
                [
                    'deliveryNumber' => 1,
                    'trackings' => $tracking_numbers,
                ],
            ],
        ];
    }
}
